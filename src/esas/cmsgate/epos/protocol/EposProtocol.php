<?php

namespace esas\cmsgate\epos\protocol;

use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\protocol\Amount;
use esas\cmsgate\protocol\RsType;
use esas\cmsgate\utils\Logger;
use Exception;
use Throwable;

/**
 * HootkiGrosh class
 */
class EposProtocol
{
    private $iiiUrl;
    private $eposUrl;
    private $ch; // curl object
    // api url
    const III_URL = 'https://iii.by/connect/token/'; // рабочий
    const III_URL_TEST = 'https://dev.iii.by/connect/token/'; // тестовый

    const EPOS_URL_REAL_ESAS = 'https://api.e-pos.by/public/'; // рабочий
    const EPOS_URL_REAL_HG = 'https://api-epos.hgrosh.by/public/'; // рабочий
    const EPOS_URL_TEST = 'https://api-dev.hgrosh.by/epos/public/'; // тестовый

    /**
     * @var Logger
     */
    private $logger;

    /**
     * @var ConfigWrapperEpos
     */
    private $configWrapper;

    /**
     * @param ConfigWrapperEpos $configWrapper
     * @throws Exception
     */
    public function __construct($configWrapper)
    {
        $this->logger = Logger::getLogger(EposProtocol::class);
        $this->configWrapper = $configWrapper;
        if ($this->configWrapper->isSandbox()) {
            $this->eposUrl = self::EPOS_URL_TEST;
            $this->iiiUrl = self::III_URL_TEST;
            $this->logger->info("Sandbox mode is on");
        } else {
            $this->eposUrl = $configWrapper->isEposEsasConnector() ? self::EPOS_URL_REAL_ESAS : self::EPOS_URL_REAL_HG;
            $this->iiiUrl = self::III_URL;
        }
    }


    /**
     * Аутентифицирует пользователя в системе
     *
     * @return EposAuthRs
     */
    public function auth(EposAuthRq $authRq = null)
    {
        $authRs = new EposAuthRs();
        try {
            if ($authRq == null)
                $authRq = new EposAuthRq($this->configWrapper->getIiiClientId(), $this->configWrapper->getIiiClientSecret());
            $this->logger->info("Logging in: host[" . $this->eposUrl . "],  clientId[" . $authRq->getClientId() . "]");
            if (empty($authRq->getClientId()) || empty($authRq->getClientSecret())) {
                throw new Exception("Ошибка конфигурации! Не задан clientId или clientSecret", EposRs::ERROR_CONFIG);
            }
            $postData = array();
            $postData['grant_type'] = 'client_credentials';
            $postData['scope'] = 'epos.public.invoice';
            $postData['client_id'] = $this->configWrapper->getIiiClientId();
            $postData['client_secret'] = $this->configWrapper->getIiiClientSecret();
            // запрос
            $res = $this->requestPost($this->iiiUrl, $postData, RsType::_ARRAY);
            if ($res == null || !is_array($res) || !array_key_exists('access_token', $res)) {
                throw new Exception("Ошибка авторизации сервисом идентификации!", EposRs::ERROR_AUTH);
            }
            $authRs->setAccessToken($res['access_token']);
            $authRs->setExpiresIn($res['expires_in']);
            $authRs->setTokenType($res['token_type']);
        } catch (Exception $e) {
            $authRs->setResponseCode($e->getCode());
            $authRs->setResponseMessage($e->getMessage());
        }
        return $authRs;
    }

