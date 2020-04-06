<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:50
 */

namespace esas\cmsgate\epos\protocol;


class EposWebPayRq extends EposRq
{
    private $invoiceId;
    private $returnUrl;
    private $cancelReturnUrl;
    private $buttonLabel = "Pay with card";

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
    public function getReturnUrl()
    {
        return $this->returnUrl;
    }

    /**
     * @param mixed $returnUrl
     */
    public function setReturnUrl($returnUrl)
    {
        $this->returnUrl = $returnUrl;
    }

    /**
     * @return mixed
     */
    public function getCancelReturnUrl()
    {
        return $this->cancelReturnUrl;
    }

    /**
     * @param mixed $cancelReturnUrl
     */
    public function setCancelReturnUrl($cancelReturnUrl)
    {
        $this->cancelReturnUrl = $cancelReturnUrl;
    }

    /**
     * @return mixed
     */
    public function getButtonLabel()
    {
        return $this->buttonLabel;
    }

    /**
     * @param mixed $buttonLabel
     */
    public function setButtonLabel($buttonLabel)
    {
        $this->buttonLabel = $buttonLabel;
    }


}