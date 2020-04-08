<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 12:47
 */

namespace esas\cmsgate\epos\protocol;


class EposRs
{
    const ERROR_DEFAULT = '100';
    const ERROR_RESP_FORMAT = '101';
    const ERROR_ALFACLICK_BILL_NOT_ADDED = '102';
    const ERROR_CONFIG = '103';
    const ERROR_AUTH = '104';

    // Список ошибок
    const STATUS_ERRORS = array(
        '100' => 'Общая ошибка',
        '101' => 'Неверный ответ сервера',
        '102' => 'Ошибка выставления счета в Альфаклик',
        '103' => 'Ошибка конфигурации',
        '104' => 'Ошибка аторизации сервисом Hutkigrosh',
    );

    private $responseCode;
    private $responseMessage;

    public function __construct()
    {
        $this->responseCode = '0';
        $this->responseMessage = '';
    }

    /**
     * @return mixed
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }

    /**
     * @param mixed $responseCode
     */
    public function setResponseCode($responseCode)
    {
        $this->responseCode = trim($responseCode);
        if (array_key_exists($this->responseCode, self::STATUS_ERRORS)) {
            $this->responseMessage = self::STATUS_ERRORS[$this->responseCode];
        }
    }

    /**
     * @return mixed
     */
    public function getResponseMessage()
    {
        return $this->responseMessage;
    }

    /**
     * @param mixed $responseMessage
     */
    public function setResponseMessage($responseMessage)
    {
        if (!empty($responseMessage))
            $this->responseMessage = $responseMessage;
    }


    /**
     * Метод для упрощения проверка результат ответа
     * @return bool
     */
    public function hasError()
    {
        return $this->responseCode != '0';
    }
}