<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 10.10.2018
 * Time: 11:27
 */

namespace esas\cmsgate\epos\view\client;


use esas\cmsgate\epos\wrappers\ConfigWrapperEpos;
use esas\cmsgate\lang\Translator;
use esas\cmsgate\Registry;
use esas\cmsgate\utils\htmlbuilder\Attributes as attribute;
use esas\cmsgate\utils\htmlbuilder\Elements as element;
use esas\cmsgate\utils\Logger;
use esas\cmsgate\epos\utils\QRUtils;
use esas\cmsgate\utils\ResourceUtils;
use esas\cmsgate\wrappers\OrderWrapper;

/**
 * Class CompletionPanelEpos используется для формирования итоговой страницы. Основной класс
 * для темазависимого представления (HGCMS-23).
 * Разбит на множество мелких методов для возможности легкого переопрделения. Что позволяет формировать итоговоую
 * страницу в тегах и CSS-классах принятых в конкретных CMS
 * @package esas\epos\view\client
 */
class CompletionPanelEpos
{
    /**
     * @var Logger
     */
    protected $logger;
    /**
     * @var ConfigWrapperEpos
     */
    private $configWrapper;

    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var OrderWrapper
     */
    private $orderWrapper;

    private $webpayForm;
    private $webpayStatus;

    private $qrCode;

    /**
     * Флаг, когда только один таб
     * и не нужна возможность выбора (например при renderWebpayOnly)
     * @var bool
     */
    private $onlyOneTab = false;

    /**
     * ViewData constructor.
     * @param OrderWrapper $orderWrapper
     */
    public function __construct($orderWrapper)
    {
        $this->logger = Logger::getLogger(get_class($this));
        $this->configWrapper = Registry::getRegistry()->getConfigWrapper();
        $this->translator = Registry::getRegistry()->getTranslator();
        $this->orderWrapper = $orderWrapper;
    }

    public function render()
    {
        $this->onlyOneTab = false;
        $completionPanel = element::content(
            element::div(
                attribute::id("completion-text"),
                attribute::clazz($this->getCssClass4CompletionTextDiv()),
                element::content($this->getCompletionText())
            ),
            element::div(
                attribute::id("epos-completion-tabs"),
                attribute::clazz($this->getCssClass4TabsGroup()),
                $this->addTabs()),
            $this->addCss()
        );
        echo $completionPanel;
    }

    public function renderWebpayOnly()
    {
        $this->onlyOneTab = true;
        $completionPanel =
            $this->elementTab(
                self::TAB_KEY_WEBPAY,
                $this->getWebpayTabLabel(),
                $this->elementWebpayTabContent($this->getWebpayStatus(), $this->getWebpayForm())
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
            element::styleFile($this->getAdditionalCSSFilePath())
        );
    }

    /**
     * @return string
     */
    public function getInstructionsTabLabel()
    {
        return $this->translator->translate(ClientViewFieldsEpos::INSTRUCTIONS_TAB_LABEL);
    }

    /**
     * @return string
     */
    public function getInstructionsText()
    {
        return $this->configWrapper->cookText($this->translator->translate(ClientViewFieldsEpos::INSTRUCTIONS), $this->orderWrapper);
    }


    /**
     * @return string
     */
    public function getCompletionText()
    {
        return $this->configWrapper->cookText($this->configWrapper->getCompletionText(), $this->orderWrapper);
    }

    /**
     * @return bool
     */
    public function isInstructionsSectionEnabled()
    {
        return $this->configWrapper->isInstructionsSectionEnabled();
    }

    /**
     * @return bool
     */
    public function isWebpaySectionEnabled()
    {
        return $this->configWrapper->isWebpaySectionEnabled();
    }

    /**
     * @return bool
     */
    public function isQRCodeSectionEnabled()
    {
        return $this->configWrapper->isQRCodeSectionEnabled();
    }

    /**
     * @return string
     */
    public function getQRCodeTabLabel()
    {
        return $this->translator->translate(ClientViewFieldsEpos::QRCODE_TAB_LABEL);
    }

    /**
     * @return string
     */
    public function getQRCodeDetails()
    {
        return strtr($this->translator->translate(ClientViewFieldsEpos::QRCODE_DETAILS), array(
            "@qr_code" => $this->getQrCode()
        ));
    }

    /**
     * @return mixed
     */
    public function getWebpayForm()
    {
        return $this->webpayForm;
    }

