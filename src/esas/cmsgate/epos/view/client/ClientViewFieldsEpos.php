<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 17.08.2018
 * Time: 11:09
 */

namespace esas\cmsgate\epos\view\client;

use esas\cmsgate\view\client\ClientViewFields;

/**
 * Перечисление полей, доступных на странице успешного выставления счета
 * Class ClientViewFieldsEpos
 * @package esas\epos\view
 */
class ClientViewFieldsEpos extends ClientViewFields
{
    const INSTRUCTIONS_TAB_LABEL = 'epos_instructions_tab_label';
    const INSTRUCTIONS = 'epos_instructions_text';
    const QRCODE_TAB_LABEL = 'epos_qrcode_tab_label';
    const QRCODE_DETAILS = 'epos_qrcode_details';
    const WEBPAY_TAB_LABEL = 'epos_webpay_tab_label';
    const WEBPAY_DETAILS = 'epos_webpay_details';
    const WEBPAY_BUTTON_LABEL = 'epos_webpay_button_label';
    const WEBPAY_MSG_SUCCESS = 'epos_webpay_msg_success';
    const WEBPAY_MSG_UNSUCCESS = 'epos_webpay_msg_unsuccess';
    const WEBPAY_MSG_UNAVAILABLE = 'epos_webpay_msg_unavailable';
    const UNKNOWN_PRODUCT = 'epos_unknown_product';
}