<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 16.07.2019
 * Time: 11:44
 */

namespace esas\cmsgate\epos\lang;


use esas\cmsgate\lang\TranslatorImpl;

class TranslatorEpos extends TranslatorImpl
{
    /**
     * TranslatorEpos constructor.
     * @param $localeLoader
     */
    public function __construct($localeLoader)
    {
        parent::__construct($localeLoader, __DIR__);
    }
}