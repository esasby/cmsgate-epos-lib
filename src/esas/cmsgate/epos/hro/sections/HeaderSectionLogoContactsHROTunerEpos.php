<?php


namespace esas\cmsgate\epos\hro\sections;


use esas\cmsgate\epos\utils\ResourceUtilsEpos;
use esas\cmsgate\hro\HRO;
use esas\cmsgate\hro\HROTuner;
use esas\cmsgate\hro\sections\HeaderSectionLogoContactsHRO;

class HeaderSectionLogoContactsHROTunerEpos implements HROTuner
{
    /**
     * @param HeaderSectionLogoContactsHRO $hroBuilder
     * @return HRO|void
     */
    public function tune($hroBuilder) {
        return $hroBuilder
            ->setLogo(ResourceUtilsEpos::getLogoEposWhite())
            ->setSmallLogo(ResourceUtilsEpos::getLogoEposWhiteVertical())
            ->addContactItemEmail("support@epos.by")
            ->addContactItemPhone("+375 17 3971919");
    }
}