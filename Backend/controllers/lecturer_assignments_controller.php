<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/lecturer_assignments_service.php';
require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . '/../utils/route.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/response.php';

use Backend\Services\LecturerAssignmentsService;
use Backend\Services\LogsService;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Backend\Utils\Response;
use Exception;

class LecturerAssignmentsController {
    private $service;
    private $logsService;

    public function __construct() {
        $this->service     = new LecturerAssignmentsService();
        $this->logsService = new LogsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    private function jsonResponse($status, $message, $data = null) {
        Response::send((string)$status, $message, $data);
    }

    private function dbLog(string $type, string $table, $old, $new): void {
        $actor = $this->getAuthUser();
        $this->logsService->logAction($type, $table, $old, $new, isset($actor['userId']) ? (int)$actor['userId'] : null);
    }

    // ── Responsibilities ──────────────────────────────────────────────

    public function getResponsibilities($req = null, $res = null) {
        try {
            $data = $this->service->getResponsibilities();
            Logger::info('[LecturerAssignmentsController::getResponsibilities]', ['count' => count($data)]);
            $this->jsonResponse('200', 'Responsibilities fetched successfully', $data);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function createResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? '';
            $payload['updated_by'] = $actor['userName'] ?? '';
            $message = $this->service->createResponsibility($payload);
            $this->dbLog('INSERT', 'lecturer_responsibility', null, $payload);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function updateResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? '';
            $old = $this->logsService->fetchRowById('lecturer_responsibility', $payload['id']);
            $message = $this->service->updateResponsibility($payload);
            $this->dbLog('UPDATE', 'lecturer_responsibility', $old, $payload);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function deleteResponsibility($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('lecturer_responsibility', $payload['id']);
            $message = $this->service->deleteResponsibility($payload['id']);
            $this->dbLog('DELETE', 'lecturer_responsibility', $old, null);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    // ── Assignments ───────────────────────────────────────────────────

    public function getAssignments($req = null, $res = null) {
        try {
            $data = $this->service->getAssignments();
            Logger::info('[LecturerAssignmentsController::getAssignments]', ['count' => count($data)]);
            $this->jsonResponse('200', 'Assignments fetched successfully', $data);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function createAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['assigned_by'] = $actor['userName'] ?? '';
            $message = $this->service->createAssignment($payload);
            $this->dbLog('INSERT', 'subject_lecture_relations', null, $payload);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function updateAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['assigned_by'] = $actor['userName'] ?? '';
            $old = $this->logsService->fetchRowById('subject_lecture_relations', $payload['id']);
            $message = $this->service->updateAssignment($payload);
            $this->dbLog('UPDATE', 'subject_lecture_relations', $old, $payload);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }

    public function deleteAssignment($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('subject_lecture_relations', $payload['id']);
            $message = $this->service->deleteAssignment($payload['id']);
            $this->dbLog('DELETE', 'subject_lecture_relations', $old, null);
            $this->jsonResponse('200', $message);
        } catch (Exception $e) {
            Logger::error('[LecturerAssignmentsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }
}
?>