    /**
     * Добавляет новый счет в систему
     *
     * @param EposInvoiceAddRq $invoiceAddRq
     * @return EposInvoiceAddRs
     * @throws Exception
     */
    public function addInvoice(EposInvoiceAddRq $invoiceAddRq)
    {
        $resp = new EposInvoiceAddRs();
        $loggerMainString = "Order[" . $invoiceAddRq->getOrderNumber() . "]: ";
        try {// формируем xml
            $this->logger->debug($loggerMainString . "addInvoice started");
            $postData = array();
            $postData['merchantInfo']['serviceId'] = $this->configWrapper->getEposServiceCode();
            $postData['number'] = $invoiceAddRq->getOrderNumber();
            $postData['currency'] = $invoiceAddRq->getAmount()->getCurrency();
            $postData['paymentDueTerms']['termsDay'] = $invoiceAddRq->getDueInterval();
            $postData['billingInfo']['contact']['fullName'] = $invoiceAddRq->getFullName();
            $postData['billingInfo']['phone']['fullNumber'] = $invoiceAddRq->getMobilePhone();
            $postData['billingInfo']['email'] = $invoiceAddRq->getEmail();
            $postData['billingInfo']['address']['fullAddress'] = $invoiceAddRq->getFullAddress();
            // Список товаров/услуг
            if (empty($invoiceAddRq->getProducts())) {
                throw new Exception('No products in order');
            }
            $items = array();
            foreach ($invoiceAddRq->getProducts() as $pr) {
                $item['code'] = $pr->getInvId();
                $item['name'] = htmlentities($pr->getName(), ENT_XML1);
                $item['quantity'] = $pr->getCount();
                $item['unitPrice']['value'] = $pr->getUnitPrice();
                $items = $item;
            }
            $postData['items'] = $items;
            // запрос
            $resArray = $this->requestPost($this->eposUrl . 'v1/invoicing/invoice?canPayAtOnce=true', $postData, RsType::_ARRAY);
            if ($resArray == null || !is_array($resArray)) {
                throw new Exception("Wrong response!", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('id', $resArray)) {
                $resp->setInvoiceId($resArray['id']);
            }
            $resp->setResponseCode($resArray['code']);
            $resp->setResponseMessage($resArray['message']);
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
            $postData['isTestMode'] = $this->configWrapper->isSandbox();
            $resStr = $this->requestPost($this->eposUrl . '/v1/pay/webpay', $postData, RsType::_STRING);
            $resXml = simplexml_load_string($resStr, null, LIBXML_NOCDATA);
            if (!isset($resXml->status)) {
                throw new Exception("Неверный формат ответа", EposRs::ERROR_RESP_FORMAT);
            }
            $resp->setResponseCode($resXml->status);
            $resp->setHtmlForm($resXml->form->__toString());
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
     * Извлекает информацию о выставленном счете
     *
     * @param EposInvoiceGetRq $invoiceGetRq
     *
     * @return EposInvoiceGetRs
     */
    public function getInvoice(EposInvoiceGetRq $invoiceGetRq)
    {
        $resp = new EposInvoiceGetRs();
        $loggerMainString = "Invoice[" . $invoiceGetRq->getInvoiceId() . "]: ";
        try {// запрос
            $this->logger->debug($loggerMainString . "getInvoice started");
            $resArray = $this->requestGet($this->eposUrl . '/v1/invoicing/invoice/' . $invoiceGetRq->getInvoiceId(), '', RsType::_ARRAY);
            if (empty($resArray)) {
                throw new Exception("Wrong message format", EposRs::ERROR_RESP_FORMAT);
            } elseif (array_key_exists('code', $resArray) && $resArray['code'] != '0') {
                throw new Exception($resArray['message'], $resArray['code']);
            }
            $resp->setResponseCode($resArray['status']);
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
        return $resp;
    }

    /**
     * Подключение GET
     *
     * @param string $path
     * @param string $data
     * @param int $rsType
     * @internal param RS_TYPE $rqType
     *
     * @return mixed
     * @throws Exception
     */
    private function requestGet($url, $data = '', $rsType = RsType::_ARRAY)
    {
        return $this->connect($url, $data, 'GET', $rsType);
    }

    /**
     * Подключение POST
     *
     * @param string $path
     * @param string $data
     * @param int $rsType
     * @internal param RS_TYPE $rqType
     * @return bool
     * @throws Exception
     */
    private function requestPost($url, $data = '', $rsType = RsType::_ARRAY)
    {
        return $this->connect($url, $data, 'POST', $rsType);
    }

    /**
     * Подключение DELETE
     *
     * @param string $path
     * @param string $data
     * @param int $rsType
     * @internal param RS_TYPE $rqType
     *
     * @return mixed
     * @throws Exception
     */
    private function requestDelete($url, $data = '', $rsType = RsType::_ARRAY)
    {
        return $this->connect($url, $data, 'DELETE', $rsType);
    }

    /**
     * Подключение GET, POST или DELETE
     *
     * @param string $path
     * @param string $data Сформированный для отправки XML
     * @param string $request
     * @param $rsType
     *
     * @return mixed
     * @throws Exception
     */
    private function connect($url, $data = '', $request = 'GET', $rsType)
    {
        $headers = array('Content-Type: application/x-www-form-urlencoded', 'Content-Length: ' . strlen($data));

        try {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_URL, $url);
            curl_setopt($this->ch, CURLOPT_HEADER, false); // включение заголовков в выводе
            curl_setopt($this->ch, CURLOPT_CONNECTTIMEOUT, 30);
            curl_setopt($this->ch, CURLOPT_VERBOSE, true); // вывод доп. информации в STDERR
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // не проверять сертификат узла сети
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // проверка существования общего имени в сертификате SSL
            curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); // возврат результата вместо вывода на экран
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers); // Массив устанавливаемых HTTP-заголовков
            if ($request == 'POST') {
                curl_setopt($this->ch, CURLOPT_POST, true);
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
            }
            if ($request == 'DELETE') {
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            }
            // для безопасности прячем пароли из лога
            $this->logger->info('Sending ' . $request . ' request[' . preg_replace('/(<pwd>).*(<\/pwd>)/', '$1********$2', $data) . "] to url[" . $url . "]");
            $response = curl_exec($this->ch);
            $this->logger->info('Got response[' . $response . "]");
            if (curl_errno($this->ch)) {
                throw new Exception(curl_error($this->ch), curl_errno($this->ch));
            }
        } finally {
            curl_close($this->ch);
        }
        switch ($rsType) {
            case RsType::_STRING:
                return $response;
            case RsType::_XML:
                return simplexml_load_string($response);
            case RsType::_ARRAY:
                return $this->responseToArray($response);
            default:
                throw new Exception("Wrong rsType.");
        }

    }

    /**
     * Преобразуем XML в массив
     *
     * @return mixed
     */
    private function responseToArray($response)
    {
        $response = trim($response);
        $array = array();
        // проверим, что это xml
        if (preg_match('/^<(.*)>$/', $response)) {
            $xml = simplexml_load_string($response);
            $array = json_decode(json_encode($xml), true);
        } elseif (preg_match('/^\{(.*)\}$/', $response)) {
            $array = json_decode($response, true);
        }
        return $array;
    }

}