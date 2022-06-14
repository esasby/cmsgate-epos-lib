<?php
/**
 * Created by IntelliJ IDEA.
 * User: nikit
 * Date: 03.12.2018
 * Time: 12:55
 */

namespace esas\cmsgate\epos\utils;


use Com\Tecnick\Barcode\Barcode;
use esas\cmsgate\epos\RegistryEpos;

class QRUtils
{
    public static function createQRCode($address, $qrData)
    {
//        $configWrapper = RegistryEpos::getRegistry()->getConfigWrapper();
//        $orderWrapper = RegistryEpos::getRegistry()->getOrderWrapper($orderId);
//        $qrCodeString =
//            self::tlv(0, "01") .
//            self::tlv(1, "11") .
//            self::tlv(32,
//                self::tlv(0, "by.raschet") .
//                self::tlv(1, $configWrapper->getEripTreeId()) .
//                self::tlv(10, $orderWrapper->getOrderNumber()) .
//                self::tlv(12, "12")) .
//            self::tlv(53, "933") .
//            self::tlv(54, $orderWrapper->getAmount()) .
//            self::tlv(58, "BY") .
//            ($configWrapper->getShopName() != "" ? self::tlv(59, $configWrapper->getShopName()) : "") .
//            self::tlv(60, "Belarus") .
//            self::tlv(62, self::tlv(1, $orderWrapper->getOrderNumber()));
        $qrCodeString =
            $address . "#" .
            rawurlencode($qrData); // rawurlencode вместо urlencode для корректной кодировки пробелов
//            strtoupper(self::tlv(63, substr(str_replace("-", "", hash('sha256', $qrData)), -4)));
        $barcode = new Barcode();
        $bobj = $barcode
            ->getBarcodeObj(
                'QRCODE,H',                     // barcode type and additional comma-separated parameters
                $qrCodeString,          // data string to encode
                -4,                             // bar width (use absolute or negative value as multiplication factor)
                -4,                             // bar height (use absolute or negative value as multiplication factor)
                'black',                        // foreground color
                array(-2, -2, -2, -2))          // padding (use absolute or negative values as multiplication factors)
            ->setBackgroundColor('white');
        return $bobj->getSvgCode();
    }

    private static function tlv($tag, $value)
    {
        return $value == "" ? "" : sprintf("%02d%02d%s", $tag, strlen($value), $value);
    }
}