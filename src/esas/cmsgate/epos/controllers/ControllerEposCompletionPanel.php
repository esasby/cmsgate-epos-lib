<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\utils\QRUtils;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposHRO;
use esas\cmsgate\epos\view\HROFactoryEpos;
use esas\cmsgate\lang\Translator;
use esas\cmsgate\Registry;
use Exception;
use Throwable;

class ControllerEposCompletionPanel extends ControllerEpos
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
//            $completionPanel = $this->registry->getCompletionPanel($orderWrapper);
            $completionPanel = HROFactoryEpos::fromRegistry()->createCompletionPanelEposBuilder();
            $completionPanel
                ->setCompletionText($this->configWrapper->cookText($this->configWrapper->getCompletionText(), $orderWrapper))
                ->setInstructionsSectionEnabled($this->configWrapper->isInstructionsSectionEnabled())
                ->setQRCodeSectionEnabled($this->configWrapper->isQRCodeSectionEnabled())
                ->setWebpaySectionEnabled($this->configWrapper->isWebpaySectionEnabled())
                ->setAdditionalCSSFile($this->configWrapper->getCompletionCssFile());
            if ($this->configWrapper->isInstructionsSectionEnabled())
                $completionPanel->setInstructionText(
                    $this->configWrapper->cookText(Translator::fromRegistry()->translate(ClientViewFieldsEpos::INSTRUCTIONS), $orderWrapper));
            if ($this->configWrapper->isWebpaySectionEnabled()) {
                $controller = new ControllerEposWebpayForm();
                $webpayResp = $controller->process($orderWrapper);
                $completionPanel->setWebpayForm($webpayResp->getHtmlForm());
                if (array_key_exists(RequestParamsEpos::WEBPAY_STATUS, $_REQUEST))
                    $completionPanel->setWebpayStatus($_REQUEST[RequestParamsEpos::WEBPAY_STATUS]);
            }
            if ($this->configWrapper->isQRCodeSectionEnabled()) {
                $controller = new ControllerEposQRCode();
                $qrCodeRs = $controller->process($orderWrapper);
                $completionPanel->setQrCode(QRUtils::createQRCode($qrCodeRs->getAddress(), $qrCodeRs->getQrData()));
            }
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