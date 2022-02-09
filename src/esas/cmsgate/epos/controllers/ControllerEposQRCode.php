<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 12:32
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\protocol\EposQRCodeRq;
use esas\cmsgate\epos\protocol\EposQRCodeRs;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposQRCode extends ControllerEpos
{
    /**
     * @param OrderWrapper $orderWrapper
     * @return EposQRCodeRs
     */
    public function process($orderWrapper)
    {
        try {
            $this->checkOrderWrapper($orderWrapper);
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumberOrId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $qrCodeRq = new EposQRCodeRq();
            $qrCodeRq->setInvoiceId($orderWrapper->getExtId());
            $qrCodeRq->setRequestImage(false);
            $qrCodeRs = EposProtocolFactory::getProtocol()->getQRCode($qrCodeRq);
            $this->logger->info($loggerMainString . "Controller ended");
            return $qrCodeRs;
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
        }
    }

}