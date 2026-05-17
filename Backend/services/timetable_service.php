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
                    tc.id AS timetable_cell_reference_id,
                    tc.id AS cell_id,
                    t.time_slot_id,
                    t.column_heading_id,
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
                    ic.lecturer_id,
                    ic.lecturer_name,
                    oth.other_lecturers
                FROM timetable t
                LEFT JOIN (
                    SELECT
                        MIN(id) AS id,
                        time_slot_id,
                        column_heading_id
                    FROM timetable_cells
                    GROUP BY time_slot_id, column_heading_id
                ) tc ON tc.time_slot_id = t.time_slot_id AND tc.column_heading_id = t.column_heading_id
                LEFT JOIN lecture_groups lg ON t.lecture_group_id = lg.id
                LEFT JOIN labs l ON t.lab_id = l.id
                LEFT JOIN practical_subjects ps ON t.subject_cord = ps.subject_cord
                LEFT JOIN years y ON ps.year_id = y.id
                LEFT JOIN (
                    SELECT slr.subject_cord,
                           slr.lecturer_id,
                           TRIM(CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,''))) AS lecturer_name
                    FROM subject_lecture_relations slr
                    JOIN lecturer_responsibility lr ON slr.responsibility_id = lr.id AND lr.responsible_level = 1
                    JOIN users u ON slr.lecturer_id = u.id
                ) ic ON ic.subject_cord = t.subject_cord
                LEFT JOIN (
                    SELECT slr.subject_cord,
                           GROUP_CONCAT(
                               TRIM(CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')))
                               ORDER BY u.last_name ASC SEPARATOR ', '
                           ) AS other_lecturers
                    FROM subject_lecture_relations slr
                    LEFT JOIN lecturer_responsibility lr ON slr.responsibility_id = lr.id
                    JOIN users u ON slr.lecturer_id = u.id
                    WHERE lr.id IS NULL OR lr.responsible_level IS NULL OR lr.responsible_level != 1
                    GROUP BY slr.subject_cord
                ) oth ON oth.subject_cord = t.subject_cord";
    }

    public function getAllTimeSchedules() {
        $query = $this->getBaseTimeScheduleQuery() . " ORDER BY tc.id ASC";
        return $this->fetchAllRows($query);
    }

    public function getTemporaryTimeSchedules($dateFrom = null, $dateTo = null) {
        $query = "SELECT
                    tt.id AS temporary_timetable_id,
                    tc.id AS timetable_cell_reference_id,
                    tc.id AS cell_id,
                    tt.time_slot_id,
                    tt.column_heading_id,
                    tt.lecture_group_id,
                    tt.lab_id,
                    tt.subject_cord,
                    tt.action,
                    tt.lecturer_date,
                    lg.group_name,
                    l.lab_name AS lab,
                    l.lab_location,
                    ps.subject,
                    ps.year_id,
                    y.year,
                    ic.lecturer_id,
                    ic.lecturer_name,
                    oth.other_lecturers
                FROM temporary_timetable tt
                LEFT JOIN (
                    SELECT
                        MIN(id) AS id,
                        time_slot_id,
                        column_heading_id
                    FROM timetable_cells
                    GROUP BY time_slot_id, column_heading_id
                ) tc ON tc.time_slot_id = tt.time_slot_id AND tc.column_heading_id = tt.column_heading_id
                LEFT JOIN lecture_groups lg ON tt.lecture_group_id = lg.id
                LEFT JOIN labs l ON tt.lab_id = l.id
                LEFT JOIN practical_subjects ps ON tt.subject_cord = ps.subject_cord
                LEFT JOIN years y ON ps.year_id = y.id
                LEFT JOIN (
                    SELECT slr.subject_cord,
                           slr.lecturer_id,
                           TRIM(CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,''))) AS lecturer_name
                    FROM subject_lecture_relations slr
                    JOIN lecturer_responsibility lr ON slr.responsibility_id = lr.id AND lr.responsible_level = 1
                    JOIN users u ON slr.lecturer_id = u.id
                ) ic ON ic.subject_cord = tt.subject_cord
                LEFT JOIN (
                    SELECT slr.subject_cord,
                           GROUP_CONCAT(
                               TRIM(CONCAT(COALESCE(u.first_name,''), ' ', COALESCE(u.last_name,'')))
                               ORDER BY u.last_name ASC SEPARATOR ', '
                           ) AS other_lecturers
                    FROM subject_lecture_relations slr
                    LEFT JOIN lecturer_responsibility lr ON slr.responsibility_id = lr.id
                    JOIN users u ON slr.lecturer_id = u.id
                    WHERE lr.id IS NULL OR lr.responsible_level IS NULL OR lr.responsible_level != 1
                    GROUP BY slr.subject_cord
                ) oth ON oth.subject_cord = tt.subject_cord
                WHERE tt.action != 'canceled'";

        $property = [];
        if ($dateFrom !== null && $dateTo !== null) {
            $query .= " AND tt.lecturer_date BETWEEN :date_from AND :date_to";
            $property['date_from'] = $dateFrom;
            $property['date_to'] = $dateTo;
        }

        $query .= " ORDER BY tt.lecturer_date ASC, tc.id ASC, tt.id DESC";
        return $this->fetchAllRows($query, $property ?: null);
    }

    public function getTimeSchedulesByYear($year) {
        $query = $this->getBaseTimeScheduleQuery() . " WHERE y.year = :year ORDER BY tc.id ASC";
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

    public function createYear($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO years
                    (year, created_by, updated_by)
                    VALUES
                    (:year, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'year' => $payload['year'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Year created successfully';
    }

    public function updateYear($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE years
                    SET
                        year = :year,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'year' => $payload['year'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Year updated successfully';
    }

    public function deleteYear($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM years WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Year deleted successfully';
    }

    public function getTimeSlots() {
        $query = "SELECT id, time_slot_number, start_time, end_time
                  FROM timetable_time_slots
                  ORDER BY time_slot_number ASC, start_time ASC";
        return $this->fetchAllRows($query);
    }

    public function getColumnHeadings() {
        $query = "SELECT id, column_heading, column_number, column_heading_number, status
                  FROM timetable_column_headings
                  ORDER BY column_heading_number ASC, column_number ASC";
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

    public function createLectureGroup($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO lecture_groups
                    (group_name, created_by, updated_by)
                    VALUES
                    (:group_name, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'group_name' => $payload['group_name'],
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Group created successfully';
    }

    public function updateLectureGroup($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE lecture_groups
                    SET
                        group_name = :group_name,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'group_name' => $payload['group_name'],
            'updated_by' => $payload['updated_by'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Group updated successfully';
    }

    public function deleteLectureGroup($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM lecture_groups WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Group deleted successfully';
    }

    public function getLabs() {
        $query = "SELECT id, lab_name, lab_location FROM labs ORDER BY lab_name";
        return $this->fetchAllRows($query);
    }

    public function createLab($payload) {
        $DB_CON = new DbConnection();
        $query = "INSERT INTO labs
                    (lab_name, lab_location)
                    VALUES
                    (:lab_name, :lab_location)";
        $result = $DB_CON->execute($query, [
            'lab_name' => $payload['lab_name'],
            'lab_location' => $payload['lab_location'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Lab created successfully';
    }

    public function updateLab($payload) {
        $DB_CON = new DbConnection();
        $query = "UPDATE labs
                    SET
                        lab_name = :lab_name,
                        lab_location = :lab_location
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'lab_name' => $payload['lab_name'],
            'lab_location' => $payload['lab_location'],
        ]);

        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Lab updated successfully';
    }

    public function deleteLab($id) {
        $DB_CON = new DbConnection();
        $query = "DELETE FROM labs WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'Lab deleted successfully';
    }

    public function getTimetableCells() {
        $query = "SELECT id, time_slot_id, column_heading_id
                  FROM timetable_cells
                  ORDER BY id";
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

    public function isColumnHeadingNumberTaken($columnHeadingNumber, $excludeId = null) {
        $query = "SELECT id FROM timetable_column_headings WHERE column_heading_number = :column_heading_number";
        $property = ['column_heading_number' => $columnHeadingNumber];
        if ($excludeId !== null) {
            $query .= " AND id != :exclude_id";
            $property['exclude_id'] = $excludeId;
        }
        $query .= " LIMIT 1";

        $result = $this->fetchOptionalRow($query, $property);
        return !empty($result);
    }

    public function isTimeSlotNumberTaken($timeSlotNumber, $excludeId = null) {
        $query = "SELECT id FROM timetable_time_slots WHERE time_slot_number = :time_slot_number";
        $property = ['time_slot_number' => $timeSlotNumber];
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
                        time_slot_id,
                        column_heading_id,
                        lecture_group_id,
                        lab_id,
                        subject_cord,
                        action,
                        created_by,
                        updated_by
                    )
                    VALUES
                    (
                        :time_slot_id,
                        :column_heading_id,
                        :lecture_group_id,
                        :lab_id,
                        :subject_cord,
                        :action,
                        :created_by,
                        :updated_by
                    )";

        $property = [
            'time_slot_id' => $payload['time_slot_id'],
            'column_heading_id' => $payload['column_heading_id'],
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
                        time_slot_id = :time_slot_id,
                        column_heading_id = :column_heading_id,
                        lecture_group_id = :lecture_group_id,
                        lab_id = :lab_id,
                        subject_cord = :subject_cord,
                        action = :action,
                        updated_by = :updated_by
                    WHERE id = :id";

        $property = [
            'id' => $payload['id'],
            'time_slot_id' => $payload['time_slot_id'],
            'column_heading_id' => $payload['column_heading_id'],
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
                    (column_heading, column_number, column_heading_number, status, created_by, updated_by)
                    VALUES
                    (:column_heading, :column_number, :column_heading_number, :status, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'column_heading' => $payload['column_heading'],
            'column_number' => $payload['column_number'],
            'column_heading_number' => $payload['column_heading_number'],
            'status' => $payload['status'],
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
                        column_heading_number = :column_heading_number,
                        status = :status,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'column_heading' => $payload['column_heading'],
            'column_number' => $payload['column_number'],
            'column_heading_number' => $payload['column_heading_number'],
            'status' => $payload['status'],
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
                    (time_slot_number, start_time, end_time, created_by, updated_by)
                    VALUES
                    (:time_slot_number, :start_time, :end_time, :created_by, :updated_by)";
        $result = $DB_CON->execute($query, [
            'time_slot_number' => $payload['time_slot_number'],
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
                        time_slot_number = :time_slot_number,
                        start_time = :start_time,
                        end_time = :end_time,
                        updated_by = :updated_by
                    WHERE id = :id";
        $result = $DB_CON->execute($query, [
            'id' => $payload['id'],
            'time_slot_number' => $payload['time_slot_number'],
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
