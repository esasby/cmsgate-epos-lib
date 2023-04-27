<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 09.07.2020
 * Time: 11:31
 */

namespace esas\cmsgate\epos\view\admin;


use esas\cmsgate\view\admin\AdminViewFields;

class AdminViewFieldsEpos extends AdminViewFields
{
    const EPOS_PROCESSOR_ESAS = "esas";
    const EPOS_PROCESSOR_UPS = "ups";
    const EPOS_PROCESSOR_RRB = "rrb";
    const LOGIN_FORM_LOGIN = 'login';
    const LOGIN_FORM_PASSWORD = 'password';
}