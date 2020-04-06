<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 30.09.2018
 * Time: 15:15
 */

namespace esas\cmsgate\epos\view\admin;


use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\view\admin\fields\ConfigFieldCheckbox;
use esas\cmsgate\view\admin\fields\ConfigFieldNumber;
use esas\cmsgate\view\admin\fields\ConfigFieldPassword;
use esas\cmsgate\view\admin\fields\ConfigFieldRichtext;
use esas\cmsgate\view\admin\fields\ConfigFieldStatusList;
use esas\cmsgate\view\admin\fields\ConfigFieldText;
use esas\cmsgate\view\admin\ManagedFields;
use esas\cmsgate\view\admin\validators\ValidatorEmail;
use esas\cmsgate\view\admin\validators\ValidatorImpl;
use esas\cmsgate\view\admin\validators\ValidatorInteger;
use esas\cmsgate\view\admin\validators\ValidatorNotEmpty;
use esas\cmsgate\view\admin\validators\ValidatorNumeric;

class ManagedFieldsEpos extends ManagedFields
{
        /**
     * ManagedFieldsEpos constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->registerField(
            (new ConfigFieldText(ConfigFieldsEpos::shopName()))
                ->setValidator(new ValidatorNotEmpty())
                ->setRequired(false));
        $this->registerField(
            (new ConfigFieldText(ConfigFieldsEpos::iiiClientId()))
                ->setValidator(new ValidatorNotEmpty())
                ->setRequired(true));
        $this->registerField(
            (new ConfigFieldPassword(ConfigFieldsEpos::iiiClientSecret()))
                ->setValidator(new ValidatorNotEmpty())
                ->setRequired(true));
        $this->registerField(
            (new ConfigFieldNumber(ConfigFieldsEpos::eposServiceCode()))
                ->setValidator(new ValidatorNumeric())
                ->setRequired(true));
        $this->registerField(
            (new ConfigFieldCheckbox(ConfigFieldsEpos::sandbox())));
        $this->registerField(
            (new ConfigFieldNumber(ConfigFieldsEpos::dueInterval()))
                ->setMin(1)
                ->setMax(10)
                ->setValidator(new ValidatorInteger(1, 10))
                ->setRequired(true));
        $this->registerField(new ConfigFieldStatusList(ConfigFieldsEpos::billStatusPending()));
        $this->registerField(new ConfigFieldStatusList(ConfigFieldsEpos::billStatusPayed()));
        $this->registerField(new ConfigFieldStatusList(ConfigFieldsEpos::billStatusFailed()));
        $this->registerField(new ConfigFieldStatusList(ConfigFieldsEpos::billStatusCanceled()));
        $this->registerField(
            (new ConfigFieldCheckbox(ConfigFieldsEpos::instructionsSection())));
        $this->registerField(
            (new ConfigFieldCheckbox(ConfigFieldsEpos::qrcodeSection())));
        $this->registerField(
            (new ConfigFieldCheckbox(ConfigFieldsEpos::webpaySection())));
        $this->registerField(
            (new ConfigFieldRichtext(ConfigFieldsEpos::completionText()))
                ->setRequired(true));
        $this->registerField(
            (new ConfigFieldText(ConfigFieldsEpos::completionCssFile()))
                ->setRequired(false)
                ->setValidator(new ValidatorImpl()));
        $this->registerField(
            (new ConfigFieldText(ConfigFieldsEpos::paymentMethodName()))
                ->setRequired(true)
                ->setValidator(new ValidatorNotEmpty()));
        $this->registerField(
            (new ConfigFieldRichtext(ConfigFieldsEpos::paymentMethodDetails()))
                ->setRequired(true)
                ->setValidator(new ValidatorNotEmpty()));

    }
}



