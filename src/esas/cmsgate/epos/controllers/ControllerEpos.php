<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 04.07.2019
 * Time: 12:07
 */

namespace esas\cmsgate\epos\controllers;


use esas\cmsgate\controllers\Controller;
use esas\cmsgate\Registry;
use esas\cmsgate\epos\RegistryEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;

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


}