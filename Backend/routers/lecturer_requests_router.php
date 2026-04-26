<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/lecturer_requests_controller.php';
require_once __DIR__ . '/../middleware/validation.php';

use Backend\Controllers\LecturerRequestsController;
use Backend\Middleware\Validation;
use Backend\Utils\Route;

class LecturerRequestsRouter {
    private $lecturerRequestsController;
    private $router;
    private $validation;

    public function __construct() {
        $this->router = Route::getInstance();
        $this->lecturerRequestsController = new LecturerRequestsController();
        $this->validation = new Validation();
        $this->routeLecturerRequests();
        $this->router->dispatch();
    }

    private function routeLecturerRequests() {
        $createValidation = function ($req = null, $res = null) {
            $this->validation->lecturerRequestCreate($req, $res);
        };

        $updateValidation = function ($req = null, $res = null) {
            $this->validation->lecturerRequestUpdate($req, $res);
        };

        $deleteValidation = function ($req = null, $res = null) {
            $this->validation->lecturerRequestDelete($req, $res);
        };

        $this->router->get('/', function ($req = null, $res = null) {
            $this->lecturerRequestsController->getAll($req, $res);
        });

        $this->router->post('/', [$createValidation], function ($req = null, $res = null) {
            $this->lecturerRequestsController->create($req, $res);
        });

        $this->router->post('/update', [$updateValidation], function ($req = null, $res = null) {
            $this->lecturerRequestsController->update($req, $res);
        });

        $this->router->post('/check-availability', function ($req = null, $res = null) {
            $this->lecturerRequestsController->checkAvailability($req, $res);
        });

        $this->router->post('/delete', [$deleteValidation], function ($req = null, $res = null) {
            $this->lecturerRequestsController->delete($req, $res);
        });
    }
}

new LecturerRequestsRouter;
?>
