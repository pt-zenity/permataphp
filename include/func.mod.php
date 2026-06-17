<?php
/**
 * Fungsi-fungsi utama aplikasi
 * Pure PHP - tanpa framework
 */
defined('main') or die('Restricted access');

require_once __DIR__ . '/df.php';
require_once __DIR__ . '/func.h2h.mod.php';

/**
 * Koneksi database berdasarkan konfigurasi
 */
function connect_db(): void
{
    // Cek file assist.ini.php untuk override config
    $cHost     = 'localhost';
    $cDatabase = 'bpr_web';
    $cUser     = 'Assist';
    $cPass     = 'Irac';
    $cPort     = 3306;

    $iniFile = BASE_PATH . '/include/assist.ini.php';
    if (is_file($iniFile)) {
        $lines = file($iniFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $line = trim($line);
            if (str_starts_with($line, '#') || str_starts_with($line, '<?')) continue;
            $parts = explode('=', $line, 2);
            if (count($parts) < 2) continue;
            $tag = strtolower(trim($parts[0]));
            $val = trim($parts[1]);
            match ($tag) {
                'ip'       => $cHost     = $val,
                'port'     => $cPort     = (int)$val,
                'database' => $cDatabase = $val,
                'user'     => $cUser     = $val,
                'password' => $cPass     = $val,
                default    => null,
            };
        }
    }

    // Override dari environment variables (prioritas lebih tinggi)
    if (getenv('DB_HOST'))     $cHost     = getenv('DB_HOST');
    if (getenv('DB_PORT'))     $cPort     = (int)getenv('DB_PORT');
    if (getenv('DB_NAME'))     $cDatabase = getenv('DB_NAME');
    if (getenv('DB_USER'))     $cUser     = getenv('DB_USER');
    if (getenv('DB_PASS'))     $cPass     = getenv('DB_PASS');

    SaveSetting('cSession_SystemDatabase', $cDatabase);
    SaveSetting('cSession_IP',             $cHost);

    $cDatabase = GetUserDatabase();
    SaveSetting('cSession_Database', $cDatabase);

    objData::Connect($cHost, $cUser, $cPass, $cDatabase, $cPort);
}

/**
 * Simpan nilai ke session
 */
function SaveSetting(string $key, mixed $value): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION[$key] = $value;
}

/**
 * Ambil nilai dari session
 */
function GetSetting(string $key, mixed $default = ''): mixed
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    return $_SESSION[$key] ?? $default;
}

/**
 * Waktu saat ini (unix timestamp)
 */
function now(): int
{
    return time();
}

/**
 * Waktu saat ini sebagai string Y-m-d H:i:s
 */
function SNow(): string
{
    return date('Y-m-d H:i:s', now());
}

/**
 * Dapatkan temporary directory
 */
function GetTmpDir(): string
{
    $baseDir = BASE_PATH . '/tmp';
    if (!is_dir($baseDir)) {
        mkdir($baseDir, 0777, true);
    }

    $nDir  = (int)date('H') % 3;
    $nDir1 = $nDir + 1;
    if ($nDir1 === 3) $nDir1 = 0;

    $dirNext = $baseDir . '/tmp' . $nDir1;
    $dirCurr = $baseDir . '/tmp' . $nDir;

    if (is_dir($dirNext)) {
        DeleteDirectory($dirNext);
    }
    if (!is_dir($dirCurr)) {
        mkdir($dirCurr, 0777, true);
    }

    return $dirCurr;
}

/**
 * Dapatkan nama temporary file
 */
function GetTmpFile(): string
{
    return GetTmpDir() . '/' . md5(rand(0, 10000) . now() . session_id());
}

/**
 * Hapus directory beserta isinya
 */
function DeleteDirectory(string $dir): void
{
    if (!is_dir($dir)) return;
    $d = dir($dir);
    while (false !== ($entry = $d->read())) {
        if ($entry === '.' || $entry === '..') continue;
        $path = $dir . '/' . $entry;
        if (is_dir($path)) {
            DeleteDirectory($path);
        } else {
            unlink($path);
        }
    }
    $d->close();
    rmdir($dir);
}

/**
 * Kirim HTTP POST menggunakan cURL
 */
function SendHTTPPost(string $url, string $message, array $headers = []): string
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS,     $message);
    curl_setopt($ch, CURLOPT_FAILONERROR,    true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,     $headers);
    curl_setopt($ch, CURLOPT_TIMEOUT,        30);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    $response = curl_exec($ch);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($response === false) {
        error_log('SendHTTPPost cURL Error: ' . $err . ' | URL: ' . $url);
        return '';
    }
    return ltrim((string)$response);
}

/**
 * Increment frekuensi alfanumerik
 */
function GetLastFrekuensi(string $frekuensi): string
{
    if ($frekuensi[0] >= '0' && $frekuensi[0] <= '9') {
        if ($frekuensi === '99') $frekuensi = 'A0';
        $frekuensi++;
    } else {
        $frekuensi++;
        if (substr($frekuensi, 1, 1) === '0') {
            $frekuensi = substr($frekuensi, 0, 1) . '1';
        }
    }
    return $frekuensi;
}

/**
 * Dapatkan nama database berdasarkan IP user
 */
function GetUserDatabase(): string
{
    $cDatabase = GetSetting('cSession_SystemDatabase');

    $cFile = BASE_PATH . '/include/.userdata/' . md5(GetIP()) . '.php';
    if (is_file($cFile)) {
        $__cDatabase = '';
        include $cFile;
        if (!empty($__cDatabase)) {
            $cDatabase = $__cDatabase;
        }
    }
    return $cDatabase;
}

/**
 * Dapatkan IP address pengirim request
 */
function GetIP(): string
{
    if (!empty($_SERVER['HTTP_CF_CONNECTING_IP'])) {
        return $_SERVER['HTTP_CF_CONNECTING_IP'];
    }
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

/**
 * Ambil konfigurasi dari database
 */
function aCfg(string $key, mixed $default = ''): mixed
{
    $sessionPrefix = 'mssession_';
    if (strtolower(substr($key, 0, strlen($sessionPrefix))) === $sessionPrefix) {
        $key = GetSetting('cSession_UserName') . $key;
    }

    $rows = objData::Browse('Config', 'Keterangan', "Kode = '$key'");
    if ($row = objData::GetRow($rows)) {
        $default = $row['Keterangan'];
    }

    if (strtolower($key) === 'mskodecabang' && trim(GetSetting('cSession_Cabang')) !== '') {
        $default = GetSetting('cSession_Cabang');
    }

    return $default;
}

/**
 * Update konfigurasi di database
 */
function UpdCfg(string $key, mixed $value): void
{
    objData::Update('config', ['Kode' => $key, 'Keterangan' => $value], "Kode = '$key'");
}

/**
 * Minify/normalize JSON string
 */
function minify(string $json): string
{
    $decoded = json_decode($json, true);
    if ($decoded === null && json_last_error() !== JSON_ERROR_NONE) {
        return $json;
    }
    return json_encode($decoded) ?: $json;
}

/**
 * Logging ke file
 */
function writeLog(string $type, string $message, string $context = ''): void
{
    $logDir  = BASE_PATH . '/logs';
    $logFile = $logDir . '/' . date('Y-m-d') . '.log';

    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }

    $ip   = GetIP();
    $time = date('Y-m-d H:i:s');
    $line = "[{$time}] [{$type}] IP:{$ip} | {$message}";
    if (!empty($context)) {
        $line .= ' | ' . $context;
    }
    $line .= PHP_EOL;

    file_put_contents($logFile, $line, FILE_APPEND | LOCK_EX);
}
