<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\protocol\IiiProtocol;
use esas\cmsgate\protocol\Amount;
use esas\cmsgate\epos\protocol\EposInvoiceAddRq;
use esas\cmsgate\epos\protocol\EposInvoiceAddRs;
use esas\cmsgate\epos\protocol\OrderProduct;
use esas\cmsgate\epos\protocol\EposProtocol;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposAddInvoice extends ControllerEpos
{
    /**
     * @param OrderWrapper $orderWrapper
     * @return EposInvoiceAddRs
     * @throws Throwable
     */
    public function process($orderWrapper)
    {
        try {
            if (empty($orderWrapper)) {
                throw new Exception("Incorrect method call! orderWrapper is null");
            }
            if (!empty($orderWrapper->getExtId())) {
                throw new Exception("Order is already processed");
            }
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumber() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $invoiceAddRq = new EposInvoiceAddRq();
            $invoiceAddRq->setOrderNumber($orderWrapper->getOrderNumber() . '-' . (time() - strtotime("today")));
            $invoiceAddRq->setFullName($orderWrapper->getFullName());
            $invoiceAddRq->setMobilePhone($orderWrapper->getMobilePhone());
            $invoiceAddRq->setEmail($orderWrapper->getEmail());
            $invoiceAddRq->setFullAddress($orderWrapper->getAddress());
            $invoiceAddRq->setAmount(new Amount($orderWrapper->getAmount(), $orderWrapper->getCurrency()));
            $invoiceAddRq->setDueInterval($this->configWrapper->getDueInterval());
            foreach ($orderWrapper->getProducts() as $cartProduct) {
                $product = new OrderProduct();
                $product->setName($cartProduct->getName());
                $product->setInvId($cartProduct->getInvId());
                $product->setCount($cartProduct->getCount());
                $product->setUnitPrice($cartProduct->getUnitPrice());
                $invoiceAddRq->addProduct($product);
                unset($product); //??
            }
            $eposProtocol = EposProtocolFactory::getProtocol();
            $resp = $eposProtocol->addInvoice($invoiceAddRq);
            if ($resp->hasError()) {
                $this->logger->error($loggerMainString . "Invoice was not added. Setting status[" . $this->configWrapper->getBillStatusFailed() . "]...");
                $this->onFailed($orderWrapper, $resp);
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            } else {
                $this->logger->info($loggerMainString . "Bill[" . $resp->getInvoiceId() . "] was successfully added. Updating status[" . $this->configWrapper->getBillStatusPending() . "]...");
                $this->onSuccess($orderWrapper, $resp);
            }
            return $resp;
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        }
    }

    /**
     * Изменяет статус заказа при успешном высталении счета
     * Вынесено в отдельный метод, для возможности owerrid-а
     * (например, кроме статуса заказа надо еще обновить статус транзакции)
     * @param OrderWrapper $orderWrapper
     * @param EposInvoiceAddRs $resp
     * @throws Throwable
     */
    public function onSuccess(OrderWrapper $orderWrapper, EposInvoiceAddRs $resp)
    {
        $orderWrapper->saveExtId($resp->getInvoiceId());
        $orderWrapper->updateStatus($this->configWrapper->getBillStatusPending());
    }

    /**
     * @param OrderWrapper $orderWrapper
     * @param EposInvoiceAddRs $resp
     * @throws Throwable
     */
    public function onFailed(OrderWrapper $orderWrapper, EposInvoiceAddRs $resp)
    {
        $orderWrapper->updateStatus($this->configWrapper->getBillStatusFailed());
    }
}