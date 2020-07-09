<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 16.02.2018
 * Time: 13:39
 */

namespace esas\cmsgate\epos\wrappers;

use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\epos\PaysystemConnectorEpos;
use esas\cmsgate\epos\view\admin\AdminViewFieldsEpos;
use esas\cmsgate\Registry;
use esas\cmsgate\wrappers\ConfigWrapper;

class ConfigWrapperEpos extends ConfigWrapper
{
    /**
     * Произольно название интернет-мазагина
     * @return string
     */
    public function getShopName()
    {
        return $this->getConfig(ConfigFieldsEpos::shopName());
    }

    /**
     * Идентификатор клиента для доступа к системе iii.by
     * @return string
     */
    public function getIiiClientId()
    {
        return $this->getConfig(ConfigFieldsEpos::iiiClientId());
    }

    /**
     * Пароль для доступа к системе iii.by
     * @return string
     */
    public function getIiiClientSecret()
    {
        return $this->getConfig(ConfigFieldsEpos::iiiClientSecret());
    }


    /**
     * Необходимо ли добавлять кнопку "Инструкиция по оплате в ЕРИП"
     * @return boolean
     */
    public function isInstructionsSectionEnabled()
    {
        return $this->checkOn(ConfigFieldsEpos::instructionsSection());
    }

    /**
     * Необходимо ли добавлять кнопку "оплатить картой"
     * @return boolean
     */
    public function isWebpaySectionEnabled()
    {
        return $this->checkOn(ConfigFieldsEpos::webpaySection());
    }

    /**
     * Уникальный код поставщика услуг в EPOS
     * @return string
     */
    public function getEposServiceProviderCode()
    {
        return $this->getConfig(ConfigFieldsEpos::eposServiceProviderCode());
    }


    /**
     * Уникальный код услуги в EPOS
     * @return string
     */
    public function getEposServiceCode()
    {
        return $this->getConfig(ConfigFieldsEpos::eposServiceCode());
    }

    /**
     * Код торговой точки
     * @return string
     */
    public function getEposRetailOutletCode()
    {
        return $this->getConfig(ConfigFieldsEpos::eposRetailOutletCode());
    }

    /**
     * ПУ под подклюен к esas или к hg
     * @return string
     */
    public function getEposProcessor()
    {
        $ret = $this->getConfig(ConfigFieldsEpos::eposProcessor());
        if ($ret == null || $ret == '') { // legacy
            return $this->checkOn(ConfigFieldsEpos::getCmsRelatedKey("epos_esas_connector")) ? AdminViewFieldsEpos::EPOS_PROCESSOR_ESAS : AdminViewFieldsEpos::EPOS_PROCESSOR_UPS; 
        } else
            return $ret;
    }

    /**
     * Необходимо ли добавлять секцию с QR-code
     * @return boolean
     */
    public function isQRCodeSectionEnabled()
    {
        return $this->checkOn(ConfigFieldsEpos::qrcodeSection());
    }

    /**
     * Итоговый текст, отображаемый клиенту после успешного выставления счета
     * @return string
     */
    public function getCompletionText()
    {
        return $this->getConfigOrDefaults(ConfigFieldsEpos::completionText());
    }

    /***
     * CSS для итогового экрана. Необходим для урощение возможности кастомизации под тему магазина
     * @return string
     */
    public function getCompletionCssFile()
    {
        return $this->getConfig(ConfigFieldsEpos::completionCssFile());
    }

    /**
     * Какой срок действия счета после его выставления (в днях)
     * @return string
     */
    public function getDueInterval()
    {
        return $this->getConfig(ConfigFieldsEpos::dueInterval());
    }


    /**
     * Метод для получения значения праметра по ключу
     * @param $config_key
     * @return bool|string
     */
    public function get($config_key)
    {
        switch ($config_key) {
            // сперва пробегаем по соответствующим методам, на случай если они были переопределены в дочернем классе
            case ConfigFieldsEpos::shopName():
                return $this->getShopName();
            case ConfigFieldsEpos::iiiClientId():
                return $this->getIiiClientId();
            case ConfigFieldsEpos::iiiClientSecret():
                return $this->getIiiClientSecret();
            case ConfigFieldsEpos::eposServiceProviderCode():
                return $this->getEposServiceProviderCode();
            case ConfigFieldsEpos::eposServiceCode():
                return $this->getEposServiceCode();
            case ConfigFieldsEpos::eposRetailOutletCode():
                return $this->getEposRetailOutletCode();
            case ConfigFieldsEpos::eposProcessor():
                return $this->getEposProcessor();
            case ConfigFieldsEpos::instructionsSection():
                return $this->isInstructionsSectionEnabled();
            case ConfigFieldsEpos::qrcodeSection():
                return $this->isQRCodeSectionEnabled();
            case ConfigFieldsEpos::webpaySection():
                return $this->isWebpaySectionEnabled();
            case ConfigFieldsEpos::completionText():
                return $this->getCompletionText();
            case ConfigFieldsEpos::completionCssFile():
                return $this->getCompletionCssFile();
            case ConfigFieldsEpos::dueInterval():
                return $this->getDueInterval();
            default:
                return parent::get($config_key);
        }
    }

    public function cookText($text, $orderWrapper)
    {
        $text = parent::cookText($text, $orderWrapper);
        $invoiceId = PaysystemConnectorEpos::getInvoiceId($orderWrapper);
        return strtr($text, array(
            "@epos_order_id" => $invoiceId,
            "@epos_invoice_id" => $invoiceId
        ));
    }


    /**
     * Нельзя делать в конструкторе
     * @param $key
     * @return bool|int|string
     */
    public function getDefaultConfig($key)
    {
        switch ($key) {
            case ConfigFieldsEpos::sandbox():
                return true;
            case ConfigFieldsEpos::dueInterval():
                return 2;
            case ConfigFieldsEpos::instructionsSection():
                return true;
            case ConfigFieldsEpos::qrcodeSection():
                return true;
            case ConfigFieldsEpos::webpaySection():
                return true;
            default:
                return Registry::getRegistry()->getTranslator()->getConfigFieldDefault($key);
        }
    }

}