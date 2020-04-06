<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 15:38
 */

namespace esas\cmsgate\epos\protocol;


class EposInvoiceAddRs extends EposRs
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