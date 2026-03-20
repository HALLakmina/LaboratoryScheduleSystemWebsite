<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/lecturer_requests_controller.php';

use Backend\Controllers\LecturerRequestsController;
use Backend\Utils\Route;

class LecturerRequestsRouter {
    private $lecturerRequestsController;
    private $router;

    public function __construct() {
        $this->router = Route::getInstance();
        $this->lecturerRequestsController = new LecturerRequestsController();
        $this->routeLecturerRequests();
        $this->router->dispatch();
    }

    private function routeLecturerRequests() {
        $this->router->get('/', function ($req = null, $res = null) {
            $this->lecturerRequestsController->getAll($req, $res);
        });

        $this->router->post('/', function ($req = null, $res = null) {
            $this->lecturerRequestsController->create($req, $res);
        });

        $this->router->post('/update', function ($req = null, $res = null) {
            $this->lecturerRequestsController->update($req, $res);
        });

        $this->router->post('/delete', function ($req = null, $res = null) {
            $this->lecturerRequestsController->delete($req, $res);
        });
    }
}

new LecturerRequestsRouter;
?>
