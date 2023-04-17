<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.10.2018
 * Time: 11:27
 */

namespace esas\cmsgate\epos\view\client;


use esas\cmsgate\epos\utils\ResourceUtilsEpos;
use esas\cmsgate\lang\Translator;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;

/**
 * Class CompletionPanelEposHRO_v1 используется для формирования итоговой страницы. Основной класс
 * для темазависимого представления (HGCMS-23).
 * Разбит на множество мелких методов для возможности легкого переопрделения. Что позволяет формировать итоговоую
 * страницу в тегах и CSS-классах принятых в конкретных CMS
 * @package esas\epos\view\client
 */
class CompletionPanelEposHRO_v1 implements CompletionPanelEposHRO
{
    /**
     * Флаг, когда только один таб
     * и не нужна возможность выбора (например при renderWebpayOnly)
     * @var bool
     */
    private $onlyOneTab = false;

    protected $completionText;
    protected $instructionText;
    protected $webpayForm;
    protected $webpayStatus;
    protected $qrCode;
    /**
     * @var boolean
     */
    protected $instructionSectionEnable;
    /**
     * @var boolean
     */
    protected $qrcodeSectionEnable;
    /**
     * @var boolean
     */
    protected $webpaySectionEnable;
    protected $additionalCSSFile;

    /**
     * @param mixed $instructionText
     * @return CompletionPanelEposHRO_v1
     */
    public function setInstructionText($instructionText) {
        $this->instructionText = $instructionText;
        return $this;
    }

    /**
     * @param mixed $completionText
     * @return CompletionPanelEposHRO_v1
     */
    public function setCompletionText($completionText) {
        $this->completionText = $completionText;
        return $this;
    }

    /**
     * @param mixed $webpayForm
     * @return CompletionPanelEposHRO_v1
     */
    public function setWebpayForm($webpayForm) {
        $this->webpayForm = $webpayForm;
        return $this;
    }

    /**
     * @param mixed $webpayStatus
     * @return CompletionPanelEposHRO_v1
     */
    public function setWebpayStatus($webpayStatus) {
        $this->webpayStatus = $webpayStatus;
        return $this;
    }

    /**
     * @param mixed $qrCode
     * @return CompletionPanelEposHRO_v1
     */
    public function setQrCode($qrCode) {
        $this->qrCode = $qrCode;
        return $this;
    }

    /**
     * @param bool $instructionSectionEnable
     * @return CompletionPanelEposHRO_v1
     */
    public function setInstructionsSectionEnabled($instructionSectionEnable) {
        $this->instructionSectionEnable = $instructionSectionEnable;
        return $this;
    }

    /**
     * @param bool $qrcodeSectionEnable
     * @return CompletionPanelEposHRO_v1
     */
    public function setQRCodeSectionEnabled($qrcodeSectionEnable) {
        $this->qrcodeSectionEnable = $qrcodeSectionEnable;
        return $this;
    }

    /**
     * @param bool $webpaySectionEnable
     * @return CompletionPanelEposHRO_v1
     */
    public function setWebpaySectionEnabled($webpaySectionEnable) {
        $this->webpaySectionEnable = $webpaySectionEnable;
        return $this;
    }



    public static function builder() {
        return new CompletionPanelEposHRO_v1();
    }

    public function build() {
        $this->onlyOneTab = false;
        return
            element::content(
                element::div(
                    attribute::id("completion-text"),
                    attribute::clazz($this->getCssClass4CompletionTextDiv()),
                    element::content($this->completionText)
                ),
                element::div(
                    attribute::id("epos-completion-tabs"),
                    attribute::clazz($this->getCssClass4TabsGroup()),
                    $this->addTabs()),
                $this->addCss()
            );
    }

    public function renderWebpayOnly()
    {
        $this->onlyOneTab = true;
        $completionPanel =
            $this->elementTab(
                self::TAB_KEY_WEBPAY,
                $this->getWebpayTabLabel(),
                $this->elementWebpayTabContent()
            );
        echo $completionPanel;
    }

