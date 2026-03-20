<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class LecturerRequestsService {
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
                    tts.start_time,
                    tts.end_time,
                    tch.column_heading
                FROM lecturer_requests lr
                LEFT JOIN users u ON lr.lecturer_id = u.id
                LEFT JOIN practical_subjects ps ON lr.subject_id = ps.subject_cord
                LEFT JOIN years y ON lr.year_id = y.id
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
