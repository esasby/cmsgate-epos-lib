<?php

namespace esas\cmsgate\epos\protocol;

use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\view\admin\AdminViewFieldsEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\protocol\Amount;
use esas\cmsgate\protocol\ProtocolCurl;
use esas\cmsgate\protocol\ProtocolError;
use esas\cmsgate\protocol\RqMethod;
use esas\cmsgate\protocol\RsType;
use esas\cmsgate\utils\CMSGateException;
use Exception;
use Throwable;

/**
 * HootkiGrosh class
 */
class EposProtocol extends ProtocolCurl
{
    const EPOS_URL_REAL_UPS = 'https://api.e-pos.by/public/'; // рабочий
    const EPOS_URL_REAL_ESAS = 'https://api-epos.hgrosh.by/public/'; // рабочий
    const EPOS_URL_REAL_RRB = 'https://api.e-pos.by/rrb/public/'; // рабочий
    const EPOS_URL_TEST = 'https://api-dev.hgrosh.by/epos/public/'; // тестовый

    /**
     * @var string
     */
    private $authToken;

    /**
     * @param ConfigWrapperEpos $configWrapper
     * @throws Exception
     */
    public function __construct($authToken)
    {
        parent::__construct(
            $this->getRealUrl(RegistryEpos::getRegistry()->getConfigWrapper()->getEposProcessor()),
            self::EPOS_URL_TEST);
        $this->authToken = $authToken;
    }

    public function getRealUrl($eposProcessor)
    {
        switch ($eposProcessor) {
            case AdminViewFieldsEpos::EPOS_PROCESSOR_ESAS:
                return self::EPOS_URL_REAL_ESAS;
            case AdminViewFieldsEpos::EPOS_PROCESSOR_UPS:
                return self::EPOS_URL_REAL_UPS;
            case AdminViewFieldsEpos::EPOS_PROCESSOR_RRB:
                return self::EPOS_URL_REAL_RRB;
            default:
                $this->logger->warn("Unknown epos processor[" . $eposProcessor . "]. Using 'esas' as default");
                return self::EPOS_URL_REAL_ESAS;
        }
    }


