<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposInvoicePaymentAllowRq;
use esas\cmsgate\epos\protocol\EposInvoicePaymentDenyRq;
use esas\cmsgate\epos\protocol\EposInvoiceUpdateRq;
use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\protocol\OrderProduct;
use esas\cmsgate\protocol\Amount;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposInvoiceUpdate extends ControllerEpos
{
    /**
     * @param OrderWrapper $orderWrapper
     * @throws Throwable
     */
    public function process($orderWrapper)
    {
        try {
            if (empty($orderWrapper)) {
                throw new Exception("Incorrect method call! orderWrapper is null");
            }
            if (empty($orderWrapper->getExtId())) {
                return; // Order was not added
            }
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumber() . "] Invoice[" . $orderWrapper->getExtId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");

            $eposProtocol = EposProtocolFactory::getProtocol();

            //<<<<<<<<<<<<<<<<<<<<<<< запрещаем прием оплаты по счету
            $resp = $eposProtocol->invoicePaymentDeny(new EposInvoicePaymentDenyRq($orderWrapper->getExtId()));
            if ($resp->hasError()) {
                $this->logger->error($loggerMainString . "Invoice can not be updated, because payment can't be denied.");
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            }
            $this->logger->info($loggerMainString . "Payment was denied successfully");

            //<<<<<<<<<<<<<<<<<<<<<<< обновляем информацию по счету
            $invoiceUpdateRq = new EposInvoiceUpdateRq();
            $invoiceUpdateRq->setOrderNumber($orderWrapper->getOrderNumber());
            $invoiceUpdateRq->setFullName($orderWrapper->getFullName());
            $invoiceUpdateRq->setMobilePhone($orderWrapper->getMobilePhone());
            $invoiceUpdateRq->setEmail($orderWrapper->getEmail());
            $invoiceUpdateRq->setFullAddress($orderWrapper->getAddress());
            $invoiceUpdateRq->setAmount(new Amount($orderWrapper->getAmount(), $orderWrapper->getCurrency()));
            foreach ($orderWrapper->getProducts() as $cartProduct) {
                $product = new OrderProduct();
                $product->setName($cartProduct->getName());
                $product->setInvId($cartProduct->getInvId());
                $product->setCount($cartProduct->getCount());
                $product->setUnitPrice($cartProduct->getUnitPrice());
                $invoiceUpdateRq->addProduct($product);
                unset($product); //??
            }
            $resp = EposProtocolFactory::getProtocol()->invoiceUpdate($invoiceUpdateRq);
            if ($resp->hasError()) {
                $this->logger->error($loggerMainString . "Invoice can not be updated.");
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            }
            $this->logger->info($loggerMainString . "invoice was updated successfully");

            //<<<<<<<<<<<<<<<<<<<<<<< опять разрешаем прием оплаты по счету
            $resp = $eposProtocol->invoicePaymentAllow(new EposInvoicePaymentAllowRq($orderWrapper->getExtId()));
            if ($resp->hasError()) {
                $this->logger->error($loggerMainString . "Invoice can not be updated, because payment can't be denied.");
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            }
            $this->logger->info($loggerMainString . "Payment was allowed successfully");
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        }
    }
}