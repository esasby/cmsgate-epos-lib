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
use esas\cmsgate\epos\lang\TranslatorEpos;
use esas\cmsgate\epos\view\admin\ManagedFieldsFactoryEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\PaysystemConnector;
use esas\cmsgate\view\admin\ManagedFieldsFactory;

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
            new VersionDescriptor("v1.10.2", "2020-06-03"),
            "EPOS (ERIP Belarus) cmsgate connector",
            "www.epos.by",
            VendorDescriptor::esas(),
            "epos"
        );
    }
}