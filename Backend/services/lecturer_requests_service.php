<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class LecturerRequestsService {
    private function fetchAllRows($query, $property = null) {
        $DB_CON = new DbConnection();
        if ($property === null) {
            $DB_CON->selectData($query);
        } else {
            $DB_CON->selectDataByProperty($query, $property);
        }
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result;
    }

    private function fetchSingleRow($query, $property = null) {
        $rows = $this->fetchAllRows($query, $property);
        return $rows[0] ?? null;
    }

    private function getTimetableSettings() {
        return $this->fetchSingleRow("SELECT * FROM timetable_settings ORDER BY id DESC LIMIT 1");
    }

    private function getActiveTimeSlots() {
        $settings = $this->getTimetableSettings();
        $breakRowNumber = (int)($settings['break_row_number'] ?? 0);
        $timeSlots = $this->fetchAllRows("SELECT id, start_time, end_time FROM timetable_time_slots ORDER BY start_time");

        if ($breakRowNumber <= 0) {
            return $timeSlots;
        }

        return array_values(array_filter($timeSlots, function ($slot, $index) use ($breakRowNumber) {
            return ($index + 1) !== $breakRowNumber;
        }, ARRAY_FILTER_USE_BOTH));
    }

    private function resolveCellReferenceId($timeSlotId, $columnHeadingId) {
        $settings = $this->getTimetableSettings();
        $columnCount = (int)($settings['table_column_count'] ?? 0);
        if ($columnCount <= 0) {
            throw new Exception('Timetable settings are not configured.');
        }

        $columnHeadings = $this->fetchAllRows("SELECT id, column_number FROM timetable_column_headings ORDER BY column_number");
        $activeTimeSlots = $this->getActiveTimeSlots();

        $columnIndex = -1;
        foreach ($columnHeadings as $index => $heading) {
            if ((string)$heading['id'] === (string)$columnHeadingId) {
                $columnIndex = $index;
                break;
            }
        }

        $rowIndex = -1;
        foreach ($activeTimeSlots as $index => $timeSlot) {
            if ((string)$timeSlot['id'] === (string)$timeSlotId) {
                $rowIndex = $index;
                break;
            }
        }

        if ($columnIndex === -1 || $rowIndex === -1) {
            throw new Exception('Unable to match timetable cell for the lecturer request.');
        }

        $cellNumber = ($rowIndex * $columnCount) + ($columnIndex + 1);
        $cell = $this->fetchSingleRow(
            "SELECT id FROM timetable_cells WHERE cell_number = :cell_number LIMIT 1",
            ['cell_number' => $cellNumber]
        );

        if (!$cell || empty($cell['id'])) {
            throw new Exception('Timetable cell was not found for the lecturer request.');
        }

        return $cell['id'];
    }

    private function resolveLectureGroupId($subjectCord) {
        $relation = $this->fetchSingleRow(
            "SELECT group_id FROM subject_group_relations WHERE subject_cord = :subject_cord ORDER BY id LIMIT 1",
            ['subject_cord' => $subjectCord]
        );

        if (!$relation || empty($relation['group_id'])) {
            throw new Exception('No lecture group is assigned to this subject.');
        }

        return $relation['group_id'];
    }

    private function syncTemporaryTimetable($payload) {
        $cellId = $this->resolveCellReferenceId($payload['timetable_time_slot_id'], $payload['timetable_column_heading_id']);
        $lectureGroupId = !empty($payload['lecture_group_id'])
            ? $payload['lecture_group_id']
            : $this->resolveLectureGroupId($payload['subject_id']);
        $existingRecord = $this->fetchSingleRow(
            "SELECT id FROM temporary_timetable
             WHERE cell_id = :cell_id
               AND lecture_group_id = :lecture_group_id
               AND subject_cord = :subject_cord
               AND lecturer_date = :lecturer_date
             ORDER BY id DESC
             LIMIT 1",
            [
                'cell_id' => $cellId,
                'lecture_group_id' => $lectureGroupId,
                'subject_cord' => $payload['subject_id'],
                'lecturer_date' => $payload['date'],
            ]
        );

        $DB_CON = new DbConnection();
        $auditValue = $payload['updated_by'] ?? $payload['created_by'] ?? 'system';

        if (($payload['action'] ?? '') === 'confirmed') {
            if ($existingRecord) {
                $result = $DB_CON->execute(
                    "UPDATE temporary_timetable
                     SET action = 'pending',
                         lab_id = :lab_id,
                         updated_by = :updated_by
                     WHERE id = :id",
                    [
                        'id' => $existingRecord['id'],
                        'lab_id' => $payload['lab_id'] ?? null,
                        'updated_by' => $auditValue,
                    ]
                );
            } else {
                $result = $DB_CON->execute(
                    "INSERT INTO temporary_timetable
                        (cell_id, lecture_group_id, lab_id, subject_cord, action, lecturer_date, created_by, updated_by)
                     VALUES
                        (:cell_id, :lecture_group_id, :lab_id, :subject_cord, :action, :lecturer_date, :created_by, :updated_by)",
                    [
                        'cell_id' => $cellId,
                        'lecture_group_id' => $lectureGroupId,
                        'lab_id' => $payload['lab_id'] ?? null,
                        'subject_cord' => $payload['subject_id'],
                        'action' => 'pending',
                        'lecturer_date' => $payload['date'],
                        'created_by' => $payload['created_by'] ?? $auditValue,
                        'updated_by' => $auditValue,
                    ]
                );
            }

            if ($result === false) {
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Failed to save temporary timetable record.');
            }
            return;
        }

        if (($payload['action'] ?? '') === 'canceled' && $existingRecord) {
            $result = $DB_CON->execute(
                "UPDATE temporary_timetable
                 SET action = 'canceled',
                     updated_by = :updated_by
                 WHERE id = :id",
                [
                    'id' => $existingRecord['id'],
                    'updated_by' => $auditValue,
                ]
            );

            if ($result === false) {
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Failed to update temporary timetable record.');
            }
        }
    }

    public function getAll() {
        $DB_CON = new DbConnection();
        $query = "SELECT
                    lr.id,
                    lr.lecturer_id,
                    lr.subject_id,
                    lr.year_id,
                    lr.timetable_time_slot_id,
                    lr.timetable_column_heading_id,
                    lr.date,
                    lr.action,
                    lr.lecturer_request,
                    lr.send_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name,
                    ps.subject,
                    y.year,
                    sgr.group_id,
                    lg.group_name,
                    tts.start_time,
                    tts.end_time,
                    tch.column_heading
                FROM lecturer_requests lr
                LEFT JOIN users u ON lr.lecturer_id = u.id
                LEFT JOIN practical_subjects ps ON lr.subject_id = ps.subject_cord
                LEFT JOIN years y ON lr.year_id = y.id
                LEFT JOIN (
                    SELECT subject_cord, MIN(group_id) AS group_id
                    FROM subject_group_relations
                    GROUP BY subject_cord
                ) sgr ON lr.subject_id = sgr.subject_cord
                LEFT JOIN lecture_groups lg ON sgr.group_id = lg.id
                LEFT JOIN timetable_time_slots tts ON lr.timetable_time_slot_id = tts.id
                LEFT JOIN timetable_column_headings tch ON lr.timetable_column_heading_id = tch.id
                ORDER BY lr.send_at DESC";
        $DB_CON->selectData($query);
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result;
    }

    public function create($payload) {
        $DB_CON = new DbConnection();
        $sendAt = date('Y-m-d H:i:s');

        $query = "INSERT INTO lecturer_requests
                    (
                        lecturer_id,
                        subject_id,
                        year_id,
                        timetable_time_slot_id,
                        timetable_column_heading_id,
                        date,
                        action,
                        lecturer_request,
                        send_at
                    )
                    VALUES
                    (
                        :lecturer_id,
                        :subject_id,
                        :year_id,
                        :timetable_time_slot_id,
                        :timetable_column_heading_id,
                        :date,
                        :action,
                        :lecturer_request,
                        :send_at
                    )";

        $property = [
            'lecturer_id' => $payload['lecturer_id'],
            'subject_id' => $payload['subject_id'],
            'year_id' => $payload['year_id'],
            'timetable_time_slot_id' => $payload['timetable_time_slot_id'],
            'timetable_column_heading_id' => $payload['timetable_column_heading_id'],
            'date' => $payload['date'],
            'action' => $payload['action'] ?? 'requested',
            'lecturer_request' => $payload['lecturer_request'],
            'send_at' => $payload['send_at'] ?? $sendAt,
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Lecturer request sent successfully';
    }

    public function update($payload) {
        $DB_CON = new DbConnection();

        $query = "UPDATE lecturer_requests
                    SET
                        lecturer_id = :lecturer_id,
                        subject_id = :subject_id,
                        year_id = :year_id,
                        timetable_time_slot_id = :timetable_time_slot_id,
                        timetable_column_heading_id = :timetable_column_heading_id,
                        date = :date,
                        action = :action,
                        lecturer_request = :lecturer_request
                    WHERE id = :id";

        $property = [
            'id' => $payload['id'],
            'lecturer_id' => $payload['lecturer_id'],
            'subject_id' => $payload['subject_id'],
            'year_id' => $payload['year_id'],
            'timetable_time_slot_id' => $payload['timetable_time_slot_id'],
            'timetable_column_heading_id' => $payload['timetable_column_heading_id'],
            'date' => $payload['date'],
            'action' => $payload['action'],
            'lecturer_request' => $payload['lecturer_request'],
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        if (in_array($payload['action'], ['confirmed', 'canceled'], true)) {
            $this->syncTemporaryTimetable($payload);
        }

        return 'Lecturer request updated successfully';
    }

    public function delete($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM lecturer_requests WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Lecturer request deleted successfully';
    }
}
?>
