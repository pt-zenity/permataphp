<?php
/**
 * Permata Switching - Pure PHP (No Framework)
 * Entry Point / Front Controller
 * Version: 1.0
 */

define('ROOT_PATH', __DIR__);
define('BASE_PATH', __DIR__);
define('main', true);

date_default_timezone_set('Asia/Jakarta');

// Load core
require_once ROOT_PATH . '/include/database.php';
require_once ROOT_PATH . '/include/router.php';

// Run router
$router = new Router();
$router->dispatch();
