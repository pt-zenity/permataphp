<?php
/**
 * Define Variables - mBanking
 */
defined('main') or die('Restricted access');

// Processing Codes DE003
define('DE003_TRX_PURCHASE',            '201040');
define('DE003_TRX_PASCA',               '211000');
define('DE003_TRX_PASCA_2',             '211041');
define('DE003_TRX_MUTASI_TABUNGAN',     '021000');
define('DE003_TRX_INQUIRY_PPOB',        '231041');

// Transaction Codes mBanking
define('TRX_MB_CHECK_SALDO',            '01');
define('TRX_MB_MUTASI_TABUNGAN',        '02');
define('TRX_MB_TRANSFER_DEBET',         '03');
define('TRX_MB_AKTIVASI_NOMOR',         '04');
define('TRX_MB_INQUIRY_REKENING_ASAL',  '05');
define('TRX_MB_INQUIRY_REKENING_TUJUAN','06');
define('TRX_MB_GANTI_PIN',              '07');
define('TRX_MB_CEK_AKTIFASI',           '08');
define('TRX_MB_KELUAR_APLIKASI',        '09');
define('TRX_MB_PURCHASE',               '20');
define('TRX_MB_PAYMENT',                '21');
define('TRX_MB_INQUIRY_PAYMENT',        '23');
define('TRX_MB_DETAIL_MUTASI',          '30');
define('TRX_MB_ANGSURAN',               '29');
define('TRX_MB_AKTIVASI_FROM_CORE',     '50');
define('TRX_MB_DEAKTIVASI_FROM_CORE',   '51');
define('TRX_MB_SENDSMS',                '52');
define('TRX_MB_NON_ISO',                '53');
define('TRX_MB_GETTOKEN_DIGITAL',       '54');
define('TRX_MB_ZAKAT',                  '55');
define('TRX_MB_INFAQ',                  '56');
define('TRX_MB_WAKAF',                  '57');
define('TRX_MB_LOGIN_VIA_EMAIL',        '60');
define('TRX_MB_SAVING_EMAIL_FROM_CORE', '61');
define('TRX_MB_RESET_PIN_FROM_CORE',    '62');
define('TRX_MMODAL_VIA_MBANKING',       '70');
define('TRX_MB_AKTIVASI_FROM_CORE_V2',  '80');
define('TRX_MB_VALIDASI_KODE_FASILITAS','81');

// Non-ISO Transaction Codes
define('TRX_PRODUK',                    '1');
define('TRX_STATUS_FITUR',              '2');
define('TRX_SMSCENTER',                 '3');
define('TRX_PREFIX',                    '4');
define('TRX_GET_TOKEN',                 '5');
define('TRX_GET_TRX',                   '6');
define('TRX_GET_KODE_BANK',             '7');
define('TRX_GET_BERITA',                '8');
define('TRX_GET_BROSUR',                '9');
define('TRX_INQ_PAYMENT',               '12');
define('TRX_GET_SYARAT',                '13');
define('TRX_GET_PROMO',                 '15');
define('TRX_GET_POINT_PROMO',           '22');
define('TRX_GET_INFO_BANK',             '65');
define('TRX_GET_IMAGE',                 '10');
define('TRX_GET_CONFIG',                '11');
define('TRX_CHECK_ACTIVE_ACCOUNT',      '17');
define('TRX_CHECK_LATEST_APP',          '18');
define('TRX_GET_ADDITIONAL_PRODUCT',    '19');
define('TRX_CEK_EMAIL_AKTIVASI',        '66');
define('TRX_CEK_AKUN_PERANGKAT',        '67');
define('TRX_SEND_SMS_CBS',              '68');
define('TRX_LOGIN_APP',                 '69');
define('TRX_UPDATE_APP_VERSION',        '70');
define('TRX_GET_RIWAYAT_PPOB',          '20');
define('TRX_GET_RIWAYAT_VA',            '21');
define('TRX_GET_MARGIN_PRODUK',         '22');
define('TRX_GET_POSPULSA',              '28');
define('TRX_PRODUK_EMONEY',             '01');

// Kode Finance Company
define('COLUMBIA',      'FNCLMB');
define('MEGA_AUTO',     'FNMAF');
define('MEGA_CENTRAL',  'FNMEGA');
define('ADIRA',         'FNADIRAH');
define('FIF',           'FNFIF');
define('WOM',           'FNWOM');
