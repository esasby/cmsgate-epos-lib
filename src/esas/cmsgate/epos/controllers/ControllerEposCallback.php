<?php

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposInvoiceGetRq;
use esas\cmsgate\epos\protocol\EposInvoiceGetRs;
use esas\cmsgate\epos\protocol\EposProtocol;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\Registry;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\utils\StringUtils;
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
     * @param $invoiceId
     * @throws Exception
     */
    public function process()
    {
        try {
            $callbackData = json_decode(file_get_contents('php://input'), true);
            $invoiceId = $callbackData["id"];
            $loggerMainString = "Invoice[" . $invoiceId . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            if (empty($invoiceId))
                throw new Exception('Wrong invoiceId[' . $invoiceId . "]");
            $this->logger->info($loggerMainString . "Loading order data from EPOS service...");
            $hg = new EposProtocol($this->configWrapper);
            $resp = $hg->auth();
            if ($resp->hasError()) {
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            }
            $this->eposInvoiceGetRs = $hg->getInvoice(new EposInvoiceGetRq($invoiceId));
            if ($this->eposInvoiceGetRs->hasError())
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            $this->logger->info($loggerMainString . 'Loading local order object for id[' . $this->eposInvoiceGetRs->getOrderNumber() . "]");
            $this->localOrderWrapper = RegistryEpos::getRegistry()->getOrderWrapperByExtId($invoiceId);
            if (empty($this->localOrderWrapper))
                throw new Exception('Can not load order info for id[' . $this->eposInvoiceGetRs->getOrderNumber() . "]");
            if (!$this->configWrapper->isSandbox() // на тестовой системе это пока не работает
                && (!StringUtils::compare($this->eposInvoiceGetRs->getFullName(), $this->localOrderWrapper->getFullName())
                    || !$this->eposInvoiceGetRs->getAmount()->isEqual($this->localOrderWrapper->getAmountObj()))) {
                throw new Exception("Unmapped purchaseid: localFullname[" . $this->localOrderWrapper->getFullName()
                    . "], remoteFullname[" . $this->eposInvoiceGetRs->getFullName()
                    . "], localAmount[" . $this->localOrderWrapper->getAmountObj()
                    . "], remoteAmount[" . $this->eposInvoiceGetRs->getAmount() . "]");
            }
            if ($this->eposInvoiceGetRs->isStatusPayed()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Payed]");
                $this->onStatusPayed();
            } elseif ($this->eposInvoiceGetRs->isStatusCanceled()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Canceled]");
                $this->onStatusCanceled();
            } elseif ($this->eposInvoiceGetRs->isStatusPending()) {
                $this->logger->info($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Pending]");
                $this->onStatusPending();
            } else {
                $this->logger->error($loggerMainString . "Remote order status[" . $this->eposInvoiceGetRs->getStatus() . "] is mapped as [Failed]");
                $this->onStatusFailed();
            }
            $this->logger->info($loggerMainString . "Controller ended");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
        }
    }

    /**
     * @param $status
     * @throws Throwable
     */
    public function updateStatus($status)
    {
        if (isset($status) && $this->localOrderWrapper->getStatus() != $status) {
            $this->logger->info("Setting status[" . $status . "] for order[" . $this->eposInvoiceGetRs->getOrderNumber() . "]...");
            $this->localOrderWrapper->updateStatus($status);
        }
    }

    /**
     * @throws Throwable
     */
    public function onStatusPayed()
    {
        $this->updateStatus($this->configWrapper->getBillStatusPayed());
    }

    /**
     * @throws Throwable
     */
    public function onStatusCanceled()
    {
        $this->updateStatus($this->configWrapper->getBillStatusCanceled());
    }

    /**
     * @throws Throwable
     */
    public function onStatusPending()
    {
        $this->updateStatus($this->configWrapper->getBillStatusPending());
    }

    /**
     * @throws Throwable
     */
    public function onStatusFailed()
    {
        $this->updateStatus($this->configWrapper->getBillStatusFailed());
    }
}