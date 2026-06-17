<?php
/**
 * MBankingFunc - Fungsi-fungsi mBanking (ISO 8583 helper)
 * Disalin dari source original dengan perbaikan
 */
defined('main') or die('Restricted access');

class MBankingFunc
{
    private static ?Iso8583 $objISO = null;
    private static string $cMTI     = '0200';
    private static array  $vaData   = [];

    private static function initISO(): void
    {
        if (self::$objISO === null) {
            self::$objISO = new Iso8583();
        }
    }

    public static function Array2ISO(array $vaArray): string
    {
        self::initISO();
        foreach ($vaArray as $key => $value) {
            if ($key === 'MTI') {
                self::$objISO->addMTI($value);
            } else {
                self::$objISO->addData($key, $value);
            }
        }
        return self::$cMTI . self::$objISO->getISO();
    }

    public static function ISO2Array(string $cISO): array
    {
        self::initISO();
        self::$objISO->addISO($cISO);
        $cMTI   = self::$objISO->getMTI();
        $va     = self::$objISO->getData();
        $cMD5   = md5($cISO);
        $vaData = ['ISO_MD5' => $cMD5, 'ISO' => $cISO, 'MTI' => $cMTI];
        foreach ($va as $key => $value) {
            $key = 'DE' . str_pad((string)$key, 3, '0', STR_PAD_LEFT);
            $vaData[$key] = $value;
        }
        return $vaData;
    }

    public static function JSON2ISO(
        bool   $isMutasiTab,
        string $cDE003, string $cDE004, string $cDE012, string $cDE013,
        string $cDE037, string $cDE039, string $cDE044, string $cDE048,
        string $cDE052, string $cDE061, string $cDE102, string $cDE103,
        string $cDE105 = '', string $cDE106 = '', string $cDE107 = '',
        string $cDE108 = '', string $cDE109 = '', string $cDE110 = ''
    ): string {
        if ($isMutasiTab) {
            $vaCode = [
                3=>$cDE003, 4=>$cDE004, 12=>$cDE012, 13=>$cDE013,
                37=>$cDE037, 39=>$cDE039, 44=>$cDE044, 48=>$cDE048,
                52=>$cDE052, 61=>$cDE061, 102=>$cDE102, 103=>$cDE103,
                105=>$cDE105, 106=>$cDE106, 107=>$cDE107, 108=>$cDE108,
                109=>$cDE109, 110=>$cDE110
            ];
        } else {
            $vaCode = [
                3=>$cDE003, 4=>$cDE004, 12=>$cDE012, 13=>$cDE013,
                37=>$cDE037, 39=>$cDE039, 44=>$cDE044, 48=>$cDE048,
                52=>$cDE052, 61=>$cDE061, 102=>$cDE102, 103=>$cDE103
            ];
        }
        return self::Array2ISO($vaCode);
    }
}
