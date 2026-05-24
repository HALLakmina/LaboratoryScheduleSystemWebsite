<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . '/../utils/route.php';
require_once __DIR__ . '/../utils/logger.php';

use Backend\Services\LogsService;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Exception;

class LogsController {
    private $service;

    public function __construct() {
        $this->service = new LogsService();
    }

    private function jsonResponse($status, $message, $data = null) {
        http_response_code((int)$status);
        echo json_encode(['status' => (string)$status, 'data' => $data, 'message' => $message]);
        exit;
    }

    public function getActionLogs($req = null, $res = null) {
        try {
            $query       = $req['query'] ?? [];
            $page        = max(1, (int)($query['page'] ?? 1));
            $perPageRaw  = $query['per_page'] ?? '20';
            $perPage     = ($perPageRaw === '0' || strtolower((string)$perPageRaw) === 'all')
                           ? 0
                           : max(1, min(500, (int)$perPageRaw));

            $result = $this->service->getActionLogs($page, $perPage);
            $this->jsonResponse('200', 'Action logs fetched successfully', $result);
        } catch (Exception $e) {
            Logger::error('[LogsController] ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            $this->jsonResponse('500', 'An internal error occurred');
        }
    }
}
?>
