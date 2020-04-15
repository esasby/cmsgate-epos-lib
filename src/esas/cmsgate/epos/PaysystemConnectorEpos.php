<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 14.04.2020
 * Time: 13:45
 */

namespace esas\cmsgate\epos;


use esas\cmsgate\epos\lang\TranslatorEpos;
use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\PaysystemConnector;

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
}