<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/lecturer_requests_service.php';

use Backend\Services\LecturerRequestsService;
use Exception;

class LecturerRequestsController {
    private $lecturerRequestsService;

    public function __construct() {
        $this->lecturerRequestsService = new LecturerRequestsService();
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $validationMessage = $this->validatePayload($payload);
            if ($validationMessage !== null) {
                echo json_encode([
                    'status' => '400',
                    'message' => $validationMessage,
                ]);
                exit;
            }

            $respond = $this->lecturerRequestsService->create($payload);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request sent successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
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
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.',
                ]);
                exit;
            }

            $validationMessage = $this->validatePayload($payload);
            if ($validationMessage !== null) {
                echo json_encode([
                    'status' => '400',
                    'message' => $validationMessage,
                ]);
                exit;
            }

            $respond = $this->lecturerRequestsService->update($payload);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request updated successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.',
                ]);
                exit;
            }

            $respond = $this->lecturerRequestsService->delete($payload['id']);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Lecturer request deleted successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    private function validatePayload($payload) {
        $requiredFields = [
            'lecturer_id',
            'subject_id',
            'year_id',
            'timetable_time_slot_id',
            'timetable_column_heading_id',
            'date',
            'lecturer_request',
        ];

        foreach ($requiredFields as $field) {
            if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                return $field . ' is required.';
            }
        }

        $requestDate = strtotime((string)$payload['date']);
        $today = strtotime(date('Y-m-d'));
        if ($requestDate === false || $requestDate < $today) {
            return 'date must be today or a future date.';
        }

        if (isset($payload['action']) && !in_array($payload['action'], ['requested', 'confirmed', 'canceled'], true)) {
            return 'action must be requested, confirmed, or canceled.';
        }

        if (($payload['action'] ?? '') === 'confirmed' && trim((string)($payload['lab_id'] ?? '')) === '') {
            return 'lab_id is required when confirming a lecturer request.';
        }

        return null;
    }
}
?>
