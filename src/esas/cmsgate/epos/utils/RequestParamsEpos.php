<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 26.02.2019
 * Time: 12:49
 */

namespace esas\cmsgate\epos\utils;


use esas\cmsgate\utils\RequestParams;

class RequestParamsEpos extends RequestParams
{
    const INVOICE_ID = "invoice_id";
    const EPOS_STATUS = "epos_status";
    const WEBPAY_STATUS = "webpay_status";
}