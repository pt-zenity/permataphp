<?php
/**
 * Define Variables & Constants
 * Pure PHP - tanpa framework
 */
defined('main') or die('Restricted access');

require_once __DIR__ . '/df.php';
require_once __DIR__ . '/definevar.mbanking.php';
require_once __DIR__ . '/definevar.tektaya.php';

define('msHutangDeposit',  1);
define('msPenjualan',      3);

// Kode Aplikasi/Channel
define('GCOLLECTION',                   '001');
define('DIGITAL_BANK',                  '002');
define('MOBILE_MODAL',                  '003');
define('DIGITAL_BANK_AMBILDATA',        '004');
define('DIGITAL_BANK_CEKSTATUSTRX',     '005');
define('MOBILE_TEKNISI',                '006');
define('MOBILE_DASHBOARD_MANAGEMENT',   '007');
define('MOBILE_KAS',                    '008');
define('DIGITAL_BANK_REGISTER',         '009');
define('DIGITAL_BANK_INQUIRY',          '010');
define('CBS',                           '011');
define('MY_ABIMART',                    '012');
define('WTPPAYMENT',                    '016');
define('DIGITAL_DASHBOARD_MANAGEMENT',  '020');
define('TRX_WINPAY',                    '021');
