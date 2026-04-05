<?php
namespace Backend\Controllers;

require_once __DIR__ . "/../services/timetable_service.php";

use Backend\Services\TimetableService;
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

    private function validateTimetablePayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['cell_id']) || trim((string)$payload['cell_id']) === '') {
            return 'cell_id is required.';
        }

        if (!isset($payload['action']) || !in_array($payload['action'], ['active', 'free', 'cancel'], true)) {
            return 'action must be active, free, or cancel.';
        }

        return null;
    }

    private function validateTimetableSettingsPayload($payload) {
        $requiredFields = ['id', 'table_row_count', 'table_column_count', 'break_row_number'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                return $field . ' is required.';
            }
        }

        $rowCount = (int)$payload['table_row_count'];
        $columnCount = (int)$payload['table_column_count'];
        $breakRowNumber = (int)$payload['break_row_number'];

        if ($rowCount < 0 || $columnCount < 0) {
            return 'table_row_count and table_column_count must be zero or greater.';
        }

        if ($breakRowNumber < 0 || $breakRowNumber > $rowCount) {
            return 'break_row_number must be between 0 and table_row_count.';
        }

        if ($this->timetableService->countColumnHeadings() > $columnCount) {
            return 'Existing column heading count is greater than the new Columns value.';
        }

        if ($this->timetableService->countTimeSlots() > $rowCount) {
            return 'Existing time slot count is greater than the new Rows value.';
        }

        return null;
    }

    private function validateColumnHeadingPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['column_heading']) || trim((string)$payload['column_heading']) === '') {
            return 'column_heading is required.';
        }

        if (!isset($payload['column_number']) || trim((string)$payload['column_number']) === '') {
            return 'column_number is required.';
        }

        $settings = $this->timetableService->getTimetableSettings();
        $columnLimit = (int)($settings['table_column_count'] ?? 0);
        $columnNumber = (int)$payload['column_number'];

        if ($columnNumber < 1 || $columnNumber > $columnLimit) {
            return 'column_number must be between 1 and the timetable Columns value.';
        }

        if (!$requireId && $this->timetableService->countColumnHeadings() >= $columnLimit) {
            return 'Column heading count has reached the timetable Columns limit.';
        }

        $excludeId = $requireId ? $payload['id'] : null;
        if ($this->timetableService->isColumnNumberTaken($columnNumber, $excludeId)) {
            return 'column_number must be unique.';
        }

        return null;
    }

    private function validateTimeSlotPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['start_time']) || trim((string)$payload['start_time']) === '') {
            return 'start_time is required.';
        }

        if (!isset($payload['end_time']) || trim((string)$payload['end_time']) === '') {
            return 'end_time is required.';
        }

        if (strtotime('1970-01-01 ' . $payload['start_time']) >= strtotime('1970-01-01 ' . $payload['end_time'])) {
            return 'end_time must be later than start_time.';
        }

        $settings = $this->timetableService->getTimetableSettings();
        $rowLimit = (int)($settings['table_row_count'] ?? 0);
        if (!$requireId && $this->timetableService->countTimeSlots() >= $rowLimit) {
            return 'Time slot count has reached the timetable Rows limit.';
        }

        return null;
    }

    private function validateSubjectPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        $requiredFields = ['subject_cord', 'subject', 'year_id'];
        foreach ($requiredFields as $field) {
            if (!isset($payload[$field]) || trim((string)$payload[$field]) === '') {
                return $field . ' is required.';
            }
        }

        return null;
    }

    private function validateYearPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['year']) || trim((string)$payload['year']) === '') {
            return 'year is required.';
        }

        return null;
    }

    private function validateLectureGroupPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['group_name']) || trim((string)$payload['group_name']) === '') {
            return 'group_name is required.';
        }

        return null;
    }

    private function validateLabPayload($payload, $requireId = false) {
        if ($requireId && (!isset($payload['id']) || trim((string)$payload['id']) === '')) {
            return 'id is required.';
        }

        if (!isset($payload['lab_name']) || trim((string)$payload['lab_name']) === '') {
            return 'lab_name is required.';
        }

        if (!isset($payload['lab_location']) || trim((string)$payload['lab_location']) === '') {
            return 'lab_location is required.';
        }

        return null;
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
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateYearPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createYear($payload);
            $this->jsonResponse("200", 'Year created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateYear($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateYearPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateYear($payload);
            $this->jsonResponse("200", 'Year updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteYear($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

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
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateLectureGroupPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createLectureGroup($payload);
            $this->jsonResponse("200", 'Group created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateLectureGroup($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateLectureGroupPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateLectureGroup($payload);
            $this->jsonResponse("200", 'Group updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteLectureGroup($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

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
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateLabPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createLab($payload);
            $this->jsonResponse("200", 'Lab created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateLab($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateLabPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateLab($payload);
            $this->jsonResponse("200", 'Lab updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteLab($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

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
            $payload = $req['body'] ?? [];
            $payload['lecture_group_id'] = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id'] = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord'] = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateTimetablePayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createTimetableRecord($payload);
            $this->jsonResponse("200", 'Timetable record created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimetableRecord($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['lecture_group_id'] = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id'] = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord'] = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);

            $validationMessage = $this->validateTimetablePayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateTimetableRecord($payload);
            $this->jsonResponse("200", 'Timetable record updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteTimetableRecord($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

            $respond = $this->timetableService->deleteTimetableRecord($payload['id']);
            $this->jsonResponse("200", 'Timetable record deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimetableSettings($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateTimetableSettingsPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

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
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $respond = $this->timetableService->resetTimetableSettings($payload);
            $this->jsonResponse("200", 'Timetable settings reset successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createColumnHeading($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateColumnHeadingPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createColumnHeading($payload);
            $this->jsonResponse("200", 'Column heading created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateColumnHeading($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateColumnHeadingPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateColumnHeading($payload);
            $this->jsonResponse("200", 'Column heading updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteColumnHeading($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

            $respond = $this->timetableService->deleteColumnHeading($payload['id']);
            $this->jsonResponse("200", 'Column heading deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createTimeSlot($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateTimeSlotPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createTimeSlot($payload);
            $this->jsonResponse("200", 'Time slot created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateTimeSlot($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateTimeSlotPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateTimeSlot($payload);
            $this->jsonResponse("200", 'Time slot updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteTimeSlot($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

            $respond = $this->timetableService->deleteTimeSlot($payload['id']);
            $this->jsonResponse("200", 'Time slot deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function createSubject($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['created_by'] = $this->normalizeNullableValue($payload['created_by'] ?? null);
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateSubjectPayload($payload);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->createSubject($payload);
            $this->jsonResponse("200", 'Subject created successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function updateSubject($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $payload['updated_by'] = $this->normalizeNullableValue($payload['updated_by'] ?? null);
            $validationMessage = $this->validateSubjectPayload($payload, true);
            if ($validationMessage !== null) {
                $this->jsonResponse("400", $validationMessage);
            }

            $respond = $this->timetableService->updateSubject($payload);
            $this->jsonResponse("200", 'Subject updated successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }

    public function deleteSubject($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (!isset($payload['id']) || trim((string)$payload['id']) === '') {
                $this->jsonResponse("400", 'id is required.');
            }

            $respond = $this->timetableService->deleteSubject($payload['id']);
            $this->jsonResponse("200", 'Subject deleted successfully', $respond);
        } catch (Exception $e) {
            $this->jsonResponse("500", $e->getMessage());
        }
    }
}
?>
