<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';
require_once __DIR__ . '/../utils/logger.php';

use Backend\DB\DbConnection;
use Backend\Utils\Logger;
use Exception;

class LogsService {

    private function executeQuery($query, $property) {
        $DB_CON = new DbConnection();
        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            throw new Exception($DB_CON->getError() ?: 'SQL query error');
        }
        return $result;
    }

    public function logAction(string $actionType, string $tableName, $oldData, $newData, ?int $changedBy): void {
        try {
            $oldJson = $oldData !== null ? json_encode($oldData) : null;
            $newJson = $newData !== null ? json_encode($newData) : null;

            $this->executeQuery(
                "INSERT INTO database_modification_logs
                    (action_type, table_name, old_data, new_data, changed_by)
                 VALUES
                    (:action_type, :table_name, :old_data, :new_data, :changed_by)",
                [
                    'action_type' => $actionType,
                    'table_name'  => $tableName,
                    'old_data'    => $oldJson,
                    'new_data'    => $newJson,
                    'changed_by'  => $changedBy,
                ]
            );
        } catch (Exception $e) {
            Logger::error('Failed to write action log to DB', [
                'action_type' => $actionType,
                'table_name'  => $tableName,
                'error'       => $e->getMessage(),
            ]);
        }
    }

    public function fetchRowById(string $table, $id): ?array {
        $allowed = [
            'lecturer_responsibility', 'subject_lecture_relations',
            'users', 'news', 'lecturer_requests',
            'years', 'labs', 'lecture_groups', 'timetable',
            'timetable_column_headings', 'timetable_time_slots', 'practical_subjects',
        ];
        if (!in_array($table, $allowed, true)) {
            return null;
        }
        $db = new DbConnection();
        $db->selectDataByProperty("SELECT * FROM `{$table}` WHERE id = :id LIMIT 1", ['id' => $id]);
        $row = $db->fetchRow();
        return $row !== false ? $row : null;
    }

    public function getActionLogs(int $page, int $perPage): array {
        $countDb = new DbConnection();
        $countDb->selectData("SELECT COUNT(*) AS total FROM database_modification_logs");
        $countRow = $countDb->fetchRow();
        if ($countRow === false) {
            throw new Exception('Failed to fetch log count');
        }
        $total = (int)$countRow['total'];

        $baseQuery = "SELECT
                dml.log_id,
                dml.action_type,
                dml.table_name,
                dml.old_data,
                dml.new_data,
                dml.changed_at,
                u.id          AS user_id,
                u.first_name,
                u.last_name,
                u.email,
                u.role
             FROM database_modification_logs dml
             LEFT JOIN users u ON dml.changed_by = u.id
             ORDER BY dml.changed_at DESC";

        $dataDb = new DbConnection();
        if ($perPage === 0) {
            $dataDb->selectData($baseQuery);
        } else {
            $offset = ($page - 1) * $perPage;
            $dataDb->selectData("{$baseQuery} LIMIT {$perPage} OFFSET {$offset}");
        }

        $rows = $dataDb->fetchAll();
        if ($rows === false) {
            throw new Exception('Failed to fetch action logs');
        }

        return [
            'logs'        => $rows,
            'total'       => $total,
            'page'        => $perPage === 0 ? 1 : $page,
            'per_page'    => $perPage === 0 ? $total : $perPage,
            'total_pages' => $perPage === 0 ? ($total > 0 ? 1 : 0) : ($total > 0 ? (int)ceil($total / $perPage) : 0),
        ];
    }
}
?>
