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
            $validationMessage = $this->validateCreatePayload($payload);
            if ($validationMessage !== null) {
                echo json_encode([
                    'status' => '400',
                    'message' => $validationMessage,
                ]);
                exit;
            }

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
            if (trim((string)($payload['id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.',
                ]);
                exit;
            }

            $validationMessage = $this->validateUpdatePayload($payload);
            if ($validationMessage !== null) {
                echo json_encode([
                    'status' => '400',
                    'message' => $validationMessage,
                ]);
                exit;
            }

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
            if (trim((string)($payload['id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.',
                ]);
                exit;
            }

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
        if (!empty($_POST)) {
            $payload = array_merge($payload, $_POST);
        }
        return $payload;
    }

    private function validateCreatePayload($payload) {
        $requiredFields = ['title', 'created_by', 'updated_by'];
        foreach ($requiredFields as $field) {
            if (trim((string)($payload[$field] ?? '')) === '') {
                return $field . ' is required.';
            }
        }

        return null;
    }

    private function validateUpdatePayload($payload) {
        $requiredFields = ['title', 'updated_by'];
        foreach ($requiredFields as $field) {
            if (trim((string)($payload[$field] ?? '')) === '') {
                return $field . ' is required.';
            }
        }

        return null;
    }
}
?>