    public function redirectWebpay()
    {
        $this->onlyOneTab = true;
        $completionPanel = element::content(
            $this->elementTab(
                self::TAB_KEY_WEBPAY,
                $this->getWebpayTabLabel(),
                $this->elementWebpayTabContent()
            ),
            element::includeFile(dirname(__FILE__) . "/webpayAutoSubmitJs.php", ["completionPanel" => $this])

        );
        echo $completionPanel;
    }

    public function addTabs()
    {
        return array(
            $this->elementInstructionsTab(),
            $this->elementQRCodeTab(),
            $this->elementWebpayTab(),
        );
    }

    public function addCss()
    {
        return array(
            element::styleFile($this->getCoreCSSFilePath()), // CSS для аккордеона, общий для всех
            element::styleFile($this->getModuleCSSFilePath()), // CSS, специфичный для модуля
            element::styleFile($this->additionalCSSFile)
        );
    }

    /**
     * @return string
     */
    public function getInstructionsTabLabel()
    {
        return Translator::fromRegistry()->translate(ClientViewFieldsEpos::INSTRUCTIONS_TAB_LABEL);
    }

    /**
     * @return string
     */
    public function getQRCodeTabLabel()
    {
        return Translator::fromRegistry()->translate(ClientViewFieldsEpos::QRCODE_TAB_LABEL);
    }

    /**
     * @return string
     */
    public function getQRCodeDetails()
    {
        return strtr(Translator::fromRegistry()->translate(ClientViewFieldsEpos::QRCODE_DETAILS), array(
            "@qr_code" => $this->qrCode
        ));
    }

    /**
     * @return string
     */
    public function getWebpayTabLabel()
    {
        return Translator::fromRegistry()->translate(ClientViewFieldsEpos::WEBPAY_TAB_LABEL);
    }

    public function elementTab($key, $header, $body)
    {
        return
            element::div(
                attribute::id("tab-" . $key),
                attribute::clazz("tab " . $this->getCssClass4Tab()),
                $this->elementTabHeaderInput($key),
                $this->elementTabHeader($key, $header),
                $this->elementTabBody($key, $body)
            )->__toString();
    }

    public function elementTabHeader($key, $header)
    {
        return
            element::div(
                attribute::clazz("tab-header " . $this->getCssClass4TabHeader()),
                element::label(
                    attribute::forr("input-" . $key),
                    attribute::clazz($this->getCssClass4TabHeaderLabel()),
                    element::content($header)
                )
            );
    }

    public function elementTabHeaderInput($key)
    {
        return
            (!$this->isOnlyOneTabEnabled() ? element::input(
                attribute::id("input-" . $key),
                attribute::type("radio"),
                attribute::name("tabs2"),
                attribute::checked($this->isTabChecked($key))
            ) : "");
    }

    public function elementTabBody($key, $body)
    {
        return
            element::div(
                attribute::clazz("tab-body " . $this->getCssClass4TabBody()),
                element::div(
                    attribute::id($key . "-content"),
                    attribute::clazz("tab-body-content " . $this->getCssClass4TabBodyContent()),
                    element::content($body)
                )
            );
    }

    public function isTabChecked($tabKey)
    {
        if ($this->isOnlyOneTabEnabled())
            return true;
        $webpayStatusPresent = '' != $this->webpayStatus;
        switch ($tabKey) {
            case self::TAB_KEY_INSTRUCTIONS:
                return !$webpayStatusPresent;
            case self::TAB_KEY_WEBPAY:
                return $webpayStatusPresent;
            default:
                return false;
        }
    }

    /**
     * @return bool
     */
    public function isOnlyOneTabEnabled()
    {
        if ($this->onlyOneTab)
            return true;
        $enabledTabsCount = 0;
        if ($this->instructionSectionEnable)
            $enabledTabsCount++;
        if ($this->qrcodeSectionEnable)
            $enabledTabsCount++;
        if ($this->webpaySectionEnable)
            $enabledTabsCount++;
        return $enabledTabsCount == 1;
    }

