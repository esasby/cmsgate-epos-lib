<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 28.02.2018
 * Time: 12:27
 */

namespace esas\cmsgate\epos\protocol;


class IiiAuthRs extends EposRs
{
    private $accessToken;
    private $expiresIn;
    private $tokenType;

    /**
     * @return mixed
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param mixed $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return mixed
     */
    public function getExpiresIn()
    {
        return $this->expiresIn;
    }

    /**
     * @param mixed $expiresIn
     */
    public function setExpiresIn($expiresIn)
    {
        $this->expiresIn = $expiresIn;
    }

    /**
     * @return mixed
     */
    public function getTokenType()
    {
        return $this->tokenType;
    }

    /**
     * @param mixed $tokenType
     */
    public function setTokenType($tokenType)
    {
        $this->tokenType = $tokenType;
    }


}