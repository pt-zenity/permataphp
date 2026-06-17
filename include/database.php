<?php
/**
 * Database & Session bootstrap
 * Pure PHP - tanpa framework
 */
defined('main') or die('Restricted access');

if (!defined('sessionstart')) {
    define('sessionstart', 1);

    // Autoloader sederhana untuk class-class
    spl_autoload_register(function (string $className) {
        $map = [
            'objData'        => BASE_PATH . '/include/objdata.php',
            'Iso8583'        => BASE_PATH . '/include/iso8583.php',
            'DataCDS'        => BASE_PATH . '/include/autoload/Classes/DataCDS/DataCDS.mod.php',
            'MBankingFunc'   => BASE_PATH . '/include/autoload/Classes/mbanking/MBankingFunc.mod.php',
            'TutupPPOB'      => BASE_PATH . '/include/autoload/Classes/mbanking/TutupPPOB.mod.php',
            'ProsesTektaya'  => BASE_PATH . '/include/autoload/Classes/tektaya/ProsesTektaya.mod.php',
        ];
        if (isset($map[$className]) && is_file($map[$className])) {
            require_once $map[$className];
        }
    });

    // Mulai session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Load variable & fungsi
    require_once BASE_PATH . '/include/definevar.mod.php';
    require_once BASE_PATH . '/include/func.mod.php';

    // Koneksi database
    connect_db();
}
