<?php


namespace esas\cmsgate\epos\view;


use esas\cmsgate\epos\utils\ResourceUtilsEpos;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\epos\view\client\CompletionPanelEposHRO_v1;
use esas\cmsgate\lang\Translator;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\hro\HROFactory;
use esas\cmsgate\utils\htmlbuilder\hro\HROFactoryCmsGate;
use esas\cmsgate\view\client\RequestParamsBridge;
use Exception;

class HROFactoryEpos implements HROFactory
{
    private static $instance;

    /**
     * @return HROFactoryEpos
     */
    public static function getInstance() {
        if (self::$instance == null)
            self::$instance = new HROFactoryEpos();
        return self::$instance;
    }

    /**
     * @return HROFactoryEpos
     */
    public static function fromRegistry() {

        try {
            $hroFactory = Registry::getRegistry()->getService(HROFactoryEpos::class);
        } catch (Exception $e) {
            $hroFactory = new HROFactoryEpos();
        }
        return $hroFactory;
    }

    public function createFooterSectionCompanyInfoBuilder() {
        return HROFactoryCmsGate::getInstance()->createFooterSectionCompanyInfoBuilder()
            ->addAboutItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ABOUT_FULL_NAME))
            ->addAboutItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ABOUT_REGISTRATION_DATA))
            ->addAddressItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ADDRESS_POST))
            ->addAddressItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ADDRESS_LEGAL))
            ->addContactItemEmail("support@epos.by")
            ->addContactItemPhone("+375 17 3971919")
            ->addContactItemPhone("+375 29 6353316");
    }

    public function createHeaderSectionLogoContactsBuilder() {
        $builder = HROFactoryCmsGate::getInstance()->createHeaderSectionLogoContactsBuilder();
        return $builder
            ->setLogo(ResourceUtilsEpos::getLogoEposWhite())
            ->setSmallLogo(ResourceUtilsEpos::getLogoEposWhiteVertical())
            ->addContactItemEmail("support@epos.by")
            ->addContactItemPhone("+375 17 3971919");
    }

    public function createAdminLoginPageBuilder() {
        $loginPageBuilder = HROFactoryCmsGate::getInstance()->createAdminLoginPageBuilder();
        $loginPageBuilder
            ->setLoginField(RequestParamsBridge::LOGIN_FORM_LOGIN, "Client ID")
            ->setPasswordField(RequestParamsBridge::LOGIN_FORM_PASSWORD, 'Secret')
            ->setMessage("Login to cmsgate " . Registry::getRegistry()->getPaysystemConnector()->getPaySystemConnectorDescriptor()->getPaySystemMachinaName());
        return $loginPageBuilder;
    }

    public function createCompletionPanelEposBuilder() {
        return CompletionPanelEposHRO_v1::builder();
    }
}