    /**
     * Добавляет новый счет в систему
     *
     * @param EposInvoiceAddRq $invoiceAddRq
     * @return EposInvoiceAddRs
     * @throws Exception
     */
    public function invoiceAdd(EposInvoiceAddRq $invoiceAddRq)
    {
        $resp = new EposInvoiceAddRs();
        $loggerMainString = "Order[" . $invoiceAddRq->getOrderNumber() . "]: ";
        try {// формируем xml
            $this->logger->debug($loggerMainString . "addInvoice started");
            $postData = array();
            $postData['merchantInfo']['serviceId'] = RegistryEpos::getRegistry()->getConfigWrapper()->getEposServiceCode();
            $postData['merchantInfo']['retailOutlet']['code'] = RegistryEpos::getRegistry()->getConfigWrapper()->getEposRetailOutletCode();
            $postData['number'] = $invoiceAddRq->getOrderNumber();
            $postData['currency'] = $invoiceAddRq->getAmount()->getCurrencyNumcode();
            $postData['dateInAirUTC'] = gmdate("Y-m-d\TH:i:s.u");
            $postData['paymentDueTerms']['termsDay'] = $invoiceAddRq->getDueInterval();
            $postData['paymentRules']['isTariff'] = false;

            self::fillCustomerAndProductsData($postData, $invoiceAddRq);
            // запрос
            $resArray = $this->requestPost('v1/invoicing/invoice?canPayAtOnce=true', json_encode($postData), RsType::_ARRAY);
            if ($resArray == null || !is_array($resArray)) {
                throw new Exception("Wrong response!", EposRs::ERROR_RESP_FORMAT);
            }
            $resArray = $resArray[0]; //epos возвращает даже один счет массивом
            if (array_key_exists('id', $resArray)) {
                $resp->setInvoiceId($resArray['id']);
            } else {
                $resp->setResponseCode(array_key_exists('code', $resArray) ? $resArray['code'] : ProtocolError::ERROR_WRONG_MSG_FORMAT);
                $resp->setResponseMessage(array_key_exists('message', $resArray) ? $resArray['message'] : "");
            }
            $this->logger->debug($loggerMainString . "addInvoice ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "addInvoice exception", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "addInvoice exception", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }

    /**
     * Сюда вынесено заполнение полей, которые могут быть изменены в заказе
     * @param $postData
     * @param EposInvoiceAddRq $invoiceAddRq
     * @throws Exception
     */
    private static function fillCustomerAndProductsData(&$postData, EposInvoiceAddRq $invoiceAddRq) {
        $postData['billingInfo']['contact']['firstName'] = $invoiceAddRq->getFullName();
        $postData['billingInfo']['phone']['nationalNumber'] = preg_replace('/[^0-9]/', '', $invoiceAddRq->getMobilePhone());
        $postData['billingInfo']['email'] = $invoiceAddRq->getEmail();
        $postData['billingInfo']['address']['line1'] = $invoiceAddRq->getFullAddress();
        if ($invoiceAddRq->getShippingAmount() != null && $invoiceAddRq->getShippingAmount()->getValue() >= 0) { // проверка =0 важно для случая, когда в заказе удалена доставка
            $postData['shippingInfo']['amount']['value'] = $invoiceAddRq->getShippingAmount()->getValue();
        }
        // Список товаров/услуг
        if (empty($invoiceAddRq->getProducts())) {
            throw new Exception('No products in order');
        }
        $items = array();
        foreach ($invoiceAddRq->getProducts() as $pr) {
            $item['code'] = $pr->getInvId();
            $item['name'] = htmlentities($pr->getName(), ENT_XML1);
            $item['measure'] = 'pcs';
            $item['quantity'] = $pr->getCount();
            $item['unitPrice']['value'] = $pr->getUnitPrice();
            $items[] = $item;
        }
        $postData['items'] = $items;
    }

    public function invoiceUpdate(EposInvoiceUpdateRq $invoiceUpdateRq)
    {
        $resp = new EposInvoiceAddRs();
        $loggerMainString = "Order[" . $invoiceUpdateRq->getOrderNumber() . "]: ";
        try {// формируем xml
            $this->logger->debug($loggerMainString . "addInvoice started");
            //получаем полную информацию по счету
            $invoiceArray = $this->requestGet('v1/invoicing/invoice/' . $invoiceUpdateRq->getInvoiceId(), '', RsType::_ARRAY);
            if (empty($invoiceArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('code', $invoiceArray) && $invoiceArray['code'] != '0') {
                throw new Exception($invoiceArray['message'], $invoiceArray['code']);
            } elseif (!array_key_exists('id', $invoiceArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            }

            self::fillCustomerAndProductsData($invoiceArray, $invoiceUpdateRq);

            //обновляем
            $resArray = $this->requestPut('v1/invoicing/invoice/' . $invoiceUpdateRq->getInvoiceId(), json_encode($invoiceArray), RsType::_ARRAY);
            if ($resArray == null || !is_array($resArray)) {
                throw new Exception("Wrong response!", EposRs::ERROR_RESP_FORMAT);
            }
//            $resArray = $resArray[0]; //epos возвращает даже один счет массивом
            if (array_key_exists('id', $resArray)) {
                $resp->setInvoiceId($resArray['id']);
            } else {
                $resp->setResponseCode(array_key_exists('code', $resArray) ? $resArray['code'] : ProtocolError::ERROR_WRONG_MSG_FORMAT);
                $resp->setResponseMessage(array_key_exists('message', $resArray) ? $resArray['message'] : "");
            }
            $this->logger->debug($loggerMainString . "addInvoice ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "addInvoice exception", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "addInvoice exception", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }

    public function invoicePaymentDeny(EposInvoicePaymentDenyRq $invoicePaymentDenyRq)
    {
        $resp = new EposInvoicePaymentDenyRs();
        $loggerMainString = "Invoice[" . $invoicePaymentDenyRq->getInvoiceId() . "]: ";
        try {// запрос
            $this->logger->debug($loggerMainString . "invoicePaymentDeny started");
            $resArray = $this->requestPost('v1/invoicing/invoice/' . $invoicePaymentDenyRq->getInvoiceId() . "/cancel", '', RsType::_ARRAY);
            if (empty($resArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('code', $resArray) && $resArray['code'] != '0') {
                throw new Exception($resArray['message'], $resArray['code']);
            }
            $this->logger->debug($loggerMainString . "invoicePaymentDeny ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "invoicePaymentDeny exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "invoicePaymentDeny exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }

    public function invoicePaymentAllow(EposInvoicePaymentAllowRq $invoicePaymentAllowRq)
    {
        $resp = new EposInvoicePaymentDenyRs();
        $loggerMainString = "Invoice[" . $invoicePaymentAllowRq->getInvoiceId() . "]: ";
        try {// запрос
            $this->logger->debug($loggerMainString . "invoicePaymentAllow started");
            $resArray = $this->requestPost('v1/invoicing/invoice/' . $invoicePaymentAllowRq->getInvoiceId() . "/send", '', RsType::_ARRAY);
            if (empty($resArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('code', $resArray) && $resArray['code'] != '0') {
                throw new Exception($resArray['message'], $resArray['code']);
            }
            $this->logger->debug($loggerMainString . "invoicePaymentAllow ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "invoicePaymentAllow exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "invoicePaymentAllow exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }


    /**
     * Получение формы виджета для оплаты картой
     *
     * @param EposWebPayRq $webPayRq
     * @return EposWebPayRs
     */

    public function getWebpayForm(EposWebPayRq $webPayRq)
    {
        $resp = new EposWebPayRs();
        $loggerMainString = "Invoice[" . $webPayRq->getInvoiceId() . "]: ";
        try {// формируем xml
            $this->logger->debug($loggerMainString . "getWebpayForm started");
            $postData = array();
            $postData['invoice']['id'] = $webPayRq->getInvoiceId();
            $postData['successReturnUrl'] = htmlspecialchars($webPayRq->getReturnUrl());
            $postData['cancelReturnUrl'] = htmlspecialchars($webPayRq->getCancelReturnUrl());
            $postData['submitValue'] = $webPayRq->getButtonLabel();
            $postData['isTestMode'] = (RegistryEpos::getRegistry()->getConfigWrapper()->isSandbox() ? "true" : "false");
            $resStr = $this->requestPost('v1/pay/webpay', json_encode($postData), RsType::_STRING);
            $resXml = simplexml_load_string($resStr, null, LIBXML_NOCDATA);
            if ($resXml == null) {
                throw new Exception("Неверный формат ответа", EposRs::ERROR_RESP_FORMAT);
            }
//            $resp->setResponseCode($resXml->status);
            $resp->setHtmlForm($resStr);
            $this->logger->debug($loggerMainString . "getWebpayForm ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "getWebpayForm exception: ", $e);
            $resp->setResponseCode(EposRs::ERROR_DEFAULT);
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "getWebpayForm exception: ", $e);
            $resp->setResponseCode(EposRs::ERROR_DEFAULT);
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }


    /**
     * Получение данных для формирования QR-code
     *
     * @param EposQRCodeRq $QRCodeRq
     * @return EposQRCodeRs
     */

    public function getQRCode(EposQRCodeRq $QRCodeRq)
    {
        $resp = new EposQRCodeRs();
        $loggerMainString = "Invoice[" . $QRCodeRq->getInvoiceId() . "]: ";
        try {// формируем xml
            $this->logger->debug($loggerMainString . "getQRCode started");
            $resArray = $this->requestGet('v1/invoicing/invoice/' . $QRCodeRq->getInvoiceId() . '/qrcode' . ($QRCodeRq->isRequestImage() ? '?getImage=true' : ''), "", RsType::_ARRAY);
            if ($resArray == null || !is_array($resArray)) {
                throw new Exception("Wrong response!", EposRs::ERROR_RESP_FORMAT);
            }
            if (array_key_exists('result', $resArray)) {
                $resp->setAddress($resArray['result']['address'] . "?param=" . $resArray['result']['num']);
                $resp->setQrData($resArray['result']['qrData']);
                $resp->setImage($resArray['result']['image']);
            } else {
                $resp->setResponseCode(array_key_exists('code', $resArray) ? $resArray['code'] : ProtocolError::ERROR_WRONG_MSG_FORMAT);
                $resp->setResponseMessage(array_key_exists('message', $resArray) ? $resArray['message'] : "");
            }
            $this->logger->debug($loggerMainString . "getQRCode ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "getQRCode exception: ", $e);
            $resp->setResponseCode(EposRs::ERROR_DEFAULT);
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "getQRCode exception: ", $e);
            $resp->setResponseCode(EposRs::ERROR_DEFAULT);
            $resp->setResponseMessage($e->getMessage());
        }
        return $resp;
    }


    /**
     * Извлекает информацию о выставленном счете
     *
     * @param EposInvoiceGetRq $invoiceGetRq
     *
     * @param bool $returnArray - true, для случая когда метод должен вернуть не объект EposInvoiceGetRs, а массив, например при редактировании счета и отправки в PUT
     * @return EposInvoiceGetRs|array
     */
    public function invoiceGet(EposInvoiceGetRq $invoiceGetRq, $returnArray = false)
    {
        $resp = new EposInvoiceGetRs();
        $loggerMainString = "Invoice[" . $invoiceGetRq->getInvoiceId() . "]: ";
        try {// запрос
            $this->logger->debug($loggerMainString . "getInvoice started");
            $resArray = $this->requestGet('v1/invoicing/invoice/' . $invoiceGetRq->getInvoiceId(), '', RsType::_ARRAY);
            if (empty($resArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('code', $resArray) && $resArray['code'] != '0') {
                throw new Exception($resArray['message'], $resArray['code']);
            }
            $resp->setResponseCode("0");
            $resp->setParentId($resArray["parentId"]);
            $resp->setInvoiceId($resArray["id"]);
            $resp->setOrderNumber($resArray["number"]);
            $resp->setEposServiceCode($resArray['merchantInfo']['serviceId']);
            $resp->setFullName($resArray['billingInfo']['contact']['fullName']);
            $resp->setFullAddress($resArray['billingInfo']['address']['fullAddress']);
            $resp->setAmount(new Amount($resArray["totalAmount"], $resArray["currency"]));
            $resp->setEmail($resArray['billingInfo']['email']);
            $resp->setMobilePhone($resArray['billingInfo']['phone']['fullNumber']);
            $resp->setStatus($resArray["state"]);
            //todo переложить продукты
            $this->logger->debug($loggerMainString . "getInvoice ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "getInvoice exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "getInvoice exception.", $e);
            $resp->setResponseCode($e->getCode());
            $resp->setResponseMessage($e->getMessage());
        }
        if ($returnArray)
            return $resArray;
        else
            return $resp;
    }

    /**
     * Подключение GET, POST или DELETE
     *
     * @param string $path
     * @param string $data Сформированный для отправки XML
     * @param int $request
     * @param $rsType
     *
     * @return mixed
     * @throws Exception
     */
    protected function send($method, $data, $rqMethod, $rsType)
    {
        try {
            $url = $this->connectionUrl . $method;
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Authorization: Bearer ' . $this->authToken;
            $this->defaultCurlInit($url);
            curl_setopt($this->ch, CURLOPT_HEADER, false); // включение заголовков в выводе
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // не проверять сертификат узла сети
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // проверка существования общего имени в сертификате SSL
            switch ($rqMethod) {
                case RqMethod::_GET:
                    $headers[] = 'Content-Length: ' . strlen($data);
                    break;
                case RqMethod::_POST:
                    curl_setopt($this->ch, CURLOPT_POST, true);
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
                    break;
                case RqMethod::_PUT:
                    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "PUT");
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
                    break;
                case RqMethod::_DELETE:
                    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
            }
            if (isset($headers) && is_array($headers))
                curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers); // Массив устанавливаемых HTTP-заголовков
            // для безопасности прячем пароли из лога
            $logStr = $data;
            if (is_array($logStr))
                $logStr = json_encode($logStr);
            $this->logger->info('Sending ' . RqMethod::toString($rqMethod) . ' request[' . preg_replace('/(<pwd>).*(<\/pwd>)/', '$1********$2', $logStr) . "] to url[" . $url . "]");
            $response = $this->execCurlAndLog();
        } finally {
            curl_close($this->ch);
        }
        return $this->convertRs($response, $rsType);
    }

    /**
     * @return EposCallbackRq
     * @throws CMSGateException
     */
    public static function readCallback()
    {
        $callbackRq = $_SESSION["epos_callback_rq"];
        if ($callbackRq == null || !($callbackRq instanceof EposCallbackRq)) {
            $callbackData = json_decode(file_get_contents('php://input'), true);
            $callbackRq = new EposCallbackRq($callbackData["id"], $callbackData["claimId"]);
            $_SESSION["epos_callback_rq"] = $callbackRq; //сохраняем в сессии, т.к. file_get_contents('php://input') не всегда корректно читается несколько раз
        }
        CMSGateException::throwIfNull($callbackRq, "Can not read callback rq");
        return $callbackRq;
    }

}