<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\hro\HROFactoryCmsGate;
use esas\cmsgate\view\client\ClientOrderCompletionPageHRO;
use Exception;
use Throwable;

class ControllerEposCompletionPage extends ControllerEpos
{
    /**
     * @param $orderId
     * @return ClientOrderCompletionPageHRO
     * @throws Throwable
     */
    public function process($orderWrapper)
    {
        try {
            $this->checkOrderWrapper($orderWrapper);
            $loggerMainString = "Order[" . $orderWrapper->getOrderNumberOrId() . "]: ";
            $this->logger->info($loggerMainString . "Controller started");

            $controller = new ControllerEposCompletionPanel();
            $completionPanel = $controller->process($orderWrapper);
            $completionPanel = $completionPanel->__toString();

            return HROFactoryCmsGate::fromRegistry()->createClientOrderCompletionPage()
                ->setOrderWrapper($orderWrapper)
                ->setElementCompletionPanel($completionPanel);
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