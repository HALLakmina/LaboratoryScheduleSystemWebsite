<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/logs_controller.php';
require_once __DIR__ . '/../middleware/jwtToken.php';

use Backend\Controllers\LogsController;
use Backend\Middleware\JwtToken;
use Backend\Utils\Route;

class LogsRouter {
    private $controller;
    private $router;

    public function __construct() {
        $this->router     = Route::getInstance();
        $this->controller = new LogsController();
        $this->registerRoutes();
        $this->router->dispatch();
    }

    private function registerRoutes() {
        $authorMiddleware = function ($req = null, $res = null) {
            (new JwtToken())->validateToken($req, $res);
        };
        $adminMiddleware = JwtToken::requireRole('admin');

        $this->router->get('/action-logs', [$authorMiddleware, $adminMiddleware], function ($req = null, $res = null) {
            $this->controller->getActionLogs($req, $res);
        });
    }
}

new LogsRouter;
?>
