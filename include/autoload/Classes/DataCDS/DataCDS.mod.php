<?php
/**
 * DataCDS - Akses Central Data Store
 * Pure PHP - tanpa framework
 */
defined('main') or die('Restricted access');

class DataCDS
{
    /**
     * Ambil data dari CDS berdasarkan kode
     */
    public static function GetArray(string $kode): mixed
    {
        $cKey       = aCfg('msCDSID');
        $cTime      = date('c');
        $cVersion   = '1.0';
        $cSignature = md5("{$cKey}:{$cTime}:{$cVersion}:");
        $cdsUrl     = aCfg('msCDSURL') ?: 'cds.sis1.net/cds/public/cds/json';

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,            $cdsUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST,  'GET');
        curl_setopt($ch, CURLOPT_TIMEOUT,        15);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            "SIS-Key: $cKey",
            'SIS-Data-Type: JSON',
            "SIS-Timestamp: $cTime",
            "SIS-Version: $cVersion",
            "SIS-Signature: $cSignature",
            "SIS-Kode: $kode",
        ]);

        $body = curl_exec($ch);
        $err  = curl_error($ch);
        curl_close($ch);

        if ($body === false || empty($body)) {
            error_log('DataCDS::GetArray cURL error: ' . $err);
            return '';
        }

        $vaBody = json_decode($body, true);
        if (
            isset($vaBody['response_code'])
            && $vaBody['response_code'] == 200
            && isset($vaBody['data'][$kode])
        ) {
            return $vaBody['data'][$kode];
        }

        return '';
    }
}
