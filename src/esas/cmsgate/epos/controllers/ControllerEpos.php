<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 04.07.2019
 * Time: 12:07
 */

namespace esas\cmsgate\epos\controllers;


use esas\cmsgate\controllers\Controller;
use esas\cmsgate\epos\protocol\IiiProtocol;
use esas\cmsgate\Registry;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use Exception;

abstract class ControllerEpos extends Controller
{
    /**
     * @var ConfigWrapperEpos
     */
    protected $configWrapper;

    /**
     * @var RegistryEpos
     */
    protected $registry;

    /**
     * ControllerEpos constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->registry = Registry::getRegistry();
        $this->configWrapper = Registry::getRegistry()->getConfigWrapper();
    }

    public function checkOrderWrapper(&$orderWrapper) {
        if (is_numeric($orderWrapper)) //если передан orderId
            $orderWrapper = $this->registry->getOrderWrapper($orderWrapper);
        if (empty($orderWrapper) || empty($orderWrapper->getOrderNumber())) {
            throw new Exception("Incorrect method call! orderWrapper is null or not well initialized");
        }
        return $orderWrapper;
    }
}