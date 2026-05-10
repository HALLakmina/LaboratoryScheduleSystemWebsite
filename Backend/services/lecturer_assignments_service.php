<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class LecturerAssignmentsService {

    private function fetchAllRows($query, $property = null) {
        $DB_CON = new DbConnection();
        if ($property === null) {
            $DB_CON->selectData($query);
        } else {
            $DB_CON->selectDataByProperty($query, $property);
        }
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            throw new Exception($DB_CON->getError() ?: 'SQL query error');
        }
        return $result;
    }

    private function executeQuery($query, $property) {
        $DB_CON = new DbConnection();
        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            throw new Exception($DB_CON->getError() ?: 'SQL query error');
        }
        return $result;
    }

    // ── Responsibilities ──────────────────────────────────────────────

    public function getResponsibilities() {
        return $this->fetchAllRows(
            "SELECT id, responsibility, created_by, updated_by, created_at, updated_at
             FROM lecturer_responsibility
             ORDER BY responsibility ASC"
        );
    }

    public function createResponsibility($payload) {
        $this->executeQuery(
            "INSERT INTO lecturer_responsibility (responsibility, created_by, updated_by)
             VALUES (:responsibility, :created_by, :updated_by)",
            [
                'responsibility' => $payload['responsibility'],
                'created_by'     => $payload['created_by'] ?? '',
                'updated_by'     => $payload['updated_by'] ?? '',
            ]
        );
        return 'Responsibility created successfully';
    }

    public function updateResponsibility($payload) {
        $this->executeQuery(
            "UPDATE lecturer_responsibility
             SET responsibility = :responsibility,
                 updated_by     = :updated_by
             WHERE id = :id",
            [
                'id'             => $payload['id'],
                'responsibility' => $payload['responsibility'],
                'updated_by'     => $payload['updated_by'] ?? '',
            ]
        );
        return 'Responsibility updated successfully';
    }

    public function deleteResponsibility($id) {
        $this->executeQuery(
            "DELETE FROM lecturer_responsibility WHERE id = :id",
            ['id' => $id]
        );
        return 'Responsibility deleted successfully';
    }

    // ── Subject–Lecturer Assignments ──────────────────────────────────

    public function getAssignments() {
        return $this->fetchAllRows(
            "SELECT
                slr.id,
                slr.subject_cord,
                slr.lecturer_id,
                slr.responsibility_id,
                slr.assigned_by,
                slr.assigned_at,
                ps.subject                                                AS subject_name,
                y.year                                                    AS year,
                TRIM(CONCAT(COALESCE(u.honorifics,''), ' ',
                            u.first_name, ' ', u.last_name))              AS lecturer_name,
                u.initials                                                AS lecturer_initials,
                lr.responsibility                                         AS responsibility_name
             FROM subject_lecture_relations slr
             LEFT JOIN practical_subjects ps ON slr.subject_cord = ps.subject_cord
             LEFT JOIN years y               ON ps.year_id = y.id
             LEFT JOIN users u               ON slr.lecturer_id = u.id
             LEFT JOIN lecturer_responsibility lr ON slr.responsibility_id = lr.id
             ORDER BY ps.year_id ASC, slr.subject_cord ASC, u.last_name ASC"
        );
    }

    public function createAssignment($payload) {
        $responsibilityId = !empty($payload['responsibility_id']) ? (int)$payload['responsibility_id'] : null;

        $this->executeQuery(
            "INSERT INTO subject_lecture_relations
                (subject_cord, lecturer_id, responsibility_id, assigned_by)
             VALUES
                (:subject_cord, :lecturer_id, :responsibility_id, :assigned_by)",
            [
                'subject_cord'      => $payload['subject_cord'],
                'lecturer_id'       => (int)$payload['lecturer_id'],
                'responsibility_id' => $responsibilityId,
                'assigned_by'       => $payload['assigned_by'] ?? '',
            ]
        );
        return 'Assignment created successfully';
    }

    public function updateAssignment($payload) {
        $responsibilityId = !empty($payload['responsibility_id']) ? (int)$payload['responsibility_id'] : null;

        $this->executeQuery(
            "UPDATE subject_lecture_relations
             SET subject_cord      = :subject_cord,
                 lecturer_id       = :lecturer_id,
                 responsibility_id = :responsibility_id,
                 assigned_by       = :assigned_by
             WHERE id = :id",
            [
                'id'                => $payload['id'],
                'subject_cord'      => $payload['subject_cord'],
                'lecturer_id'       => (int)$payload['lecturer_id'],
                'responsibility_id' => $responsibilityId,
                'assigned_by'       => $payload['assigned_by'] ?? '',
            ]
        );
        return 'Assignment updated successfully';
    }

    public function deleteAssignment($id) {
        $this->executeQuery(
            "DELETE FROM subject_lecture_relations WHERE id = :id",
            ['id' => $id]
        );
        return 'Assignment deleted successfully';
    }
}
?>
