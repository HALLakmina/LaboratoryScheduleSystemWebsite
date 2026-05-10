<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/lecturer_requests_service.php';

use Backend\Services\LecturerRequestsService;
use Backend\Utils\Route;
use Exception;

class LecturerRequestsController {
    private $lecturerRequestsService;

    public function __construct() {
        $this->lecturerRequestsService = new LecturerRequestsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->lecturerRequestsService->create($payload);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request sent successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[LecturerRequestsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function getAll($req = null, $res = null) {
        try {
            $respond = $this->lecturerRequestsService->getAll();
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer requests fetched successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[LecturerRequestsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->lecturerRequestsService->update($payload);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request updated successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[LecturerRequestsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->lecturerRequestsService->delete($payload['id']);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request deleted successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[LecturerRequestsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function checkAvailability($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $requiredFields = ['timetable_time_slot_id', 'timetable_column_heading_id', 'date'];

            foreach ($requiredFields as $field) {
                if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                    http_response_code(400);
                    echo json_encode([
                        'status' => '400',
                        'message' => $field . ' is required.',
                    ]);
                    exit;
                }
            }

            $respond = $this->lecturerRequestsService->checkTemporaryTimetableAvailability($payload);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => $respond['is_booked'] ? 'Lecture request date is already booked.' : 'Lecture request date is available.'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[LecturerRequestsController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

}
?>
