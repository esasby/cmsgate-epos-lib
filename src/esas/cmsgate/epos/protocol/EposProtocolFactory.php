<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 10.04.2020
 * Time: 13:10
 */

namespace esas\cmsgate\epos\protocol;


use Exception;

/**
 * Класс позволяет избежать повторных запросов токенов в рамках обработки одного запроса клиента
 * @package esas\cmsgate\epos\protocol
 */
class EposProtocolFactory
{
    private static $protocol;

    /**
     * @return EposProtocol
     * @throws \Exception
     */
    public static function getProtocol(){
        if (self::$protocol == null)
            self::$protocol = new EposProtocol(self::getAuthToken());
        return self::$protocol;
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public static function getAuthToken() {
        $iiiProtocol = new IiiProtocol();
        $authRs = $iiiProtocol->auth();
        if ($authRs->hasError()) {
            throw new Exception($authRs->getResponseMessage());
        }
        return $authRs->getAccessToken();
    }
}