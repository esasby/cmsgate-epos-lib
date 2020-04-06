<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\CompletionPanel;
use esas\cmsgate\wrappers\OrderWrapper;
use Exception;
use Throwable;

class ControllerEposCompletionPage extends ControllerEpos
{
    /**
     * @param $orderId
     * @return CompletionPanel
     * @throws Throwable
     */
    public function process($orderId)
    {
        try {
            $orderWrapper = $this->registry->getOrderWrapper($orderId);
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumber() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $completionPanel = $this->registry->getCompletionPanel($orderWrapper);
            if ($this->configWrapper->isWebpaySectionEnabled()) {
                $controller = new ControllerEposWebpayForm();
                $webpayResp = $controller->process($orderWrapper);
                $completionPanel->setWebpayForm($webpayResp->getHtmlForm());
                if (array_key_exists(RequestParamsEpos::WEBPAY_STATUS, $_REQUEST))
                    $completionPanel->setWebpayStatus($_REQUEST[RequestParamsEpos::WEBPAY_STATUS]);
            }
            return $completionPanel;
        } catch (Throwable $e) {
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        } catch (Exception $e) { // для совместимости с php 5
            $this->logger->error($loggerMainString . "Controller exception! ", $e);
            throw $e;
        }
    }
}