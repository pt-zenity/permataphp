<?php
/**
 * Router - Pure PHP tanpa framework
 * Menangani routing berdasarkan REQUEST_URI
 */
defined('main') or die('Restricted access');

class Router
{
    private array $routes = [];

    public function __construct()
    {
        $this->registerRoutes();
    }

    private function registerRoutes(): void
    {
        // Format: [method, uri_pattern, controller_file, class, method]
        $this->routes = [
            // B2B Token
            ['POST', '/access-token/b2b',      'mvc/b2b/b2b.controller.php',           'B2bController',     'index'],
            // Inquiry VA
            ['POST', '/transfer-va/inquiry',    'mvc/inquiry/inquiry.controller.php',   'InquiryController', 'index'],
            // Payment VA
            ['POST', '/transfer-va/payment',    'mvc/payment/payment.controller.php',   'PaymentController', 'index'],
            // Default / health check
            ['GET',  '/',                       null,                                   null,                null],
        ];
    }

    public function dispatch(): void
    {
        $method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri    = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
        $uri    = '/' . trim($uri, '/');
        if ($uri === '') $uri = '/';

        // Handle OPTIONS (CORS preflight)
        if ($method === 'OPTIONS') {
            $this->sendCorsHeaders();
            http_response_code(200);
            exit;
        }

        foreach ($this->routes as [$routeMethod, $pattern, $file, $class, $action]) {
            if ($method !== $routeMethod && $routeMethod !== 'ANY') continue;
            if ($uri !== $pattern) continue;

            // Default / health check
            if ($file === null) {
                $this->sendCorsHeaders();
                header('Content-Type: application/json');
                echo json_encode(['status' => 'ok', 'service' => 'permata-switching', 'version' => '1.0']);
                return;
            }

            $filePath = BASE_PATH . '/' . $file;
            if (!is_file($filePath)) {
                $this->sendError(500, 'Controller file not found: ' . $file);
                return;
            }

            require_once $filePath;

            if (!class_exists($class)) {
                $this->sendError(500, "Controller class '$class' not found");
                return;
            }

            $controller = new $class();
            if (!method_exists($controller, $action)) {
                $this->sendError(500, "Method '$action' not found in '$class'");
                return;
            }

            $this->sendCorsHeaders();
            $controller->$action();
            return;
        }

        // 404 - route not found
        $this->sendCorsHeaders();
        header('Content-Type: application/json');
        http_response_code(404);
        echo json_encode(['responseCode' => '404', 'responseMessage' => 'Endpoint not found']);
    }

    private function sendCorsHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: authorization, content-type, x-timestamp, x-signature, x-partner-id, x-external-id, channel-id, x-client-key, x-xsrf-token, Cache-Control, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');
    }

    private function sendError(int $code, string $message): void
    {
        header('Content-Type: application/json');
        http_response_code($code);
        echo json_encode(['responseCode' => (string)$code, 'responseMessage' => $message]);
    }
}
