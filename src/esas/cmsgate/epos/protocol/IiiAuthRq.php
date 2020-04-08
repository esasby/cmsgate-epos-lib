<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 28.02.2018
 * Time: 12:24
 */

namespace esas\cmsgate\epos\protocol;


class IiiAuthRq
{
    private $clientId;
    private $clientSecret;

    /**
     * EposLoginRq constructor.
     * @param $username
     * @param $password
     */
    public function __construct($username, $password)
    {
        $this->clientId = $username;
        $this->clientSecret = $password;
    }


    /**
     * @return mixed
     */
    public function getClientId()
    {
        return $this->clientId;
    }

    /**
     * @param mixed $clientId
     */
    public function setClientId($clientId)
    {
        $this->clientId = trim($clientId);
    }

    /**
     * @return mixed
     */
    public function getClientSecret()
    {
        return $this->clientSecret;
    }

    /**
     * @param mixed $clientSecret
     */
    public function setClientSecret($clientSecret)
    {
        $this->clientSecret = trim($clientSecret);
    }

}