<?php
namespace Backend;

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/utils/route.php';

use Backend\Utils\Route;
use Dotenv\Dotenv;
use Exception;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

// CORS — reflect origin only when it is in the configured allowlist.
$allowedOrigins = array_filter(
    array_map('trim', explode(',', $_ENV['ALLOWED_ORIGINS'] ?? ''))
);
$requestOrigin = $_SERVER['HTTP_ORIGIN'] ?? '';

if ($requestOrigin !== '' && in_array($requestOrigin, $allowedOrigins, true)) {
    header('Access-Control-Allow-Origin: ' . $requestOrigin);
    header('Access-Control-Allow-Credentials: true');
    header('Vary: Origin');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle OPTIONS preflight — no further processing needed.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

class Server {
    public function __construct() {
        $this->server();
    }

    private static function server() {
        $router = Route::getInstance();
        try {
            $router->accessEndpoint('/api/v1/user', 'routers/users_router.php');
            $router->accessEndpoint('/api/v1/timetable', 'routers/timetable_router.php');
            $router->accessEndpoint('/api/v1/lecturer-request', 'routers/lecturer_requests_router.php');
            $router->accessEndpoint('/api/v1/news', 'routers/news_router.php');
            http_response_code(404);
            echo json_encode(['status' => '404', 'message' => 'Endpoint not found']);
            exit;
        } catch (Exception $e) {
            error_log('[API] Unhandled exception: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
        }
    }
}

new Server;
