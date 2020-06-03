<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:49
 */

namespace esas\cmsgate\epos\protocol;


use esas\cmsgate\protocol\Amount;

class EposInvoiceGetRs extends EposRs
{
    private $invoiceId;
    private $eposServiceCode;
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
    private $status;

    /**
     * @return string
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param string $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = trim($invoiceId);
    }

    /**
     * @return mixed
     */
    public function getEposServiceCode()
    {
        return $this->eposServiceCode;
    }

    /**
     * @param mixed $eposServiceCode
     */
    public function setEposServiceCode($eposServiceCode)
    {
        $this->eposServiceCode = trim($eposServiceCode);
    }

    /**
     * @return mixed
     */
    public function getOrderNumber()
    {
        return $this->orderNumber;
    }

    /**
     * @param mixed $orderNumber
     */
    public function setOrderNumber($orderNumber)
    {
        $this->orderNumber = trim($orderNumber);
    }

    /**
     * @return mixed
     */
    public function getFullName()
    {
        return $this->fullName;
    }

    /**
     * @param mixed $fullName
     */
    public function setFullName($fullName)
    {
        $this->fullName = trim($fullName);
    }

    /**
     * @return mixed
     */
    public function getMobilePhone()
    {
        return $this->mobilePhone;
    }

    /**
     * @param mixed $mobilePhone
     */
    public function setMobilePhone($mobilePhone)
    {
        $this->mobilePhone = trim($mobilePhone);
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email)
    {
        $this->email = trim($email);
    }

    /**
     * @return mixed
     */
    public function getFullAddress()
    {
        return $this->fullAddress;
    }

    /**
     * @param mixed $fullAddress
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
     * @param Amount $amount
     */
    public function setAmount(Amount $amount)
    {
        $this->amount = $amount;
    }


    /**
     * @return mixed
     */
    public function getProducts()
    {
        return $this->products;
    }

    /**
     * @param mixed $products
     */
    public function setProducts($products)
    {
        $this->products = $products;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = trim($status);
    }

    const STATUS_DRAFT = 0;
    const STATUS_ACTIVE = 10;
    const STATUS_PAYED = 20;
    const STATUS_DELETED = 30;
    const STATUS_VERIFIED = 70;

    public function isStatusPayed()
    {
        return in_array($this->status, array(self::STATUS_PAYED, self::STATUS_VERIFIED));
    }

    public function isStatusCanceled()
    {
        return in_array($this->status, array(self::STATUS_DELETED));
    }

    public function isStatusPending()
    {
        return in_array($this->status, array(self::STATUS_ACTIVE));
    }
}