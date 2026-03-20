<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/news_controller.php';

use Backend\Controllers\NewsController;
use Backend\Utils\Route;

class NewsRouter {
    private $newsController;
    private $router;

    public function __construct() {
        $this->router = Route::getInstance();
        $this->newsController = new NewsController();
        $this->routeNews();
        $this->router->dispatch();
    }

    private function routeNews() {
        $this->router->get('/', function ($req = null, $res = null) {
            $this->newsController->getAll($req, $res);
        });

        $this->router->get('/byId', function ($req = null, $res = null) {
            $this->newsController->getById($req, $res);
        });

        $this->router->post('/', function ($req = null, $res = null) {
            $this->newsController->create($req, $res);
        });

        $this->router->post('/update', function ($req = null, $res = null) {
            $this->newsController->update($req, $res);
        });

        $this->router->post('/delete', function ($req = null, $res = null) {
            $this->newsController->delete($req, $res);
        });
    }
}

new NewsRouter;
?>
