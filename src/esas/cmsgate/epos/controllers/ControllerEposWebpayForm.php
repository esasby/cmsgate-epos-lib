<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 12:32
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposProtocol;
use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\protocol\EposWebPayRq;
use esas\cmsgate\epos\protocol\EposWebPayRs;
use esas\cmsgate\epos\protocol\IiiProtocol;
use esas\cmsgate\Registry;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\ViewFields;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposWebpayForm extends ControllerEpos
{
    /**
     * @param OrderWrapper $orderWrapper
     * @return EposWebPayRs
     */
    public function process($orderWrapper)
    {
        try {
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumber() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $webPayRq = new EposWebPayRq();
            $webPayRq->setInvoiceId($orderWrapper->getExtId());
            $webPayRq->setReturnUrl($this->generateSuccessReturnUrl($orderWrapper));
            $webPayRq->setCancelReturnUrl($this->generateUnsuccessReturnUrl($orderWrapper));
            $webPayRq->setButtonLabel(Registry::getRegistry()->getTranslator()->translate(ViewFields::WEBPAY_BUTTON_LABEL));
            $webPayRs = EposProtocolFactory::getProtocol()->getWebpayForm($webPayRq);
            $this->logger->info($loggerMainString . "Controller ended");
            return $webPayRs;
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        }
    }

    /**
     * При необходимости, может быть переопределен в дочерних классах
     * @param OrderWrapper $orderWrapper
     * @return string
     */
    public function generateSuccessReturnUrl(OrderWrapper $orderWrapper)
    {
        return RegistryEpos::getRegistry()->getConfigWrapper()->getUrlWebpay($orderWrapper->getOrderId()) . '&' . RequestParamsEpos::WEBPAY_STATUS . '=payed';
    }

    public function generateUnsuccessReturnUrl(OrderWrapper $orderWrapper)
    {
        return RegistryEpos::getRegistry()->getConfigWrapper()->getUrlWebpay($orderWrapper->getOrderId()) . '&' . RequestParamsEpos::WEBPAY_STATUS . '=failed';
    }

}