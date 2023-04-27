<?php


namespace esas\cmsgate\epos\hro\sections;


use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\hro\HRO;
use esas\cmsgate\hro\HROTuner;
use esas\cmsgate\hro\sections\FooterSectionCompanyInfoHRO;
use esas\cmsgate\lang\Translator;

class FooterSectionCompanyInfoHROTunerEpos implements HROTuner
{
    /**
     * @param FooterSectionCompanyInfoHRO $hroBuilder
     * @return HRO|void
     */
    public function tune($hroBuilder) {
        return $hroBuilder
            ->addAboutItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ABOUT_FULL_NAME))
            ->addAboutItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ABOUT_REGISTRATION_DATA))
            ->addAddressItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ADDRESS_POST))
            ->addAddressItem(Translator::fromRegistry()->translate(ClientViewFieldsEpos::EPOS_ADDRESS_LEGAL))
            ->addContactItemEmail("support@epos.by")
            ->addContactItemPhone("+375 17 3971919")
            ->addContactItemPhone("+375 29 6353316");
    }
}