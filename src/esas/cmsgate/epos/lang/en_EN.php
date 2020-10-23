<?php

use esas\cmsgate\epos\ConfigFieldsEpos;
use esas\cmsgate\epos\view\admin\AdminViewFieldsEpos;
use esas\cmsgate\epos\view\client\ClientViewFieldsEpos;
use esas\cmsgate\view\admin\AdminViewFields;

return array(
    ConfigFieldsEpos::shopName() => 'Shop name',
    ConfigFieldsEpos::shopName() . _DESC => 'Your shop short name',

    ConfigFieldsEpos::iiiClientId() => 'Client id',
    ConfigFieldsEpos::iiiClientId() . _DESC => '',

    ConfigFieldsEpos::iiiClientSecret() => 'Client secret',
    ConfigFieldsEpos::iiiClientSecret() . _DESC => '',

    ConfigFieldsEpos::eposServiceProviderCode() => 'SP code',
    ConfigFieldsEpos::eposServiceProviderCode() . _DESC => 'Your shop EPOS unique service provider code',

    ConfigFieldsEpos::eposServiceCode() => 'EPOS service code',
    ConfigFieldsEpos::eposServiceCode() . _DESC => 'Your shop EPOS service code',

    ConfigFieldsEpos::eposRetailOutletCode() => 'Retail outlet code',
    ConfigFieldsEpos::eposRetailOutletCode() . _DESC => 'Your retail outlet code',

    ConfigFieldsEpos::eposProcessor() => 'EPOS Processor',
    ConfigFieldsEpos::eposProcessor() . _DESC => 'EPOS service processor',

    ConfigFieldsEpos::sandbox() => 'Sandbox',
    ConfigFieldsEpos::sandbox() . _DESC => 'Sandbox mode. If *true* then all requests will be sent to trial host api-dev.hgrosh.by',

    ConfigFieldsEpos::instructionsSection() => 'Section Instructions',
    ConfigFieldsEpos::instructionsSection() . _DESC => 'If *true* then customer will see step-by-step instructions to pay bill with ERIP',

    ConfigFieldsEpos::qrcodeSection() => 'Section QR-code',
    ConfigFieldsEpos::qrcodeSection() . _DESC => 'If *true* then customer will be able to pay bill with QR-code',

    ConfigFieldsEpos::webpaySection() => 'Section Webpay',
    ConfigFieldsEpos::webpaySection() . _DESC => 'If *true* then customer will get *Pay with car* button on success page',

    ConfigFieldsEpos::completionText() => 'Completion text',
    ConfigFieldsEpos::completionText() . _DESC => 'Text displayed to the client after the successful invoice. Can contain html. ' .
        'In the text you can refer to variables @order_id, @order_number, @order_total, @order_currency, @order_fullname, @order_phone, @order_address',
    ConfigFieldsEpos::completionText() . _DEFAULT => '<p>Bill #<strong>@order_number</strong> was successfully placed in ERIP</p>
<p>You can pay it in cash, a plastic card and electronic money, in any bank, cash departments, ATMs, payment terminals, in the system of electronic money, through Internet banking, M-banking, online acquiring</p>',

    ConfigFieldsEpos::completionCssFile() => 'Completion page CSS file',
    ConfigFieldsEpos::completionCssFile() . _DESC => 'Optional CSS file path for completion page',

    ConfigFieldsEpos::paymentMethodName() => 'Payment method name',
    ConfigFieldsEpos::paymentMethodName() . _DESC => 'Name displayed to the customer when choosing a payment method',
    ConfigFieldsEpos::paymentMethodName() . _DEFAULT => 'EPOS (invoicing to ERIP)',

    ConfigFieldsEpos::paymentMethodDetails() => 'Payment method details',
    ConfigFieldsEpos::paymentMethodDetails() . _DESC => 'Description of the payment method that will be shown to the client at the time of payment',
    ConfigFieldsEpos::paymentMethodDetails() . _DEFAULT => 'EPOS™ — payment service for invoicing in AIS *Raschet* (ERIP). After invoicing you will be available for payment by a plastic card and electronic money, at any of the bank branches, cash desks, ATMs, payment terminals, in the electronic money system, through Internet banking, M-banking, Internet acquiring. ' .
'Also EPOS service gives possibility to pay bills by QR-codes',

    ConfigFieldsEpos::paymentMethodNameWebpay() => 'Payment method name (webpay)',
    ConfigFieldsEpos::paymentMethodNameWebpay() . _DESC => 'Name displayed to the customer when choosing a payment method',
    ConfigFieldsEpos::paymentMethodNameWebpay() . _DEFAULT => 'By card',

    ConfigFieldsEpos::paymentMethodDetailsWebpay() => 'Payment method details (webpay)',
    ConfigFieldsEpos::paymentMethodDetailsWebpay() . _DESC => 'Description of the payment method that will be shown to the client at the time of payment',
    ConfigFieldsEpos::paymentMethodDetailsWebpay() . _DEFAULT => 'Payment by card (VISA, Maestro, Belcard) view Webpay',

    ConfigFieldsEpos::billStatusPending() => 'Bill status pending',
    ConfigFieldsEpos::billStatusPending() . _DESC => 'Mapped status for pending bills',

    ConfigFieldsEpos::billStatusPayed() => 'Bill status payed',
    ConfigFieldsEpos::billStatusPayed() . _DESC => 'Mapped status for payed bills',

    ConfigFieldsEpos::billStatusFailed() => 'Bill status failed',
    ConfigFieldsEpos::billStatusFailed() . _DESC => 'Mapped status for failed bills',

    ConfigFieldsEpos::billStatusCanceled() => 'Bill status canceled',
    ConfigFieldsEpos::billStatusCanceled() . _DESC => 'Mapped status for canceled bills',

    ConfigFieldsEpos::dueInterval() => 'Bill due interval (days)',
    ConfigFieldsEpos::dueInterval() . _DESC => 'How many days new bill will be available for payment',

    ClientViewFieldsEpos::INSTRUCTIONS_TAB_LABEL => 'Payment instructions',
    ClientViewFieldsEpos::INSTRUCTIONS => '<p>To pay an bill in ERIP:</p>
<ol>
    <li>Select the ERIP payment tree</li>
    <li>Select a service: <strong>EPOS</strong></li>
    <li>Enter bill number <strong>@epos_order_id</strong></li>
    <li>Verify information is correct</li>
    <li>Make a payment</li>
</ol>',

    ClientViewFieldsEpos::QRCODE_TAB_LABEL => 'Pay with QR-code',
    ClientViewFieldsEpos::QRCODE_DETAILS => '<p>You can pay this bill by QR-code:</p>
<div align="center">@qr_code</div>
<p>To get information about mobile apps with QR-code payment support please visit <a href="http://pay.raschet.by/" target="_blank"style="color: #8c2003;"><span>this link</span></a></p>',


    ClientViewFieldsEpos::WEBPAY_TAB_LABEL => 'Pay with card',
    ClientViewFieldsEpos::WEBPAY_DETAILS => 'You can pay bill with Visa, Mastercard or Belcard',
    ClientViewFieldsEpos::WEBPAY_BUTTON_LABEL => 'Continue',
    ClientViewFieldsEpos::WEBPAY_MSG_SUCCESS => 'Webpay: payment completed!',
    ClientViewFieldsEpos::WEBPAY_MSG_UNSUCCESS => 'Webpay: payment failed!',
    ClientViewFieldsEpos::WEBPAY_MSG_UNAVAILABLE => 'Sorry, operation currently not available',

    AdminViewFields::ADMIN_PAYMENT_METHOD_NAME => 'EPOS',
    AdminViewFields::ADMIN_PAYMENT_METHOD_DESCRIPTION => 'Payment via EPOS service',

    AdminViewFieldsEpos::EPOS_PROCESSOR_ESAS => "Esas",
    AdminViewFieldsEpos::EPOS_PROCESSOR_UPS => "UPS",
    AdminViewFieldsEpos::EPOS_PROCESSOR_RRB => "RRB Bank"
);