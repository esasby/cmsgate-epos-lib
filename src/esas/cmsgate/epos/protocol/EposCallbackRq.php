<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 02.06.2020
 * Time: 10:50
 */

namespace esas\cmsgate\epos\protocol;


class EposCallbackRq
{
    private $invoiceId;

    /**
     * EposCallbackRq constructor.
     * @param $invoiceId
     */
    public function __construct($invoiceId)
    {
        $this->invoiceId = $invoiceId;
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