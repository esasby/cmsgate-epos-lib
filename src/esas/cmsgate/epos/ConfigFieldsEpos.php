<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.08.2018
 * Time: 12:21
 */

namespace esas\cmsgate\epos;


use esas\cmsgate\ConfigFields;

class ConfigFieldsEpos extends ConfigFields
{
    public static function shopName()
    {
        return self::getCmsRelatedKey("shop_name");
    }

    public static function iiiClientId()
    {
        return self::getCmsRelatedKey("iii_client_id");
    }

    public static function iiiClientSecret()
    {
        return self::getCmsRelatedKey("iii_client_secret");
    }

    public static function eposServiceProviderCode()
    {
        return self::getCmsRelatedKey("epos_service_provider_code");
    }

    public static function eposServiceCode()
    {
        return self::getCmsRelatedKey("epos_service_code");
    }

    public static function eposRetailOutletCode()
    {
        return self::getCmsRelatedKey("epos_retail_outlet_code");
    }

    public static function eposProcessor()
    {
        return self::getCmsRelatedKey("epos_processor");
    }

    public static function instructionsSection()
    {
        return self::getCmsRelatedKey("instructions_section");
    }

    public static function qrcodeSection()
    {
        return self::getCmsRelatedKey("qrcode_section");
    }

    public static function webpaySection()
    {
        return self::getCmsRelatedKey("webpay_section");
    }

    public static function completionText()
    {
        return self::getCmsRelatedKey("completion_text");
    }

    public static function completionCssFile()
    {
        return self::getCmsRelatedKey("completion_css_file");
    }
    
    public static function dueInterval()
    {
        return self::getCmsRelatedKey("due_interval");
    }

    /**
     * Настройки для отдельного способа оплаты webpay (клиенту более очевидна, что оплата по карте)
     * @return mixed
     */
    public static function paymentMethodNameWebpay()
    {
        return self::getCmsRelatedKey("payment_method_name_webpay");
    }

    public static function paymentMethodDetailsWebpay()
    {
        return self::getCmsRelatedKey("payment_method_details_webpay");
    }
}