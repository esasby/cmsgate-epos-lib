<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\hro\client\CompletionPanelEposHRO;
use esas\cmsgate\epos\hro\client\CompletionPanelEposHROFactory;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\Registry;
use Exception;
use Throwable;

class ControllerEposCompletionPanelWebpay extends ControllerEpos
{
    /**
     * @param $orderId
     * @return CompletionPanelEposHRO
     * @throws Throwable
     */
    public function process($orderWrapper) {
        try {
            $this->checkOrderWrapper($orderWrapper);
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumberOrId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");
            $completionPanel = CompletionPanelEposHROFactory::findBuilder();
            $completionPanel
                ->setCompletionText(ConfigWrapperEpos::fromRegistry()->cookText(ConfigWrapperEpos::fromRegistry()->getCompletionText(), $orderWrapper))
                ->setQRCodeSectionEnabled(false)
                ->setInstructionsSectionEnabled(false)
                ->setAdditionalCSSFile(ConfigWrapperEpos::fromRegistry()->getCompletionCssFile());
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