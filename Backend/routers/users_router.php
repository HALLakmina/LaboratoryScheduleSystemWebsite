<?php
namespace Backend\Routers;
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../controllers/users_controller.php";
require_once __DIR__ . "/../middleware/validation.php";
require_once __DIR__ . "/../middleware/jwtToken.php";
use Backend\Services\UsersService;
use Backend\Controllers\UsersController;
use Backend\Utils\Route;
use Backend\Middleware\Validation;
use Backend\Middleware\JwtToken;
use Exception;

class UsersRouter {
    private $usersService;
    private $usersController;
    private $router;
    private $validation;

    public function __construct() {
        $this->router = Route::getInstance();
        $this->usersService = new UsersService();
        $this->usersController = new UsersController();
        $this->validation = new Validation();
        $this->routeService();
        $this->router->dispatch();
    }

    private function routeService(): void {
        $this->router->get('/', function ($req = null, $res = null) {
            $this->usersController->getAll($req, $res);
        });

        $validationMiddleware = function ($req = null, $res = null) {
            $this->validation->userBodyDataValidation($req);
        };
        $authorMiddleware = function ($req = null, $res = null) {
            $jwt = new JwtToken();
            $jwt->getJwtToken($req);
        };

        $this->router->post('/', [$validationMiddleware, $authorMiddleware], function ($req = null, $res = null) {
            $this->usersController->create($req, $res);
        });

        $this->router->post('/login', function ($req = null, $res = null) {
            $this->usersController->login($req, $res);
        });

        $this->router->post('/logout', function ($req = null, $res = null) {
            $this->usersController->logout($req, $res);
        });
    }
}
new UsersRouter;
?>
