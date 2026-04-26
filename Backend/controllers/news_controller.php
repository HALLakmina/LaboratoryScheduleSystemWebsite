<?php
namespace Backend\Controllers;

require_once __DIR__ . '/../services/news_service.php';

use Backend\Services\NewsService;
use Exception;

class NewsController {
    private $newsService;

    public function __construct() {
        $this->newsService = new NewsService();
    }

    public function getAll($req = null, $res = null) {
        try {
            $respond = $this->newsService->getAll();
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'News fetched successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function getById($req = null, $res = null) {
        try {
            $id = $req['query']['id'] ?? '';
            if (trim((string)$id) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.'
                ]);
                exit;
            }

            $respond = $this->newsService->getById($id);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'News fetched successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->newsService->create($payload, $_FILES['image'] ?? null);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'News created successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->newsService->update($payload, $_FILES['image'] ?? null);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'News updated successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $respond = $this->newsService->delete($payload['id']);
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'News deleted successfully'
            ]);
            exit;
        } catch (Exception $e) {
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
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
}
?>
