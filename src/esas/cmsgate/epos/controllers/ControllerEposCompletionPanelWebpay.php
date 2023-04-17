<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposHRO_v1;
use esas\cmsgate\epos\view\HROFactoryEpos;
use esas\cmsgate\Registry;
use Exception;
use Throwable;

class ControllerEposCompletionPanelWebpay extends ControllerEpos
{
    /**
     * @param $orderId
     * @return CompletionPanelEposHRO_v1
     * @throws Throwable
     */
    public function process($orderWrapper) {
        try {
            $this->checkOrderWrapper($orderWrapper);
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumberOrId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $completionPanel = HROFactoryEpos::fromRegistry()->createCompletionPanelEposBuilder();
            $completionPanel
                ->setCompletionText($this->configWrapper->cookText($this->configWrapper->getCompletionText(), $orderWrapper))
                ->setQRCodeSectionEnabled(false)
                ->setInstructionsSectionEnabled(false)
                ->setAdditionalCSSFile($this->configWrapper->getCompletionCssFile());
            $controller = new ControllerEposWebpayForm();
            $webpayResp = $controller->process($orderWrapper);
            $completionPanel->setWebpayForm($webpayResp->getHtmlForm());
            if (array_key_exists(RequestParamsEpos::WEBPAY_STATUS, $_REQUEST))
                $completionPanel->setWebpayStatus($_REQUEST[RequestParamsEpos::WEBPAY_STATUS]);
            return $completionPanel;
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