<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\ConfigStorageCmsArray;
use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\epos\protocol\IiiProtocol;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\Registry;
use Exception;
use Throwable;

class ControllerEposLogin extends ControllerEpos
{
    /**
     * @return boolean
     * @throws Throwable
     */
    public function process($clientId, $clientSecret, $sandbox = true)
    {
        try {
            $loggerMainString = "ClientId[" . $clientId . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $configWrapper =
                new ConfigWrapperEpos(
                    new ConfigStorageCmsArray([
                        ConfigFieldsEpos::iiiClientId() => $clientId,
                        ConfigFieldsEpos::iiiClientSecret() => $clientSecret,
                        ConfigFieldsEpos::sandbox() => $sandbox,
                        ConfigFieldsEpos::debugMode() => false])
                );
            $iiiProtocol = new IiiProtocol($configWrapper);
            $resp = $iiiProtocol->auth();
            if ($resp->hasError()) {
                throw new Exception($resp->getResponseMessage(), $resp->getResponseCode());
            }
            $this->logger->info($loggerMainString . "Auth data is correct!");
            return true;
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
            throw $e;
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            Registry::getRegistry()->getMessenger()->addErrorMessage($e->getMessage());
            throw $e;
        }
    }
}