<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 01.10.2018
 * Time: 11:35
 */

namespace esas\cmsgate\epos;


use esas\cmsgate\Registry;
use esas\cmsgate\epos\view\client\CompletionPanelEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;

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
        $completionPanel = new CompletionPanelEpos($orderWrapper);
        return $completionPanel;
    }

    public function getPaySystemName() {
        return "epos";
    }

    abstract function getUrlWebpay($orderWrapper);

    /**
     * @return HooksEpos
     */
    public function getHooks()
    {
        return parent::getHooks();
    }
}