    const TAB_KEY_WEBPAY = "webpay";
    const TAB_KEY_INSTRUCTIONS = "instructions";
    const TAB_KEY_QRCODE = "qrcode";

    public function elementWebpayTab()
    {
        if ($this->webpaySectionEnable) {
            return $this->elementTab(
                self::TAB_KEY_WEBPAY,
                $this->getWebpayTabLabel(),
                $this->elementWebpayTabContent());
        }
        return "";
    }

    public function elementInstructionsTab()
    {
        if ($this->instructionSectionEnable) {
            return $this->elementTab(
                self::TAB_KEY_INSTRUCTIONS,
                $this->getInstructionsTabLabel(),
                $this->instructionText);
        }
        return "";
    }

    public function elementQRCodeTab()
    {
        if ($this->qrcodeSectionEnable) {
            return $this->elementTab(
                self::TAB_KEY_QRCODE,
                $this->getQRCodeTabLabel(),
                $this->getQRCodeDetails());
        }
        return "";
    }

    public function getCoreCSSFilePath()
    {
        return dirname(__FILE__) . "/accordion.css";
    }

    public function getModuleCSSFilePath()
    {
        return "";
    }

    public function setAdditionalCSSFile($fileName)
    {
        if ("default" == $fileName)
            $this->additionalCSSFile = dirname(__FILE__) . "/completion-default.css";
        else if (!empty($fileName))
            $this->additionalCSSFile = $_SERVER['DOCUMENT_ROOT'] . $fileName;
        return $this;
    }

    const STATUS_PAYED = 'payed';
    const STATUS_FAILED = 'failed';

    public function elementWebpayTabContent()
    {
        $ret =
            element::div(
                attribute::id("webpay_details"),
                element::content(Translator::fromRegistry()->translate(ClientViewFieldsEpos::WEBPAY_DETAILS)),
                element::br());

        $ret .= $this->elementWebpayTabContentResultMsg($this->webpayStatus);

        if ("" != $this->webpayForm) {
            $ret .=
                element::div(
                    attribute::id("webpay"),
                    attribute::align("right"),
                    element::img(
                        attribute::id("webpay-ps-image"),
                        attribute::src(ResourceUtilsEpos::getPsImageUrl()),
                        attribute::alt("")
                    ),
                    element::br(),
                    element::content($this->webpayForm),
                    element::includeFile(dirname(__FILE__) . "/webpayJs.php", ["completionPanel" => $this]));
        } else {
            $ret .=
                element::div(
                    attribute::id("webpay_message_unavailable"),
                    element::content(Translator::fromRegistry()->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNAVAILABLE)));
        }
        return $ret;
    }

    public function elementWebpayTabContentResultMsg($status)
    {
        if (self::STATUS_PAYED == $status) {
            return
                element::div(
                    attribute::clazz($this->getCssClass4MsgSuccess()),
                    attribute::id("webpay_message"),
                    element::content(Translator::fromRegistry()->translate(ClientViewFieldsEpos::WEBPAY_MSG_SUCCESS)));
        } elseif (self::STATUS_FAILED == $status) {
            return
                element::div(
                    attribute::clazz($this->getCssClass4MsgUnsuccess()),
                    attribute::id("webpay_message"),
                    element::content(Translator::fromRegistry()->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNSUCCESS)));
        } else
            return "";
    }

    /**
     * @return string
     */
    public function getCssClass4Tab()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4TabHeader()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4TabHeaderLabel()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4TabBody()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4TabBodyContent()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4MsgSuccess()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4MsgUnsuccess()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4CompletionTextDiv()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4TabsGroup()
    {
        return "";
    }

    /**
     * @return string
     */
    public function getCssClass4WebpayButton()
    {
        return $this->getCssClass4Button();
    }

    /**
     * @return string
     */
    public function getCssClass4Button()
    {
        return "";
    }

    public function getCssClass4FormInput()
    {
        return "";
    }
}