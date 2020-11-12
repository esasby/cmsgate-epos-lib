<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\protocol\EposProtocolFactory;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\protocol\Amount;
use esas\cmsgate\epos\protocol\EposInvoiceAddRq;
use esas\cmsgate\epos\protocol\EposInvoiceAddRs;
use esas\cmsgate\epos\protocol\OrderProduct;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposInvoiceAdd extends ControllerEpos
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
//            $invoiceAddRq->setOrderNumber($orderWrapper->getOrderNumber() . '-' . (time() - strtotime("today"))); // для тестов
            $invoiceAddRq->setOrderNumber($orderWrapper->getOrderNumberOrId());
            $invoiceAddRq->setFullName($orderWrapper->getFullName());
            $invoiceAddRq->setMobilePhone($orderWrapper->getMobilePhone());
            $invoiceAddRq->setEmail($orderWrapper->getEmail());
            $invoiceAddRq->setFullAddress($orderWrapper->getAddress());
            $invoiceAddRq->setAmount(new Amount($orderWrapper->getAmount(), $orderWrapper->getCurrency()));
            $invoiceAddRq->setShippingAmount(new Amount($orderWrapper->getShippingAmount(), $orderWrapper->getCurrency()));
            $invoiceAddRq->setDueInterval($this->configWrapper->getDueInterval());
            if (self::setOrderProducts($orderWrapper, $invoiceAddRq))
                $this->logger->warn($loggerMainString . "Total amount mismatch. Extra tax was added"); //что-то так с суммой
            $eposProtocol = EposProtocolFactory::getProtocol();
            $resp = $eposProtocol->invoiceAdd($invoiceAddRq);
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
     * @param OrderWrapper $orderWrapper
     * @param EposInvoiceAddRq $invoiceAddRq
     */
    public static function setOrderProducts($orderWrapper, $invoiceAddRq) {
        $productsTotal = 0;
        foreach ($orderWrapper->getProducts() as $cartProduct) {
            $product = new OrderProduct();
            $product->setName($cartProduct->getName());
            $product->setInvId($cartProduct->getInvId());
            $product->setCount($cartProduct->getCount());
            $product->setUnitPrice($cartProduct->getUnitPrice());
            $invoiceAddRq->addProduct($product);
            $productsTotal += intval($cartProduct->getCount()) * floatval($cartProduct->getUnitPrice());
            unset($product); //??
        }
        /**
         * если по какой-то причине, общая сумма заказа не совпадает с суммой продуктов в заказе + стоимость доставка
         * то разницу добавляем как отдельный продукт "Extra Tax"
         */
        $extTax = floatval($orderWrapper->getAmount()) - floatval($productsTotal) - floatval($orderWrapper->getShippingAmount());
        if ($extTax > 0) {
            $product = new OrderProduct();
            $product->setName("Extra tax");
            $product->setInvId("TAX");
            $product->setCount(1);
            $product->setUnitPrice($extTax);
            $invoiceAddRq->addProduct($product);
            return true; // для возможности логироания
        } elseif ($extTax < 0) { //сумма за товары и доставку выше обшей суммы заказа. для безопасности создаем товар с суммой заказа
            $product = new OrderProduct();
            $product->setName(Registry::getRegistry()->getTranslator()->translate(ClientViewFieldsEpos::UNKNOWN_PRODUCT));
            $product->setInvId("0");
            $product->setCount(1);
            $product->setUnitPrice($orderWrapper->getAmount());
            $invoiceAddRq->setShippingAmount(new Amount(0, $orderWrapper->getCurrency())); //обнуляем стоиомть доставки
            $invoiceAddRq->setProducts([$product]); //остальные продукты удаляем, т.к. EPOS считает по ним суммы
        }
        return false;
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