<?php


namespace esas\cmsgate\epos\utils;


use esas\cmsgate\utils\ResourceUtils;

class ResourceUtilsEpos extends ResourceUtils
{
    private static function getImageDir() {
        return dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . "/static/image/";
    }

    public static function getPsImageUrl() {
        return self::getImageUrl(self::getImageDir(), 'ps_icons.png');
    }

    public static function getLogoEposWhite() {
        return self::getImageUrl(self::getImageDir(), 'epos_by_white.svg');
    }

    public static function getLogoEposWhiteVertical() {
        return self::getImageUrl(self::getImageDir(), 'epos_by_white_vertical.svg');
    }
}