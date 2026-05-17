<?php
namespace Backend\Routers;

require_once __DIR__ . '/../controllers/lecturer_assignments_controller.php';
require_once __DIR__ . '/../middleware/validation.php';
require_once __DIR__ . '/../middleware/jwtToken.php';

use Backend\Controllers\LecturerAssignmentsController;
use Backend\Middleware\Validation;
use Backend\Middleware\JwtToken;
use Backend\Utils\Route;

class LecturerAssignmentsRouter {
    private $controller;
    private $router;
    private $validation;

    public function __construct() {
        $this->router     = Route::getInstance();
        $this->controller = new LecturerAssignmentsController();
        $this->validation = new Validation();
        $this->registerRoutes();
        $this->router->dispatch();
    }

    private function registerRoutes() {
        $authorMiddleware = function ($req = null, $res = null) {
            (new JwtToken())->validateToken($req, $res);
        };
        $adminMiddleware = JwtToken::requireRole('admin');

        $responsibilityCreateValidation = function ($req = null, $res = null) {
            $this->validation->responsibilityCreate($req, $res);
        };
        $responsibilityUpdateValidation = function ($req = null, $res = null) {
            $this->validation->responsibilityUpdate($req, $res);
        };
        $deleteByIdValidation = function ($req = null, $res = null) {
            $this->validation->deleteById($req, $res);
        };
        $assignmentCreateValidation = function ($req = null, $res = null) {
            $this->validation->assignmentCreate($req, $res);
        };
        $assignmentUpdateValidation = function ($req = null, $res = null) {
            $this->validation->assignmentUpdate($req, $res);
        };

        // Responsibilities
        $this->router->get('/responsibilities', function ($req = null, $res = null) {
            $this->controller->getResponsibilities($req, $res);
        });
        $this->router->post('/responsibilities', [$authorMiddleware, $adminMiddleware, $responsibilityCreateValidation], function ($req = null, $res = null) {
            $this->controller->createResponsibility($req, $res);
        });
        $this->router->post('/responsibilities/update', [$authorMiddleware, $adminMiddleware, $responsibilityUpdateValidation], function ($req = null, $res = null) {
            $this->controller->updateResponsibility($req, $res);
        });
        $this->router->post('/responsibilities/delete', [$authorMiddleware, $adminMiddleware, $deleteByIdValidation], function ($req = null, $res = null) {
            $this->controller->deleteResponsibility($req, $res);
        });

        // Assignments
        $this->router->get('/assignments', function ($req = null, $res = null) {
            $this->controller->getAssignments($req, $res);
        });
        $this->router->post('/assignments', [$authorMiddleware, $adminMiddleware, $assignmentCreateValidation], function ($req = null, $res = null) {
            $this->controller->createAssignment($req, $res);
        });
        $this->router->post('/assignments/update', [$authorMiddleware, $adminMiddleware, $assignmentUpdateValidation], function ($req = null, $res = null) {
            $this->controller->updateAssignment($req, $res);
        });
        $this->router->post('/assignments/delete', [$authorMiddleware, $adminMiddleware, $deleteByIdValidation], function ($req = null, $res = null) {
            $this->controller->deleteAssignment($req, $res);
        });
    }
}

new LecturerAssignmentsRouter;
?>
