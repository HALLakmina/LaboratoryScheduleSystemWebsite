<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/news_controller.php';
require_once __DIR__ . '/../middleware/validation.php';

use Backend\Controllers\NewsController;
use Backend\Middleware\Validation;
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

        $this->router->post('/', [$createValidation], function ($req = null, $res = null) {
            $this->newsController->create($req, $res);
        });

        $this->router->post('/update', [$updateValidation], function ($req = null, $res = null) {
            $this->newsController->update($req, $res);
        });

        $this->router->post('/delete', [$deleteValidation], function ($req = null, $res = null) {
            $this->newsController->delete($req, $res);
        });
    }
}

new NewsRouter;
?>
