<?php
/**
 * Created by PhpStorm.
 * User: nikit
 * Date: 27.02.2018
 * Time: 14:46
 */

namespace esas\cmsgate\epos\protocol;


class EposWebPayRs extends EposRs
{
    private $htmlForm;

    /**
     * @return string
     */
    public function getHtmlForm()
    {
        return $this->htmlForm;
    }

    /**
     * @param mixed $htmlForm
     */
    public function setHtmlForm($htmlForm)
    {
        $this->htmlForm = $htmlForm;
    }


}