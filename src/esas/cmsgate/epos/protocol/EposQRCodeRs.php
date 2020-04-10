<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.02.2018
 * Time: 14:46
 */

namespace esas\cmsgate\epos\protocol;


class EposQRCodeRs extends EposRs
{
    private $address;
    private $qrData;
    private $image;

    /**
     * @return mixed
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * @param mixed $address
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function getQrData()
    {
        return $this->qrData;
    }

    /**
     * @param mixed $qrData
     */
    public function setQrData($qrData)
    {
        $this->qrData = $qrData;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }


}