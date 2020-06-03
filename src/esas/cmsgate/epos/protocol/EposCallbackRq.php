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
    private $claimId;

    /**
     * EposCallbackRq constructor.
     * @param $invoiceId
     * @param $claimId
     */
    public function __construct($invoiceId, $claimId)
    {
        $this->invoiceId = $invoiceId;
        $this->claimId = $claimId;
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

    /**
     * @return mixed
     */
    public function getClaimId()
    {
        return $this->claimId;
    }

    /**
     * @param mixed $claimId
     */
    public function setClaimId($claimId)
    {
        $this->claimId = $claimId;
    }


}