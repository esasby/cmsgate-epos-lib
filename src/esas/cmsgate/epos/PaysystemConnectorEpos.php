<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 14.04.2020
 * Time: 13:45
 */

namespace esas\cmsgate\epos;


use esas\cmsgate\descriptors\PaySystemConnectorDescriptor;
use esas\cmsgate\descriptors\VendorDescriptor;
use esas\cmsgate\descriptors\VersionDescriptor;
use esas\cmsgate\epos\controllers\ControllerEposLogin;
use esas\cmsgate\epos\lang\TranslatorEpos;
use esas\cmsgate\epos\view\admin\ManagedFieldsFactoryEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\PaysystemConnector;
use esas\cmsgate\view\admin\ManagedFieldsFactory;
use esas\cmsgate\wrappers\OrderWrapper;

class PaysystemConnectorEpos extends PaysystemConnector
{

    public function createConfigWrapper()
    {
        return new ConfigWrapperEpos();
    }

    public function createTranslator()
    {
        return new TranslatorEpos();
    }

    /**
     * @return ManagedFieldsFactory
     */
    public function createManagedFieldsFactory()
    {
        return new ManagedFieldsFactoryEpos();
    }

    public function createPaySystemConnectorDescriptor()
    {
        return new PaySystemConnectorDescriptor(
            "cmsgate-epos-lib",
            new VersionDescriptor("v2.2.5", "2024-10-18"),
            "EPOS (ERIP Belarus) cmsgate connector",
            "www.epos.by",
            VendorDescriptor::esas(),
            "epos"
        );
    }

    /**
     * @param OrderWrapper $orderWrapper
     * @return string
     */
    public static function getInvoiceId($orderWrapper) {
        $configWrapper = RegistryEpos::getRegistry()->getConfigWrapper();
        return $configWrapper->getEposServiceProviderCode() . '-' . $configWrapper->getEposServiceCode() . '-' . $orderWrapper->getOrderNumberOrId();
    }

    public function createHooks()
    {
        return new HooksEpos();
    }

    public function checkAuth($login, $password, $sandbox)
    {
        $controllre = new ControllerEposLogin();
        $controllre->process($login, $password, $sandbox);
    }
}