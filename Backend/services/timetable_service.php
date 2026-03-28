<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class TimetableService {
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
        $DB_CON = new DbConnection();
        if ($property === null) {
            $DB_CON->selectData($query);
        } else {
            $DB_CON->selectDataByProperty($query, $property);
        }
        $result = $DB_CON->fetchRow();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result;
    }

    private function fetchOptionalRow($query, $property = null) {
        $DB_CON = new DbConnection();
        if ($property === null) {
            $DB_CON->selectData($query);
        } else {
            $DB_CON->selectDataByProperty($query, $property);
        }
        $result = $DB_CON->fetchRow();
        if ($result === false) {
            return null;
        }

        return $result;
    }

    private function getBaseTimeScheduleQuery() {
        return "SELECT
                    t.id AS timetable_id,
                    t.cell_id AS timetable_cell_reference_id,
                    tc.cell_number AS cell_id,
                    t.lecture_group_id,
                    t.lab_id,
                    t.subject_cord,
                    t.action,
                    lg.group_name,
                    l.lab_name AS lab,
                    l.lab_location,
                    ps.subject,
                    ps.year_id,
                    y.year,
                    sl.lecturer_id,
                    CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name
                FROM timetable t
                LEFT JOIN timetable_cells tc ON t.cell_id = tc.id
                LEFT JOIN lecture_groups lg ON t.lecture_group_id = lg.id
                LEFT JOIN labs l ON t.lab_id = l.id
                LEFT JOIN practical_subjects ps ON t.subject_cord = ps.subject_cord
                LEFT JOIN years y ON ps.year_id = y.id
                LEFT JOIN subject_lecture_relations sl ON ps.subject_cord = sl.subject_cord
                LEFT JOIN users u ON sl.lecturer_id = u.id";
    }

    public function getAllTimeSchedules() {
        $query = $this->getBaseTimeScheduleQuery() . " ORDER BY tc.cell_number ASC";
        return $this->fetchAllRows($query);
    }

    public function getTimeSchedulesByYear($year) {
        $query = $this->getBaseTimeScheduleQuery() . " WHERE y.year = :year ORDER BY tc.cell_number ASC";
        return $this->fetchAllRows($query, [
            'year' => $year,
        ]);
    }

    public function getSubjectCodes() {
        $query = "SELECT DISTINCT ps.id AS subject_id, ps.subject_cord, ps.subject, ps.year_id, y.year
                  FROM practical_subjects ps
                  LEFT JOIN years y ON ps.year_id = y.id
                  ORDER BY ps.subject_cord";
        return $this->fetchAllRows($query);
    }

    public function getYears() {
        $query = "SELECT id, year FROM years ORDER BY year";
        return $this->fetchAllRows($query);
    }

    public function getTimeSlots() {
        $query = "SELECT id, start_time, end_time FROM timetable_time_slots ORDER BY start_time";
        return $this->fetchAllRows($query);
    }

    public function getColumnHeadings() {
        $query = "SELECT id, column_heading, column_number FROM timetable_column_headings ORDER BY column_number";
        return $this->fetchAllRows($query);
    }

    public function getTimetableSettings() {
        $query = "SELECT * FROM timetable_settings ORDER BY id DESC LIMIT 1";
        return $this->fetchSingleRow($query);
    }

    public function getLectureGroups() {
        $query = "SELECT id, group_name FROM lecture_groups ORDER BY group_name";
        return $this->fetchAllRows($query);
    }

    public function getLabs() {
        $query = "SELECT id, lab_name, lab_location FROM labs ORDER BY lab_name";
        return $this->fetchAllRows($query);
    }

    public function getTimetableCells() {
        $query = "SELECT id, cell_number FROM timetable_cells ORDER BY cell_number";
        return $this->fetchAllRows($query);
    }

    public function countColumnHeadings() {
        $result = $this->fetchSingleRow("SELECT COUNT(*) AS total FROM timetable_column_headings");
        return (int)($result['total'] ?? 0);
    }

    public function countTimeSlots() {
        $result = $this->fetchSingleRow("SELECT COUNT(*) AS total FROM timetable_time_slots");
        return (int)($result['total'] ?? 0);
    }

    public function isColumnNumberTaken($columnNumber, $excludeId = null) {
        $query = "SELECT id FROM timetable_column_headings WHERE column_number = :column_number";
        $property = ['column_number' => $columnNumber];
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
            $property['exclude_id'] = $excludeId;
        }
        $query .= " LIMIT 1";

        $result = $this->fetchOptionalRow($query, $property);
        return !empty($result);
    }

    public function createTimetableRecord($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO timetable
                    (
                        cell_id,
                        lecture_group_id,
                        lab_id,
                        subject_cord,
                        action,
                        created_by,
                        updated_by
                    )
                    VALUES
                    (
                        :cell_id,
                        :lecture_group_id,
                        :lab_id,
                        :subject_cord,
                        :action,
                        :created_by,
                        :updated_by
                    )";

        $property = [
            'cell_id' => $payload['cell_id'],
            'lecture_group_id' => $payload['lecture_group_id'],
            'lab_id' => $payload['lab_id'],
            'subject_cord' => $payload['subject_cord'],
            'action' => $payload['action'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Timetable record created successfully';
    }

    public function updateTimetableRecord($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE timetable
                    SET
                        cell_id = :cell_id,
                        lecture_group_id = :lecture_group_id,
                        lab_id = :lab_id,
                        subject_cord = :subject_cord,
                        action = :action,
                        updated_by = :updated_by
                    WHERE id = :id";

        $property = [
            'id' => $payload['id'],
            'cell_id' => $payload['cell_id'],
            'lecture_group_id' => $payload['lecture_group_id'],
            'lab_id' => $payload['lab_id'],
            'subject_cord' => $payload['subject_cord'],
            'action' => $payload['action'],
            'updated_by' => $payload['updated_by'],
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Timetable record updated successfully';
    }

    public function deleteTimetableRecord($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM timetable WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Timetable record deleted successfully';
    }

    public function updateTimetableSettings($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE timetable_settings
                    SET
                        table_cell_count = :table_cell_count,
                        table_row_count = :table_row_count,
                        table_column_count = :table_column_count,
                        break_row_number = :break_row_number,
                        break_cell_ids = :break_cell_ids,
                        updated_by = :updated_by
                    WHERE id = :id";

        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'table_cell_count' => $payload['table_cell_count'],
            'table_row_count' => $payload['table_row_count'],
            'table_column_count' => $payload['table_column_count'],
            'break_row_number' => $payload['break_row_number'],
            'break_cell_ids' => $payload['break_cell_ids'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Timetable settings updated successfully';
    }

    public function resetTimetableSettings($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE timetable_settings
                    SET
                        table_cell_count = 0,
                        table_row_count = 0,
                        table_column_count = 0,
                        break_row_number = 0,
                        break_cell_ids = '',
                        updated_by = :updated_by
                    WHERE id = :id";

        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Timetable settings reset successfully';
    }

    public function createColumnHeading($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO timetable_column_headings
                    (column_heading, column_number, created_by, updated_by)
                    VALUES
                    (:column_heading, :column_number, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'column_heading' => $payload['column_heading'],
            'column_number' => $payload['column_number'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Column heading created successfully';
    }

    public function updateColumnHeading($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE timetable_column_headings
                    SET
                        column_heading = :column_heading,
                        column_number = :column_number,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'column_heading' => $payload['column_heading'],
            'column_number' => $payload['column_number'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Column heading updated successfully';
    }

    public function deleteColumnHeading($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM timetable_column_headings WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Column heading deleted successfully';
    }

    public function createTimeSlot($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO timetable_time_slots
                    (start_time, end_time, created_by, updated_by)
                    VALUES
                    (:start_time, :end_time, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'start_time' => $payload['start_time'],
            'end_time' => $payload['end_time'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Time slot created successfully';
    }

    public function updateTimeSlot($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE timetable_time_slots
                    SET
                        start_time = :start_time,
                        end_time = :end_time,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'start_time' => $payload['start_time'],
            'end_time' => $payload['end_time'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Time slot updated successfully';
    }

    public function deleteTimeSlot($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM timetable_time_slots WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Time slot deleted successfully';
    }

    public function createSubject($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO practical_subjects
                    (subject_cord, subject, year_id, created_by, updated_by)
                    VALUES
                    (:subject_cord, :subject, :year_id, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'subject_cord' => $payload['subject_cord'],
            'subject' => $payload['subject'],
            'year_id' => $payload['year_id'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Subject created successfully';
    }

    public function updateSubject($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE practical_subjects
                    SET
                        subject_cord = :subject_cord,
                        subject = :subject,
                        year_id = :year_id,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'subject_cord' => $payload['subject_cord'],
            'subject' => $payload['subject'],
            'year_id' => $payload['year_id'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Subject updated successfully';
    }

    public function deleteSubject($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM practical_subjects WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Subject deleted successfully';
    }
}
?>
