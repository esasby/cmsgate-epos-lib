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
use esas\cmsgate\epos\utils\QRUtils;
use esas\cmsgate\epos\utils\RequestParamsEpos;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\lang\Translator;
use esas\cmsgate\messenger\Messenger;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\presets\BootstrapPreset as bootstrap;
use esas\cmsgate\view\client\ClientViewFields;
use esas\cmsgate\wrappers\OrderWrapper;
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
            $completionPanel = CompletionPanelEposHROFactory::findBuilder();

            switch ($orderWrapper->getStatus()->getOrderStatus()) {
                case Registry::getRegistry()->getConfigWrapper()->getOrderStatusPayed():
                    Messenger::fromRegistry()->addSuccessMessage(
                        Registry::getRegistry()->getConfigWrapper()->cookText(
                            Translator::fromRegistry()->translate(ClientViewFields::COMPLETION_PAGE_ORDER_PAYED_ALERT), $orderWrapper));
                    return $completionPanel;
                case Registry::getRegistry()->getConfigWrapper()->getOrderStatusFailed():
                    Messenger::fromRegistry()->addErrorMessage(
                        Registry::getRegistry()->getConfigWrapper()->cookText(
                            Translator::fromRegistry()->translate(ClientViewFields::COMPLETION_PAGE_ORDER_FAILED_ALERT), $orderWrapper));
                    return $completionPanel;
                case Registry::getRegistry()->getConfigWrapper()->getOrderStatusCanceled():
                    Messenger::fromRegistry()->addWarnMessage(
                        Registry::getRegistry()->getConfigWrapper()->cookText(
                            Translator::fromRegistry()->translate(ClientViewFields::COMPLETION_PAGE_ORDER_CANCELED_ALERT), $orderWrapper));
                    return $completionPanel;
                case Registry::getRegistry()->getConfigWrapper()->getOrderStatusPending():
                    $completionPanel->setOrderCanBePayed(true);
                    break;
                default:
                    $this->logger->error($loggerMainString . 'Unknown order status[' . $orderWrapper->getStatus()->getOrderStatus() . ']');
                    Messenger::fromRegistry()->addWarnMessage(
                        Registry::getRegistry()->getConfigWrapper()->cookText(
                            Translator::fromRegistry()->translate(ClientViewFields::COMPLETION_PAGE_ORDER_UNKNOWN_STATUS_ALERT), $orderWrapper));
                    return $completionPanel;
            }

            $completionPanel
                ->setCompletionText(ConfigWrapperEpos::fromRegistry()->cookText(ConfigWrapperEpos::fromRegistry()->getCompletionText(), $orderWrapper))
                ->setInstructionsSectionEnabled(ConfigWrapperEpos::fromRegistry()->isInstructionsSectionEnabled())
                ->setQRCodeSectionEnabled(ConfigWrapperEpos::fromRegistry()->isQRCodeSectionEnabled())
                ->setWebpaySectionEnabled(ConfigWrapperEpos::fromRegistry()->isWebpaySectionEnabled())
                ->setAdditionalCSSFile(ConfigWrapperEpos::fromRegistry()->getCompletionCssFile());
            if (ConfigWrapperEpos::fromRegistry()->isInstructionsSectionEnabled())
                $completionPanel->setInstructionText(
                    ConfigWrapperEpos::fromRegistry()->cookText(Translator::fromRegistry()->translate(ClientViewFieldsEpos::INSTRUCTIONS), $orderWrapper));
            if (ConfigWrapperEpos::fromRegistry()->isWebpaySectionEnabled()) {
                $controller = new ControllerEposWebpayForm();
                $webpayResp = $controller->process($orderWrapper);
                $completionPanel->setWebpayForm($webpayResp->getHtmlForm());
                if (array_key_exists(RequestParamsEpos::WEBPAY_STATUS, $_REQUEST))
                    $completionPanel->setWebpayStatus($_REQUEST[RequestParamsEpos::WEBPAY_STATUS]);
            }
            if (ConfigWrapperEpos::fromRegistry()->isQRCodeSectionEnabled()) {
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

    /**
     * @param $completionPanel CompletionPanelEposHRO
     * @param $orderWrapper OrderWrapper
     * @return \esas\cmsgate\utils\htmlbuilder\Element
     */
    public function initPanel($completionPanel, $orderWrapper) {
        switch ($orderWrapper->getStatus()->getOrderStatus()) {
            case Registry::getRegistry()->getConfigWrapper()->getOrderStatusPayed():
                $alert = ClientViewFields::COMPLETION_PAGE_ORDER_PAYED_ALERT;
                $alertType = bootstrap::ALERT_TYPE_SUCCESS;
                break;
            case Registry::getRegistry()->getConfigWrapper()->getOrderStatusFailed():
                $alert = ClientViewFields::COMPLETION_PAGE_ORDER_FAILED_ALERT;
                $alertType = bootstrap::ALERT_TYPE_DANGER;
                break;
            case Registry::getRegistry()->getConfigWrapper()->getOrderStatusCanceled():
                $alert = ClientViewFields::COMPLETION_PAGE_ORDER_CANCELED_ALERT;
                $alertType = bootstrap::ALERT_TYPE_WARNING;
                break;
            case Registry::getRegistry()->getConfigWrapper()->getOrderStatusPending():
            default:

        }
    }
}