<?php
namespace Backend\Controllers;

require_once __DIR__ . "/../services/timetable_service.php";

use Backend\Services\TimetableService;
use Backend\Utils\Route;
use Exception;

class TimetableController {
    private $timetableService;

    public function __construct() {
        $this->timetableService = new TimetableService();
    }

    private function jsonResponse($status, $message, $data = null) {
        echo json_encode([
            "status" => $status,
            "data" => $data,
            "message" => $message,
        ]);
        exit;
    }

    private function normalizeNullableValue($value) {
        return trim((string)($value ?? '')) === '' ? null : $value;
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    public function getAllTimeSchedules($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getAllTimeSchedules();
            $this->jsonResponse("200", 'Data get Successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getTimeSchedulesByYear($req = null, $res = null) {
        $year = $req['query']['year'] ?? '';
        try {
            $respond = $this->timetableService->getTimeSchedulesByYear($year);
            $this->jsonResponse("200", 'Data get Successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getTemporaryTimeSchedules($req = null, $res = null) {
        $dateFrom = $req['query']['date_from'] ?? null;
        $dateTo = $req['query']['date_to'] ?? null;

        try {
            $respond = $this->timetableService->getTemporaryTimeSchedules($dateFrom, $dateTo);
            $this->jsonResponse("200", 'Temporary timetable fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getSubjectCodes($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getSubjectCodes();
            $this->jsonResponse("200", 'Subject codes fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getYears($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getYears();
            $this->jsonResponse("200", 'Years fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createYear($payload);
            $this->jsonResponse("200", 'Year created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateYear($payload);
            $this->jsonResponse("200", 'Year updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteYear($payload['id']);
            $this->jsonResponse("200", 'Year deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getTimeSlots($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimeSlots();
            $this->jsonResponse("200", 'Time slots fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getColumnHeadings($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getColumnHeadings();
            $this->jsonResponse("200", 'Column headings fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getTimetableSettings($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimetableSettings();
            $this->jsonResponse("200", 'Timetable settings fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getLectureGroups($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getLectureGroups();
            $this->jsonResponse("200", 'Lecture groups fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createLectureGroup($payload);
            $this->jsonResponse("200", 'Group created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateLectureGroup($payload);
            $this->jsonResponse("200", 'Group updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteLectureGroup($payload['id']);
            $this->jsonResponse("200", 'Group deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getLabs($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getLabs();
            $this->jsonResponse("200", 'Labs fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createLab($payload);
            $this->jsonResponse("200", 'Lab created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateLab($payload);
            $this->jsonResponse("200", 'Lab updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteLab($payload['id']);
            $this->jsonResponse("200", 'Lab deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function getTimetableCells($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimetableCells();
            $this->jsonResponse("200", 'Timetable cells fetched successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $payload['time_slot_id'] = $this->normalizeNullableValue($payload['time_slot_id'] ?? null);
            $payload['column_heading_id'] = $this->normalizeNullableValue($payload['column_heading_id'] ?? null);
            $payload['lecture_group_id'] = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id'] = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord'] = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createTimetableRecord($payload);
            $this->jsonResponse("200", 'Timetable record created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $payload['time_slot_id'] = $this->normalizeNullableValue($payload['time_slot_id'] ?? null);
            $payload['column_heading_id'] = $this->normalizeNullableValue($payload['column_heading_id'] ?? null);
            $payload['lecture_group_id'] = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id'] = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord'] = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateTimetableRecord($payload);
            $this->jsonResponse("200", 'Timetable record updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteTimetableRecord($payload['id']);
            $this->jsonResponse("200", 'Timetable record deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimetableSettings($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $rowCount = (int)$payload['table_row_count'];
            $columnCount = (int)$payload['table_column_count'];
            $breakRowNumber = (int)$payload['break_row_number'];
            $activeRows = $breakRowNumber > 0 ? max($rowCount - 1, 0) : $rowCount;
            $payload['table_cell_count'] = $activeRows * $columnCount;
            $payload['break_cell_ids'] = '';

            $respond = $this->timetableService->updateTimetableSettings($payload);
            $this->jsonResponse("200", 'Timetable settings updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function resetTimetableSettings($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->resetTimetableSettings($payload);
            $this->jsonResponse("200", 'Timetable settings reset successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createColumnHeading($payload);
            $this->jsonResponse("200", 'Column heading created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateColumnHeading($payload);
            $this->jsonResponse("200", 'Column heading updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteColumnHeading($payload['id']);
            $this->jsonResponse("200", 'Column heading deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createTimeSlot($payload);
            $this->jsonResponse("200", 'Time slot created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateTimeSlot($payload);
            $this->jsonResponse("200", 'Time slot updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteTimeSlot($payload['id']);
            $this->jsonResponse("200", 'Time slot deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->createSubject($payload);
            $this->jsonResponse("200", 'Subject created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $respond = $this->timetableService->updateSubject($payload);
            $this->jsonResponse("200", 'Subject updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->timetableService->deleteSubject($payload['id']);
            $this->jsonResponse("200", 'Subject deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }
}
?>
