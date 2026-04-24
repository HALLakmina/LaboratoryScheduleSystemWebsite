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
        $timeSlots = $this->fetchAllRows("SELECT id, time_slot_number, start_time, end_time FROM timetable_time_slots ORDER BY time_slot_number ASC, start_time ASC");

        if ($breakRowNumber <= 0) {
            return $timeSlots;
        }

        return array_values(array_filter($timeSlots, function ($slot, $index) use ($breakRowNumber) {
            return ($index + 1) !== $breakRowNumber;
        }, ARRAY_FILTER_USE_BOTH));
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
        $timeSlotId = $payload['timetable_time_slot_id'];
        $columnHeadingId = $payload['timetable_column_heading_id'];
        $lectureGroupId = !empty($payload['lecture_group_id'])
            ? $payload['lecture_group_id']
            : $this->resolveLectureGroupId($payload['subject_id']);
        $existingRecord = $this->fetchSingleRow(
            "SELECT id FROM temporary_timetable
             WHERE time_slot_id = :time_slot_id
               AND column_heading_id = :column_heading_id
               AND lecture_group_id = :lecture_group_id
               AND subject_cord = :subject_cord
               AND lecturer_date = :lecturer_date
             ORDER BY id DESC
             LIMIT 1",
            [
                'time_slot_id' => $timeSlotId,
                'column_heading_id' => $columnHeadingId,
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
                     SET action = 'temporary_lecture',
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
                        (time_slot_id, column_heading_id, lecture_group_id, lab_id, subject_cord, action, lecturer_date, created_by, updated_by)
                     VALUES
                        (:time_slot_id, :column_heading_id, :lecture_group_id, :lab_id, :subject_cord, :action, :lecturer_date, :created_by, :updated_by)",
                    [
                        'time_slot_id' => $timeSlotId,
                        'column_heading_id' => $columnHeadingId,
                        'lecture_group_id' => $lectureGroupId,
                        'lab_id' => $payload['lab_id'] ?? null,
                        'subject_cord' => $payload['subject_id'],
                        'action' => 'temporary_lecture',
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

    private function deleteTemporaryTimetableForRequest($request) {
        $lectureGroupId = !empty($request['lecture_group_id'])
            ? $request['lecture_group_id']
            : $this->resolveLectureGroupId($request['subject_id']);

        $DB_CON = new DbConnection();
        $result = $DB_CON->execute(
            "DELETE FROM temporary_timetable
             WHERE time_slot_id = :time_slot_id
               AND column_heading_id = :column_heading_id
               AND lecture_group_id = :lecture_group_id
               AND subject_cord = :subject_cord
               AND lecturer_date = :lecturer_date",
            [
                'time_slot_id' => $request['timetable_time_slot_id'],
                'column_heading_id' => $request['timetable_column_heading_id'],
                'lecture_group_id' => $lectureGroupId,
                'subject_cord' => $request['subject_id'],
                'lecturer_date' => $request['date'],
            ]
        );

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Failed to delete temporary timetable record.');
        }
    }

    public function checkTemporaryTimetableAvailability($payload) {
        $record = $this->fetchSingleRow(
            "SELECT
                tt.id,
                tt.action,
                tt.lecturer_date,
                tt.time_slot_id,
                tt.column_heading_id,
                tt.subject_cord,
                lg.group_name,
                l.lab_name
             FROM temporary_timetable tt
             LEFT JOIN lecture_groups lg ON tt.lecture_group_id = lg.id
             LEFT JOIN labs l ON tt.lab_id = l.id
             WHERE tt.time_slot_id = :time_slot_id
               AND tt.column_heading_id = :column_heading_id
               AND tt.lecturer_date = :lecturer_date
               AND tt.action != 'canceled'
             ORDER BY tt.id DESC
             LIMIT 1",
             [
                'time_slot_id' => $payload['timetable_time_slot_id'],
                'column_heading_id' => $payload['timetable_column_heading_id'],
                'lecturer_date' => $payload['date'],
            ]
        );

        return [
            'is_booked' => !empty($record),
            'time_slot_id' => $record['time_slot_id'] ?? null,
            'column_heading_id' => $record['column_heading_id'] ?? null,
            'record' => $record,
        ];
    }

    public function getAll() {
        $DB_CON = new DbConnection();
        $query = "SELECT
                    lr.id,
                    lr.lecturer_id,
                    lr.subject_id,
                    lr.year_id,
                    lr.lecture_group_id,
                    lr.timetable_time_slot_id,
                    lr.timetable_column_heading_id,
                    lr.date,
                    lr.action,
                    lr.lecturer_request,
                    lr.send_at,
                    CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name,
                    ps.subject,
                    y.year,
                    lg.group_name,
                    tts.start_time,
                    tts.end_time,
                    tch.column_heading
                FROM lecturer_requests lr
                LEFT JOIN users u ON lr.lecturer_id = u.id
                LEFT JOIN practical_subjects ps ON lr.subject_id = ps.subject_cord
                LEFT JOIN years y ON lr.year_id = y.id
                LEFT JOIN lecture_groups lg ON lr.lecture_group_id = lg.id
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
        $lectureGroupId = !empty($payload['lecture_group_id'])
            ? $payload['lecture_group_id']
            : $this->resolveLectureGroupId($payload['subject_id']);

        $query = "INSERT INTO lecturer_requests
                    (
                        lecturer_id,
                        subject_id,
                        year_id,
                        lecture_group_id,
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
                        :lecture_group_id,
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
            'lecture_group_id' => $lectureGroupId,
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
        $lectureGroupId = !empty($payload['lecture_group_id'])
            ? $payload['lecture_group_id']
            : $this->resolveLectureGroupId($payload['subject_id']);

        $query = "UPDATE lecturer_requests
                    SET
                        lecturer_id = :lecturer_id,
                        subject_id = :subject_id,
                        year_id = :year_id,
                        lecture_group_id = :lecture_group_id,
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
            'lecture_group_id' => $lectureGroupId,
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
        $request = $this->fetchSingleRow(
            "SELECT
                id,
                subject_id,
                lecture_group_id,
                timetable_time_slot_id,
                timetable_column_heading_id,
                date
             FROM lecturer_requests
             WHERE id = :id
             LIMIT 1",
            ['id' => $id]
        );

        if (!$request) {
            throw new Exception('Lecturer request not found.');
        }

        $this->deleteTemporaryTimetableForRequest($request);

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
