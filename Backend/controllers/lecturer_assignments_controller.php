<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/lecturer_assignments_service.php';
require_once __DIR__ . '/../utils/route.php';

use Backend\Services\LecturerAssignmentsService;
use Backend\Utils\Route;
use Exception;

class LecturerAssignmentsController {
    private $service;

    public function __construct() {
        $this->service = new LecturerAssignmentsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    private function jsonResponse($status, $message, $data = null) {
        http_response_code((int)$status);
        echo json_encode(['status' => (string)$status, 'data' => $data, 'message' => $message]);
        exit;
    }

    // ── Responsibilities ──────────────────────────────────────────────

    public function getResponsibilities($req = null, $res = null) {
        try {
            $this->jsonResponse('200', 'Responsibilities fetched successfully', $this->service->getResponsibilities());
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function createResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? '';
            $payload['updated_by'] = $actor['userName'] ?? '';
            $this->jsonResponse('200', $this->service->createResponsibility($payload));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function updateResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? '';
            $this->jsonResponse('200', $this->service->updateResponsibility($payload));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function deleteResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $this->jsonResponse('200', $this->service->deleteResponsibility($payload['id']));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    // ── Assignments ───────────────────────────────────────────────────

    public function getAssignments($req = null, $res = null) {
        try {
            $this->jsonResponse('200', 'Assignments fetched successfully', $this->service->getAssignments());
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function createAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['assigned_by'] = $actor['userName'] ?? '';
            $this->jsonResponse('200', $this->service->createAssignment($payload));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function updateAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['assigned_by'] = $actor['userName'] ?? '';
            $this->jsonResponse('200', $this->service->updateAssignment($payload));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function deleteAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $this->jsonResponse('200', $this->service->deleteAssignment($payload['id']));
        } catch (Exception $e) {
            error_log('[LecturerAssignmentsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }
}
?>
