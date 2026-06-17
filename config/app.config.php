<?php
/**
 * Configuration File
 * Sesuaikan nilai di sini untuk environment produksi
 *
 * JANGAN commit file ini ke git jika berisi credential!
 * Sebaiknya gunakan environment variable atau file .env
 */
defined('main') or die('Restricted access');

// ── Database ─────────────────────────────────────────────────────────────────
$config['db_host']     = getenv('DB_HOST')     ?: 'localhost';
$config['db_port']     = (int)(getenv('DB_PORT') ?: 3306);
$config['db_name']     = getenv('DB_NAME')     ?: 'bpr_web';
$config['db_user']     = getenv('DB_USER')     ?: 'Assist';
$config['db_pass']     = getenv('DB_PASS')     ?: 'Irac';

// ── IP Filtering ──────────────────────────────────────────────────────────────
// Set true untuk mengaktifkan whitelist IP
$config['global_ip_allowed_enable'] = false;
// Daftar IP yang diizinkan, pisah dengan koma: '192.168.1.1, 10.0.0.1'
$config['global_ip_allowed']        = '';

// Set true untuk mengaktifkan blacklist IP
$config['global_ip_blacklist_enabled'] = false;
// Daftar IP yang diblokir, pisah dengan koma
$config['global_ip_blacklist']         = '';

// ── Authentication ────────────────────────────────────────────────────────────
// 'class'      = gunakan class MVC_Authentication di config/global_aut.php
// 'global_key' = gunakan global key
// 'none'       = tanpa autentikasi
$config['global_authentication_type'] = 'class';

// ── REST API Config ───────────────────────────────────────────────────────────
$config['rest_ip_allowed_enable']    = false;
$config['rest_ip_allowed']           = '';
$config['rest_ip_blacklist_enabled'] = false;
$config['rest_ip_blacklist']         = '';
$config['rest_authentication_type']  = 'none';

// ── Application ───────────────────────────────────────────────────────────────
$config['app_name']    = 'Permata Switching';
$config['app_version'] = '1.0';
$config['app_env']     = getenv('APP_ENV') ?: 'production';
$config['app_debug']   = filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN);

// ── Logging ───────────────────────────────────────────────────────────────────
$config['log_path']    = BASE_PATH . '/logs';
$config['log_enabled'] = true;

// ── CDS (Central Data Store) ──────────────────────────────────────────────────
$config['cds_url']     = getenv('CDS_URL') ?: 'cds.sis1.net/cds/public/cds/json';

return $config;
