<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/news_controller.php';
require_once __DIR__ . '/../middleware/validation.php';
require_once __DIR__ . '/../middleware/jwtToken.php';

use Backend\Controllers\NewsController;
use Backend\Middleware\Validation;
use Backend\Middleware\JwtToken;
use Backend\Utils\Route;

class NewsRouter {
    private $newsController;
    private $router;
    private $validation;

    public function __construct() {
        $this->router = Route::getInstance();
        $this->newsController = new NewsController();
        $this->validation = new Validation();
        $this->routeNews();
        $this->router->dispatch();
    }

    private function routeNews() {
        $authorMiddleware = function ($req = null, $res = null) {
            (new JwtToken())->validateToken($req, $res);
        };

        $createValidation = function ($req = null, $res = null) {
            $this->validation->newsCreate($req, $res);
        };

        $updateValidation = function ($req = null, $res = null) {
            $this->validation->newsUpdate($req, $res);
        };

        $deleteValidation = function ($req = null, $res = null) {
            $this->validation->newsDelete($req, $res);
        };

        $this->router->get('/', function ($req = null, $res = null) {
            $this->newsController->getAll($req, $res);
        });

        $this->router->get('/byId', function ($req = null, $res = null) {
            $this->newsController->getById($req, $res);
        });

        $this->router->post('/', [$authorMiddleware, $createValidation], function ($req = null, $res = null) {
            $this->newsController->create($req, $res);
        });

        $this->router->post('/update', [$authorMiddleware, $updateValidation], function ($req = null, $res = null) {
            $this->newsController->update($req, $res);
        });

        $this->router->post('/delete', [$authorMiddleware, $deleteValidation], function ($req = null, $res = null) {
            $this->newsController->delete($req, $res);
        });
    }
}

new NewsRouter;
?>
