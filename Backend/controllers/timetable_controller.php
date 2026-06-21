<?php
namespace Backend\Controllers;

require_once __DIR__ . "/../services/timetable_service.php";
require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/response.php';

use Backend\Services\TimetableService;
use Backend\Services\LogsService;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Backend\Utils\Response;
use Exception;

class TimetableController {
    private $timetableService;
    private $logsService;

    public function __construct() {
        $this->timetableService = new TimetableService();
        $this->logsService      = new LogsService();
    }

    private function jsonResponse($status, $message, $data = null) {
        Response::send((string)$status, $message, $data);
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

    private function dbLog(string $type, string $table, $old, $new): void {
        $actor = $this->getAuthUser();
        $this->logsService->logAction($type, $table, $old, $new, isset($actor['userId']) ? (int)$actor['userId'] : null);
    }

    // ── Read ─────────────────────────────────────────────────────────

    public function getAllTimeSchedules($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getAllTimeSchedules();
            $this->jsonResponse("200", 'Data fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getTimeSchedulesByYear($req = null, $res = null) {
        $year = $req['query']['year'] ?? '';
        try {
            $respond = $this->timetableService->getTimeSchedulesByYear($year);
            $this->jsonResponse("200", 'Data fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getTemporaryTimeSchedules($req = null, $res = null) {
        $dateFrom = $req['query']['date_from'] ?? null;
        $dateTo   = $req['query']['date_to'] ?? null;
        try {
            $respond = $this->timetableService->getTemporaryTimeSchedules($dateFrom, $dateTo);
            $this->jsonResponse("200", 'Temporary timetable fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getSubjectCodes($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getSubjectCodes();
            $this->jsonResponse("200", 'Subject codes fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getYears($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getYears();
            $this->jsonResponse("200", 'Years fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getTimeSlots($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimeSlots();
            $this->jsonResponse("200", 'Time slots fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getColumnHeadings($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getColumnHeadings();
            $this->jsonResponse("200", 'Column headings fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getTimetableSettings($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimetableSettings();
            $this->jsonResponse("200", 'Timetable settings fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getLectureGroups($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getLectureGroups();
            $this->jsonResponse("200", 'Lecture groups fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getLabs($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getLabs();
            $this->jsonResponse("200", 'Labs fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function getTimetableCells($req = null, $res = null) {
        try {
            $respond = $this->timetableService->getTimetableCells();
            $this->jsonResponse("200", 'Timetable cells fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Years ─────────────────────────────────────────────────────────

    public function createYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createYear($payload);
            $this->dbLog('INSERT', 'years', null, $payload);
            $this->jsonResponse("200", 'Year created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('years', $payload['id'] ?? null);
            $respond = $this->timetableService->updateYear($payload);
            $this->dbLog('UPDATE', 'years', $old, $payload);
            $this->jsonResponse("200", 'Year updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteYear($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('years', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteYear($payload['id']);
            $this->dbLog('DELETE', 'years', $old, null);
            $this->jsonResponse("200", 'Year deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Lecture Groups ────────────────────────────────────────────────

    public function createLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createLectureGroup($payload);
            $this->dbLog('INSERT', 'lecture_groups', null, $payload);
            $this->jsonResponse("200", 'Group created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('lecture_groups', $payload['id'] ?? null);
            $respond = $this->timetableService->updateLectureGroup($payload);
            $this->dbLog('UPDATE', 'lecture_groups', $old, $payload);
            $this->jsonResponse("200", 'Group updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteLectureGroup($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('lecture_groups', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteLectureGroup($payload['id']);
            $this->dbLog('DELETE', 'lecture_groups', $old, null);
            $this->jsonResponse("200", 'Group deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Labs ──────────────────────────────────────────────────────────

    public function createLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createLab($payload);
            $this->dbLog('INSERT', 'labs', null, $payload);
            $this->jsonResponse("200", 'Lab created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('labs', $payload['id'] ?? null);
            $respond = $this->timetableService->updateLab($payload);
            $this->dbLog('UPDATE', 'labs', $old, $payload);
            $this->jsonResponse("200", 'Lab updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteLab($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('labs', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteLab($payload['id']);
            $this->dbLog('DELETE', 'labs', $old, null);
            $this->jsonResponse("200", 'Lab deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Timetable Records ─────────────────────────────────────────────

    public function createTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $payload['time_slot_id']       = $this->normalizeNullableValue($payload['time_slot_id'] ?? null);
            $payload['column_heading_id']  = $this->normalizeNullableValue($payload['column_heading_id'] ?? null);
            $payload['lecture_group_id']   = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id']             = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord']       = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createTimetableRecord($payload);
            $this->dbLog('INSERT', 'timetable', null, $payload);
            $this->jsonResponse("200", 'Timetable record created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $payload['time_slot_id']       = $this->normalizeNullableValue($payload['time_slot_id'] ?? null);
            $payload['column_heading_id']  = $this->normalizeNullableValue($payload['column_heading_id'] ?? null);
            $payload['lecture_group_id']   = $this->normalizeNullableValue($payload['lecture_group_id'] ?? null);
            $payload['lab_id']             = $this->normalizeNullableValue($payload['lab_id'] ?? null);
            $payload['subject_cord']       = $this->normalizeNullableValue($payload['subject_cord'] ?? null);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('timetable', $payload['id'] ?? null);
            $respond = $this->timetableService->updateTimetableRecord($payload);
            $this->dbLog('UPDATE', 'timetable', $old, $payload);
            $this->jsonResponse("200", 'Timetable record updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteTimetableRecord($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('timetable', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteTimetableRecord($payload['id']);
            $this->dbLog('DELETE', 'timetable', $old, null);
            $this->jsonResponse("200", 'Timetable record deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Timetable Settings ────────────────────────────────────────────

    public function updateTimetableSettings($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;

            $rowCount        = (int)$payload['table_row_count'];
            $columnCount     = (int)$payload['table_column_count'];
            $breakRowNumber  = (int)$payload['break_row_number'];
            $activeRows      = $breakRowNumber > 0 ? max($rowCount - 1, 0) : $rowCount;
            $payload['table_cell_count'] = $activeRows * $columnCount;
            $payload['break_cell_ids']   = '';

            $respond = $this->timetableService->updateTimetableSettings($payload);
            $this->dbLog('UPDATE', 'timetable_settings', null, $payload);
            $this->jsonResponse("200", 'Timetable settings updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function resetTimetableSettings($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->resetTimetableSettings($payload);
            $this->dbLog('UPDATE', 'timetable_settings', null, ['action' => 'reset', 'updated_by' => $payload['updated_by']]);
            $this->jsonResponse("200", 'Timetable settings reset successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Column Headings ───────────────────────────────────────────────

    public function createColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createColumnHeading($payload);
            $this->dbLog('INSERT', 'timetable_column_headings', null, $payload);
            $this->jsonResponse("200", 'Column heading created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('timetable_column_headings', $payload['id'] ?? null);
            $respond = $this->timetableService->updateColumnHeading($payload);
            $this->dbLog('UPDATE', 'timetable_column_headings', $old, $payload);
            $this->jsonResponse("200", 'Column heading updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteColumnHeading($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('timetable_column_headings', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteColumnHeading($payload['id']);
            $this->dbLog('DELETE', 'timetable_column_headings', $old, null);
            $this->jsonResponse("200", 'Column heading deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Time Slots ────────────────────────────────────────────────────

    public function createTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createTimeSlot($payload);
            $this->dbLog('INSERT', 'timetable_time_slots', null, $payload);
            $this->jsonResponse("200", 'Time slot created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('timetable_time_slots', $payload['id'] ?? null);
            $respond = $this->timetableService->updateTimeSlot($payload);
            $this->dbLog('UPDATE', 'timetable_time_slots', $old, $payload);
            $this->jsonResponse("200", 'Time slot updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteTimeSlot($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('timetable_time_slots', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteTimeSlot($payload['id']);
            $this->dbLog('DELETE', 'timetable_time_slots', $old, null);
            $this->jsonResponse("200", 'Time slot deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    // ── Subjects ──────────────────────────────────────────────────────

    public function createSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $respond = $this->timetableService->createSubject($payload);
            $this->dbLog('INSERT', 'practical_subjects', null, $payload);
            $this->jsonResponse("200", 'Subject created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function updateSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('practical_subjects', $payload['id'] ?? null);
            $respond = $this->timetableService->updateSubject($payload);
            $this->dbLog('UPDATE', 'practical_subjects', $old, $payload);
            $this->jsonResponse("200", 'Subject updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }

    public function deleteSubject($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('practical_subjects', $payload['id'] ?? null);
            $respond = $this->timetableService->deleteSubject($payload['id']);
            $this->dbLog('DELETE', 'practical_subjects', $old, null);
            $this->jsonResponse("200", 'Subject deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[TimetableController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            $this->jsonResponse("500", 'An internal error occurred');
        }
    }
}
?>