    /**
     * @param mixed $webpayForm
     */
    public function setWebpayForm($webpayForm)
    {
        $this->webpayForm = $webpayForm;
    }

    /**
     * @return mixed
     */
    public function getQrCode()
    {
        return $this->qrCode;
    }

    /**
     * @param mixed $qrCode
     */
    public function setQrCode($qrCode)
    {
        $this->qrCode = $qrCode;
    }

    /**
     * @return string
     */
    public function getWebpayStatus()
    {
        return $this->webpayStatus;
    }

    /**
     * @param mixed $webpayStatus
     */
    public function setWebpayStatus($webpayStatus)
    {
        $this->webpayStatus = $webpayStatus;
    }

    /**
     * @return string
     */
    public function getWebpayTabLabel()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_TAB_LABEL);
    }

    /**
     * @return string
     */
    public function getWebpayButtonLabel()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_BUTTON_LABEL);
    }


    /**
     * @return string
     */
    public function getWebpayDetails()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_DETAILS);
    }

    /**
     * @return string
     */
    public function getWebpayMsgSuccess()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_SUCCESS);
    }

    /**
     * @return string
     */
    public function getWebpayMsgUnsuccess()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNSUCCESS);
    }

    /**
     * @return string
     */
    public function getWebpayMsgUnavailable()
    {
        return $this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNAVAILABLE);
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
        $webpayStatusPresent = '' != $this->getWebpayStatus();
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
        if ($this->configWrapper->isInstructionsSectionEnabled())
            $enabledTabsCount++;
        if ($this->configWrapper->isQRCodeSectionEnabled())
            $enabledTabsCount++;
        if ($this->configWrapper->isWebpaySectionEnabled())
            $enabledTabsCount++;
        return $enabledTabsCount == 1;
    }

    const TAB_KEY_WEBPAY = "webpay";
    const TAB_KEY_INSTRUCTIONS = "instructions";
    const TAB_KEY_QRCODE = "qrcode";

    public function elementWebpayTab()
    {
        if ($this->isWebpaySectionEnabled()) {
            return $this->elementTab(
                self::TAB_KEY_WEBPAY,
                $this->getWebpayTabLabel(),
                $this->elementWebpayTabContent($this->getWebpayStatus(), $this->getWebpayForm()));
        }
        return "";
    }

    public function elementInstructionsTab()
    {
        if ($this->isInstructionsSectionEnabled()) {
            return $this->elementTab(
                self::TAB_KEY_INSTRUCTIONS,
                $this->getInstructionsTabLabel(),
                $this->getInstructionsText());
        }
        return "";
    }

    public function elementQRCodeTab()
    {
        if ($this->isQRCodeSectionEnabled()) {
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

    public function getAdditionalCSSFilePath()
    {
        if ("default" == $this->configWrapper->getCompletionCssFile())
            return dirname(__FILE__) . "/completion-default.css";
        else
            return $_SERVER['DOCUMENT_ROOT'] . $this->configWrapper->getCompletionCssFile();
    }

    const STATUS_PAYED = 'payed';
    const STATUS_FAILED = 'failed';

    public function elementWebpayTabContent($status, $webpayForm)
    {
        $ret =
            element::div(
                attribute::id("webpay_details"),
                element::content($this->translator->translate(ClientViewFieldsEpos::WEBPAY_DETAILS)),
                element::br());

        $ret .= $this->elementWebpayTabContentResultMsg($status);

        if ("" != $webpayForm) {
            $ret .=
                element::div(
                    attribute::id("webpay"),
                    attribute::align("right"),
                    element::img(
                        attribute::id("webpay-ps-image"),
                        attribute::src(ResourceUtils::getImageUrl('ps_icons.png')),
                        attribute::alt("")
                    ),
                    element::br(),
                    element::content($webpayForm),
                    element::includeFile(dirname(__FILE__) . "/webpayJs.php", ["completionPanel" => $this]));
        } else {
            $ret .=
                element::div(
                    attribute::id("webpay_message_unavailable"),
                    element::content($this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNAVAILABLE)));
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
                    element::content($this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_SUCCESS)));
        } elseif (self::STATUS_FAILED == $status) {
            return
                element::div(
                    attribute::clazz($this->getCssClass4MsgUnsuccess()),
                    attribute::id("webpay_message"),
                    element::content($this->translator->translate(ClientViewFieldsEpos::WEBPAY_MSG_UNSUCCESS)));
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