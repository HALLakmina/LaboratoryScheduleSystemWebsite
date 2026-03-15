<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class LecturerRequestsService {
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
                        :lecturer_request,
                        :send_at
                    )";

        $property = [
            'lecturer_id' => $payload['lecturer_id'],
            'subject_id' => $payload['subject_id'],
            'year_id' => $payload['year_id'],
            'timetable_time_slot_id' => $payload['timetable_time_slot_id'],
            'timetable_column_heading_id' => $payload['timetable_column_heading_id'],
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
}
?>
