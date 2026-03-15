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
            $requiredFields = [
                'lecturer_id',
                'subject_id',
                'year_id',
                'timetable_time_slot_id',
                'timetable_column_heading_id',
                'lecturer_request',
            ];

            foreach ($requiredFields as $field) {
                if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                    echo json_encode([
                        'status' => '400',
                        'message' => $field . ' is required.',
                    ]);
                    exit;
                }
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
}
?>
