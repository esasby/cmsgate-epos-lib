<?php


namespace esas\cmsgate\epos\protocol;


class EposInvoiceUpdateRq extends EposInvoiceAddRq
{
    /**
     * @var string
     */
    private $invoiceId;

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

}