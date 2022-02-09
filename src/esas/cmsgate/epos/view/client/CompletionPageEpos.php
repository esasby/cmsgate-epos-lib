<?php


namespace esas\cmsgate\epos\view\client;


use esas\cmsgate\Registry;
use esas\cmsgate\view\client\CompletionPage;

class CompletionPageEpos extends CompletionPage
{

    public function getAboutArray()
    {
        return [
            $this->translator->translate(ClientViewFieldsEpos::EPOS_ABOUT_FULL_NAME),
            $this->translator->translate(ClientViewFieldsEpos::EPOS_ABOUT_REGISTRATION_DATA),
        ];
    }

    public function getAddressArray()
    {
        return [
            $this->translator->translate(ClientViewFieldsEpos::EPOS_ADDRESS_POST),
            $this->translator->translate(ClientViewFieldsEpos::EPOS_ADDRESS_LEGAL),
        ];
    }

    public function getContactsArray()
    {
        return [
            "support@epos.by",
            "+375 17 3971919",
            "+375 29 6353316"
        ];
    }
}