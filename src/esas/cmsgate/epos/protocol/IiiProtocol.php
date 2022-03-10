<?php

namespace esas\cmsgate\epos\protocol;

use esas\cmsgate\protocol\ProtocolCurl;
use esas\cmsgate\protocol\RqMethod;
use esas\cmsgate\protocol\RsType;
use Exception;

/**
 * IiiProtocol class
 */
class IiiProtocol extends ProtocolCurl
{
    const III_URL_REAL = 'https://iii.by/connect/token'; // рабочий
    const III_URL_TEST = 'https://ids.iii.by/connect/token'; // тестовый

    /**
     * @throws Exception
     */
    public function __construct($configWrapper)
    {
        parent::__construct(self::III_URL_REAL, self::III_URL_TEST, $configWrapper);
    }


    /**
     * Аутентифицирует пользователя в системе
     *
     * @return IiiAuthRs
     */
    public function auth(IiiAuthRq $authRq = null)
    {
        $authRs = new IiiAuthRs();
        try {
            if ($authRq == null)
                $authRq = new IiiAuthRq($this->configurationWrapper->getIiiClientId(), $this->configurationWrapper->getIiiClientSecret());
            $this->logger->info("Logging in: host[" . $this->connectionUrl . "], clientId[" . $authRq->getClientId() . "]");
            if (empty($authRq->getClientId()) || empty($authRq->getClientSecret())) {
                throw new Exception("Ошибка конфигурации! Не задан clientId или clientSecret", EposRs::ERROR_CONFIG);
            }
            $postData = array();
            $postData['grant_type'] = 'client_credentials';
            $postData['scope'] = 'epos.public.invoice';
            $postData['client_id'] = $authRq->getClientId();
            $postData['client_secret'] = $authRq->getClientSecret();
            // запрос
            $res = $this->requestPost($this->connectionUrl, $postData, RsType::_ARRAY);
            if ($res == null || !is_array($res) || !array_key_exists('access_token', $res)) {
                throw new Exception("Ошибка авторизации сервисом идентификации!", EposRs::ERROR_AUTH);
            }
            $authRs->setAccessToken($res['access_token']);
            $authRs->setExpiresIn($res['expires_in']);
            $authRs->setTokenType($res['token_type']);
        } catch (Exception $e) {
            $authRs->setResponseCode($e->getCode());
            $authRs->setResponseMessage($e->getMessage());
        }
        return $authRs;
    }

    /**
     * Подключение GET, POST или DELETE
     *
     * @param $url
     * @param string $data Сформированный для отправки XML
     * @param $rqMethod
     * @param $rsType
     *
     * @return mixed
     * @throws Exception
     */
    protected function send($url, $data, $rqMethod, $rsType)
    {
        try {
            $this->defaultCurlInit($url);
            curl_setopt($this->ch, CURLOPT_HEADER, false); // включение заголовков в выводе
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // не проверять сертификат узла сети
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false); // проверка существования общего имени в сертификате SSL
            switch ($rqMethod) {
                case RqMethod::_GET:
                    break;
                case RqMethod::_POST:
                    curl_setopt($this->ch, CURLOPT_POST, true);
                    curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
                    break;
                case RqMethod::_DELETE:
                    curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                    break;
            }
            curl_setopt($this->ch, CURLOPT_HTTPHEADER, array(
                "Content-Type: application/x-www-form-urlencoded"
            ));

            $logStr = $data;
            if (is_array($logStr))
                $logStr = json_encode($logStr);
            $this->logger->info('Sending ' . RqMethod::toString($rqMethod) . ' request[' . preg_replace('/(<pwd>).*(<\/pwd>)/', '$1********$2', $logStr) . "] to url[" . $url . "]");
            $response = $this->execCurlAndLog();
        } finally {
            curl_close($this->ch);
        }
        return $this->convertRs($response, $rsType);
    }
}