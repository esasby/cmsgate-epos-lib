<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 22.03.2018
 * Time: 14:13
 */

namespace esas\cmsgate\epos\controllers;

use esas\cmsgate\epos\view\client\CompletionPageEpos;
use esas\cmsgate\Registry;
use Exception;
use Throwable;

class ControllerEposCompletionPage extends ControllerEpos
{
    /**
     * @param $orderId
     * @return CompletionPageEpos
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

            $completionPage = $this->registry->getCompletionPage($orderWrapper, $completionPanel);
            return $completionPage;
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