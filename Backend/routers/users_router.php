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
        $authorMiddleware = function ($req = null, $res = null) {
            $jwt = new JwtToken();
            $jwt->validateToken($req, $res);
        };
        $adminMiddleware = JwtToken::requireRole('admin');

        $this->router->get('/', [$authorMiddleware, $adminMiddleware], function ($req = null, $res = null) {
            $this->usersController->getAll($req, $res);
        });

        $userCreateValidation = function ($req = null, $res = null) {
            $this->validation->userCreate($req, $res);
        };

        $userUpdateValidation = function ($req = null, $res = null) {
            $this->validation->userUpdate($req, $res);
        };

        $userDeleteValidation = function ($req = null, $res = null) {
            $this->validation->userDelete($req, $res);
        };

        $userResetPasswordValidation = function ($req = null, $res = null) {
            $this->validation->userResetPassword($req, $res);
        };

        $userLoginValidation = function ($req = null, $res = null) {
            $this->validation->userLogin($req, $res);
        };

        $this->router->post('/', [$authorMiddleware, $adminMiddleware, $userCreateValidation], function ($req = null, $res = null) {
            $this->usersController->create($req, $res);
        });

        $this->router->post('/update', [$authorMiddleware, $adminMiddleware, $userUpdateValidation], function ($req = null, $res = null) {
            $this->usersController->update($req, $res);
        });

        $this->router->post('/delete', [$authorMiddleware, $adminMiddleware, $userDeleteValidation], function ($req = null, $res = null) {
            $this->usersController->delete($req, $res);
        });

        $this->router->post('/reset-password', [$authorMiddleware, $adminMiddleware, $userResetPasswordValidation], function ($req = null, $res = null) {
            $this->usersController->resetPassword($req, $res);
        });

        $this->router->post('/login', [$userLoginValidation], function ($req = null, $res = null) {
            $this->usersController->login($req, $res);
        });

        $this->router->post('/logout', function ($req = null, $res = null) {
            $this->usersController->logout($req, $res);
        });
    }
}
new UsersRouter;
?>
