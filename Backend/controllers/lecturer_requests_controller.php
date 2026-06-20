<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/lecturer_requests_service.php';
require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/response.php';

use Backend\Services\LecturerRequestsService;
use Backend\Services\LogsService;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Backend\Utils\Response;
use Exception;

class LecturerRequestsController {
    private $lecturerRequestsService;
    private $logsService;

    public function __construct() {
        $this->lecturerRequestsService = new LecturerRequestsService();
        $this->logsService             = new LogsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    private function dbLog(string $type, string $table, $old, $new): void {
        $actor = $this->getAuthUser();
        $this->logsService->logAction($type, $table, $old, $new, isset($actor['userId']) ? (int)$actor['userId'] : null);
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->lecturerRequestsService->create($payload);
            $this->dbLog('INSERT', 'lecturer_requests', null, $payload);
            Response::success('Lecturer request sent successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[LecturerRequestsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function getAll($req = null, $res = null) {
        try {
            $respond = $this->lecturerRequestsService->getAll();
            $actor = $this->getAuthUser();
            Logger::info('[LecturerRequestsController::getAll]', ['user' => $actor['userName'] ?? 'anonymous', 'count' => count($respond)]);
            Response::success('Lecturer requests fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[LecturerRequestsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('lecturer_requests', $payload['id'] ?? null);
            $respond = $this->lecturerRequestsService->update($payload);
            $this->dbLog('UPDATE', 'lecturer_requests', $old, $payload);
            Response::success('Lecturer request updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[LecturerRequestsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('lecturer_requests', $payload['id'] ?? null);
            $respond = $this->lecturerRequestsService->delete($payload['id']);
            $this->dbLog('DELETE', 'lecturer_requests', $old, null);
            Response::success('Lecturer request deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[LecturerRequestsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function checkAvailability($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $requiredFields = ['timetable_time_slot_id', 'timetable_column_heading_id', 'date'];
            foreach ($requiredFields as $field) {
                if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                    Response::error('400', $field . ' is required.');
                }
            }
            $respond = $this->lecturerRequestsService->checkTemporaryTimetableAvailability($payload);
            Response::success(
                $respond['is_booked']
                    ? 'Lecture request date is already booked.'
                    : 'Lecture request date is available.',
                $respond
            );
        } catch (Exception $e) {
            Logger::error('[LecturerRequestsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }
}
?>
