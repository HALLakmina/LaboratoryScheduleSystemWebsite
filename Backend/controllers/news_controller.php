<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/news_service.php';
require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . '/../utils/logger.php';
require_once __DIR__ . '/../utils/response.php';

use Backend\Services\NewsService;
use Backend\Services\LogsService;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Backend\Utils\Response;
use Exception;

class NewsController {
    private $newsService;
    private $logsService;

    public function __construct() {
        $this->newsService = new NewsService();
        $this->logsService = new LogsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        if (!is_array($payload)) {
            $payload = [];
        }
        if (!empty($_POST)) {
            $payload = array_merge($payload, $_POST);
        }
        return $payload;
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    private function dbLog(string $type, string $table, $old, $new): void {
        $actor = $this->getAuthUser();
        $this->logsService->logAction($type, $table, $old, $new, isset($actor['userId']) ? (int)$actor['userId'] : null);
    }

    public function getAll($req = null, $res = null) {
        try {
            $respond = $this->newsService->getAll();
            Response::success('News fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[NewsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function getById($req = null, $res = null) {
        try {
            $id = $req['query']['id'] ?? '';
            if (trim((string)$id) === '') {
                Response::error('400', 'id is required.');
            }
            $respond = $this->newsService->getById($id);
            Response::success('News fetched successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[NewsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = (int)($actor['userId'] ?? 0);
            $payload['updated_by'] = (int)($actor['userId'] ?? 0);
            $respond = $this->newsService->create($payload, $_FILES['image'] ?? null);
            $this->dbLog('INSERT', 'news', null, $payload);
            Response::success('News created successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[NewsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = (int)($actor['userId'] ?? 0);
            $old = $this->logsService->fetchRowById('news', $payload['id'] ?? null);
            $respond = $this->newsService->update($payload, $_FILES['image'] ?? null);
            $this->dbLog('UPDATE', 'news', $old, $payload);
            Response::success('News updated successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[NewsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('news', $payload['id'] ?? null);
            $respond = $this->newsService->delete($payload['id']);
            $this->dbLog('DELETE', 'news', $old, null);
            Response::success('News deleted successfully', $respond);
        } catch (Exception $e) {
            Logger::error('[NewsController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            Response::error('500', 'An internal error occurred');
        }
    }
}
?>
