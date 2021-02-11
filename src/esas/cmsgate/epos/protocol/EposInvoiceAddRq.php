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
    private $mobilePhone;
    private $firstName = '';
    private $lastName = '';
    private $middleName = '';
    private $email;
    private $fullAddress;
    /**
     * @var Amount
     */
    private $amount;
    /**
     * @var Amount
     */
    private $shippingAmount;
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
     * @param string $fullName
     */
    public function setFullName($fullName)
    {
        $parts = preg_split('/\s+/', $fullName);
        if (sizeof($parts) >= 3)
            $this->middleName = $parts[2];
        if (sizeof($parts) >= 2)
            $this->firstName = $parts[1];
        if (sizeof($parts) >= 1)
            $this->lastName = $parts[0];
    }

    /**
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param mixed $firstName
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;
    }

    /**
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param mixed $lastName
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;
    }

    /**
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     * @param mixed $middleName
     */
    public function setMiddleName($middleName)
    {
        $this->middleName = $middleName;
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
     * @return Amount
     */
    public function getShippingAmount()
    {
        return $this->shippingAmount;
    }

    /**
     * @param Amount $shippingAmount
     */
    public function setShippingAmount($shippingAmount)
    {
        if ($shippingAmount == null || $shippingAmount->getValue() <= 0)
            $this->logger->warn('Incorrect shipping  amount[' . $shippingAmount->getValue() . "]");
        $this->shippingAmount = $shippingAmount;
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
