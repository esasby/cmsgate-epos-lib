<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 11:35
 */

namespace esas\cmsgate\epos;


use esas\cmsgate\epos\view\client\CompletionPageEpos;
use esas\cmsgate\Registry;
use esas\cmsgate\epos\view\client\CompletionPanelEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\utils\CMSGateException;

/**
 * @package esas\cmsgate
 */
abstract class RegistryEpos extends Registry
{
    /**
     * @return RegistryEpos
     */
    public static function getRegistry()
    {
        return parent::getRegistry();
    }

    /**
     * @return ConfigWrapperEpos
     */
    public function getConfigWrapper()
    {
        return parent::getConfigWrapper();
    }

    public function getCompletionPanel($orderWrapper)
    {
        return new CompletionPanelEpos($orderWrapper);
    }

    /**
     * @param $orderWrapper
     * @param $completionPanel
     * @throws CMSGateException
     */
    public function getCompletionPage($orderWrapper, $completionPanel)
    {
        return new CompletionPageEpos($orderWrapper, $completionPanel);
    }

    public function getPaySystemName() {
        return "epos";
    }

    abstract function getUrlWebpay($orderWrapper);

}