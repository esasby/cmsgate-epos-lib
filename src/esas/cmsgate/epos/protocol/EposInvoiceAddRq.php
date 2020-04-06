<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:48
 */

namespace esas\cmsgate\epos\protocol;


use esas\cmsgate\protocol\Amount;

class EposInvoiceAddRq extends EposRq
{
    private $orderNumber;
    private $fullName;
    private $mobilePhone;
    private $email;
    private $fullAddress;
    /**
     * @var Amount
     */
    private $amount;
    private $products;
    private $dueInterval;


    /**
     * @return string
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param string $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = trim($orderNumber);
    }

    /**
     * @return string
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = trim($fullName);
    }

    /**
     * @return string
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param string $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = trim($mobilePhone);
    }

    /**
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail($email)
    {
        $this->email = trim($email);
    }

    /**
     * @return string
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * @param string $fullAddress
     */
    public function setFullAddress($fullAddress)
    {
        $this->fullAddress = trim($fullAddress);
    }

    /**
     * @return Amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount(Amount $amount)
    {
        if ($amount == null || $amount->getValue() <= 0)
            $this->logger->warn('Incorrect bill amount[' . $amount->getValue() . "]");
        $this->amount = $amount;
    }

    /**
     * @return OrderProduct[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param array $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    public function addProduct(OrderProduct $product)
    {
        $this->products[] = $product;
    }

    /**
     * @return mixed
     */
    public function getDueInterval()
    {
        return $this->dueInterval;
    }

    /**
     * @param mixed $dueInterval
     */
    public function setDueInterval($dueInterval)
    {
        $this->dueInterval = $dueInterval;
    }
}
