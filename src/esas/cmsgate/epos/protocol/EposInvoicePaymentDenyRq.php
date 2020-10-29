<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:48
 */

namespace esas\cmsgate\epos\protocol;



class EposInvoicePaymentDenyRq extends EposRq
{
    private $invoiceId;

    /**
     * EposInvoiceGetRq constructor.
     * @param $billId
     */
    public function __construct($billId)
    {
        parent::__construct();
        $this->invoiceId = $billId;
    }


    /**
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }
}
