<?php


namespace esas\cmsgate\epos\hro\client;


use esas\cmsgate\hro\HRO;

interface CompletionPanelEposHRO extends HRO
{
    /**
     * @param boolean $enabled
     * @return CompletionPanelEposHRO
     */
    public function setInstructionsSectionEnabled($enabled);

    /**
     * @param mixed $instructionText
     * @return CompletionPanelEposHRO
     */
    public function setInstructionText($instructionText);

    /**
     * @param boolean $enabled
     * @return CompletionPanelEposHRO
     */
    public function setWebpaySectionEnabled($enabled);

    /**
     * @param mixed $completionText
     * @return CompletionPanelEposHRO
     */
    public function setCompletionText($completionText);

    /**
     * @param mixed $webpayForm
     * @return CompletionPanelEposHRO
     */
    public function setWebpayForm($webpayForm);

    /**
     * @param mixed $webpayStatus
     * @return CompletionPanelEposHRO
     */
    public function setWebpayStatus($webpayStatus);

    /**
     * @param boolean $enabled
     * @return CompletionPanelEposHRO
     */
    public function setQRCodeSectionEnabled($enabled);

    /**
     * @param mixed $qrCode
     * @return CompletionPanelEposHRO_v1
     */
    public function setQrCode($qrCode);

    public function setAdditionalCSSFile($fileName);

    public function renderWebpayOnly();

    public function redirectWebpay();
}