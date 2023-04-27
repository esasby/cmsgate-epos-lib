<?php


namespace esas\cmsgate\epos\hro\admin;


use esas\cmsgate\epos\view\admin\AdminViewFieldsEpos;
use esas\cmsgate\Registry;
use esas\cmsgate\hro\HRO;
use esas\cmsgate\hro\HROTuner;
use esas\cmsgate\hro\pages\AdminLoginPageHRO;

class AdminLoginPageHROTunerEpos implements HROTuner
{
    /**
     * @param AdminLoginPageHRO $hroBuilder
     * @return HRO|void
     */
    public function tune($hroBuilder) {
        return $hroBuilder
            ->setLoginField(AdminViewFieldsEpos::LOGIN_FORM_LOGIN, "Client ID")
            ->setPasswordField(AdminViewFieldsEpos::LOGIN_FORM_PASSWORD, 'Secret')
            ->setMessage("Login to cmsgate " . Registry::getRegistry()->getPaysystemConnector()->getPaySystemConnectorDescriptor()->getPaySystemMachinaName());
    }
}