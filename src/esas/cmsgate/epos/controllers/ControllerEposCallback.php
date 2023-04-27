<?php

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposInvoiceGetRq;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\epos\protocol\EposProtocol;
use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 11:37
 */
class ControllerEposCallback extends ControllerEpos
{
    /**
     * @var OrderWrapper
     */
    protected $localOrderWrapper;

    /**
     * @var EposInvoiceGetRs
     */
    protected $eposInvoiceGetRs;

    /**
     * @return EposInvoiceGetRs
     */
    public function process()
    {
        try {
            $callbackRq = EposProtocol::readCallback();
            $loggerMainString = "Invoice[" . $callbackRq->getInvoiceId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            if (empty($callbackRq->getInvoiceId()))
                throw new Exception('Wrong invoiceId[' . $callbackRq->getInvoiceId() . "]");
            RegistryEpos::getRegistry()->getHooks()->onCallbackRqRead($callbackRq);
            $this->logger->info($loggerMainString . "Loading order data from EPOS service...");
            $this->eposInvoiceGetRs = EposProtocolFactory::getProtocol()->invoiceGet(new EposInvoiceGetRq($callbackRq->getInvoiceId()));
            if ($this->eposInvoiceGetRs->hasError())
                throw new Exception($this->eposInvoiceGetRs->getResponseMessage(), $this->eposInvoiceGetRs->getResponseCode());
            $this->logger->info($loggerMainString . 'Loading local order object for id[' . $this->eposInvoiceGetRs->getOrderNumber() . "]");
            $this->localOrderWrapper = RegistryEpos::getRegistry()->getOrderWrapperByOrderNumberOrId($this->eposInvoiceGetRs->getOrderNumber());
            if (empty($this->localOrderWrapper))
                throw new Exception('Can not load order info for id[' . $this->eposInvoiceGetRs->getOrderNumber() . "]");
            if (!ConfigWrapperEpos::fromRegistry()->isSandbox() // на тестовой системе это пока не работает
                && !$this->eposInvoiceGetRs->getAmount()->isEqual($this->localOrderWrapper->getAmountObj())) {
                throw new Exception("Unmapped purchaseid: localFullname[" . $this->localOrderWrapper->getFullName()
                    . "], remoteFullname[" . $this->eposInvoiceGetRs->getFullName()
                    . "], localAmount[" . $this->localOrderWrapper->getAmountObj()
                    . "], remoteAmount[" . $this->eposInvoiceGetRs->getAmount() . "]");
            }
            if ($this->eposInvoiceGetRs->isStatusPayed()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Payed]");
                RegistryEpos::getRegistry()->getHooks()->onCallbackStatusPayed($this->localOrderWrapper, $this->eposInvoiceGetRs);
            } elseif ($this->eposInvoiceGetRs->isStatusCanceled()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Canceled]");
                RegistryEpos::getRegistry()->getHooks()->onCallbackStatusCanceled($this->localOrderWrapper, $this->eposInvoiceGetRs);
            } elseif ($this->eposInvoiceGetRs->isStatusPending()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Pending]");
                RegistryEpos::getRegistry()->getHooks()->onCallbackStatusPending($this->localOrderWrapper, $this->eposInvoiceGetRs);
            } else {
                $this->logger->error($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Failed]");
                RegistryEpos::getRegistry()->getHooks()->onCallbackStatusFailed($this->localOrderWrapper, $this->eposInvoiceGetRs);
            }
            $this->logger->info($loggerMainString . "Controller ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        } finally {
            return $this->eposInvoiceGetRs;
        }
    }
}