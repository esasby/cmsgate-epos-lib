<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:50
 */

namespace esas\cmsgate\epos\protocol;


class EposQRCodeRq extends EposRq
{
    private $invoiceId;
    /**
     * @var boolean
     */
    private $requestImage;

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
     * @return bool
     */
    public function isRequestImage()
    {
        return $this->requestImage;
    }

    /**
     * @param bool $requestImage
     */
    public function setRequestImage($requestImage)
    {
        $this->requestImage = $requestImage;
    }


}