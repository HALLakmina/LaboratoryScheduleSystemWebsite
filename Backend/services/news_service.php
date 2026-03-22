<?php
namespace Backend\Services;

require_once __DIR__ . '/../DB/dbConnection.php';

use Backend\DB\DbConnection;
use Exception;

class NewsService {
    private $imageStorageAbsolutePath;
    private $imageStorageRelativePath = 'storage/images/';

    public function __construct() {
        $this->imageStorageAbsolutePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'images';
    }

    public function getAll() {
        $DB_CON = new DbConnection();
        $query = "SELECT
                    n.id,
                    n.image_id,
                    n.title,
                    n.description,
                    n.start_date,
                    n.end_date,
                    n.start_at,
                    n.end_at,
                    n.created_at,
                    n.updated_at,
                    n.created_by,
                    n.updated_by,
                    i.original_name,
                    i.stored_name,
                    i.file_path,
                    i.file_size
                FROM news n
                LEFT JOIN images i ON n.image_id = i.id
                ORDER BY n.created_at DESC";
        $DB_CON->selectData($query);
        $result = $DB_CON->fetchAll();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result;
    }

    public function getById($id) {
        $DB_CON = new DbConnection();
        $query = "SELECT
                    n.id,
                    n.image_id,
                    n.title,
                    n.description,
                    n.start_date,
                    n.end_date,
                    n.start_at,
                    n.end_at,
                    n.created_at,
                    n.updated_at,
                    n.created_by,
                    n.updated_by,
                    i.original_name,
                    i.stored_name,
                    i.file_path,
                    i.file_size
                FROM news n
                LEFT JOIN images i ON n.image_id = i.id
                WHERE n.id = :id
                LIMIT 1";
        $DB_CON->selectDataByProperty($query, ['id' => $id]);
        $result = $DB_CON->fetchRow();
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return $result;
    }

    public function create($payload, $imageFile = null) {
        $DB_CON = new DbConnection();
        $imageId = null;

        if ($imageFile && !empty($imageFile['tmp_name'])) {
            $imageId = $this->createImageRecord($imageFile, (int)$payload['created_by']);
        }

        $query = "INSERT INTO news
                    (
                        image_id,
                        title,
                        description,
                        start_date,
                        end_date,
                        start_at,
                        end_at,
                        created_by,
                        updated_by
                    )
                    VALUES
                    (
                        :image_id,
                        :title,
                        :description,
                        :start_date,
                        :end_date,
                        :start_at,
                        :end_at,
                        :created_by,
                        :updated_by
                    )";

        $property = [
            'image_id' => $imageId,
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'start_date' => $payload['start_date'] ?: null,
            'end_date' => $payload['end_date'] ?: null,
            'start_at' => $payload['start_at'] ?: null,
            'end_at' => $payload['end_at'] ?: null,
            'created_by' => $payload['created_by'],
            'updated_by' => $payload['updated_by'],
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            if ($imageId !== null) {
                $this->deleteImageAssetById($imageId);
            }
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'News created successfully';
    }

    public function update($payload, $imageFile = null) {
        $existingNews = $this->getById($payload['id']);
        if (!$existingNews) {
            throw new Exception('News not found.');
        }

        $imageId = $existingNews['image_id'] ?? null;
        if ($imageFile && !empty($imageFile['tmp_name'])) {
            $newImageId = $this->createImageRecord($imageFile, (int)$payload['updated_by']);
            if ($imageId) {
                $this->deleteImageAssetById((int)$imageId);
            }
            $imageId = $newImageId;
        }

        $DB_CON = new DbConnection();
        $query = "UPDATE news
                    SET
                        image_id = :image_id,
                        title = :title,
                        description = :description,
                        start_date = :start_date,
                        end_date = :end_date,
                        start_at = :start_at,
                        end_at = :end_at,
                        updated_by = :updated_by
                    WHERE id = :id";

        $property = [
            'id' => $payload['id'],
            'image_id' => $imageId,
            'title' => $payload['title'],
            'description' => $payload['description'] ?? null,
            'start_date' => $payload['start_date'] ?: null,
            'end_date' => $payload['end_date'] ?: null,
            'start_at' => $payload['start_at'] ?: null,
            'end_at' => $payload['end_at'] ?: null,
            'updated_by' => $payload['updated_by'],
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        return 'News updated successfully';
    }

    public function delete($id) {
        $existingNews = $this->getById($id);
        if (!$existingNews) {
            throw new Exception('News not found.');
        }

        $DB_CON = new DbConnection();
        $query = "DELETE FROM news WHERE id = :id";
        $result = $DB_CON->execute($query, ['id' => $id]);
        if ($result === false) {
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        if (!empty($existingNews['image_id'])) {
            $this->deleteImageAssetById((int)$existingNews['image_id']);
        }

        return 'News deleted successfully';
    }

    private function createImageRecord($imageFile, $uploadedBy) {
        $this->ensureImageStorageDirectory();

        $originalName = $imageFile['name'] ?? '';
        $storedName = $this->generateStoredName($originalName);
        $relativeFilePath = $this->imageStorageRelativePath . $storedName;
        $absoluteFilePath = $this->imageStorageAbsolutePath . DIRECTORY_SEPARATOR . $storedName;

        if (!move_uploaded_file($imageFile['tmp_name'], $absoluteFilePath)) {
            throw new Exception('Failed to upload image file.');
        }

        $DB_CON = new DbConnection();
        $query = "INSERT INTO images
                    (
                        original_name,
                        stored_name,
                        file_path,
                        file_size,
                        uploaded_by
                    )
                    VALUES
                    (
                        :original_name,
                        :stored_name,
                        :file_path,
                        :file_size,
                        :uploaded_by
                    )";

        $property = [
            'original_name' => $originalName,
            'stored_name' => $storedName,
            'file_path' => $relativeFilePath,
            'file_size' => $imageFile['size'] ?? 0,
            'uploaded_by' => $uploadedBy,
        ];

        $result = $DB_CON->execute($query, $property);
        if ($result === false) {
            if (file_exists($absoluteFilePath)) {
                unlink($absoluteFilePath);
            }
            $error = $DB_CON->getError();
            throw new Exception($error ? $error : 'Sql server sql query error');
        }

        $DB_CON->selectDataByProperty(
            "SELECT id FROM images WHERE stored_name = :stored_name ORDER BY id DESC LIMIT 1",
            ['stored_name' => $storedName]
        );
        $imageRow = $DB_CON->fetchRow();
        if (!$imageRow || empty($imageRow['id'])) {
            throw new Exception('Failed to find uploaded image record.');
        }

        return (int)$imageRow['id'];
    }

    private function deleteImageAssetById($imageId) {
        $DB_CON = new DbConnection();
        $DB_CON->selectDataByProperty("SELECT id, file_path FROM images WHERE id = :id LIMIT 1", ['id' => $imageId]);
        $imageRow = $DB_CON->fetchRow();
        if (!$imageRow) {
            return;
        }

        $absoluteFilePath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $imageRow['file_path']);
        if (file_exists($absoluteFilePath)) {
            unlink($absoluteFilePath);
        }

        $DB_CON->execute("DELETE FROM images WHERE id = :id", ['id' => $imageId]);
    }

    private function ensureImageStorageDirectory() {
        if (!is_dir($this->imageStorageAbsolutePath)) {
            mkdir($this->imageStorageAbsolutePath, 0777, true);
        }
    }

    private function generateStoredName($originalName) {
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $randomName = bin2hex(random_bytes(16));
        return $extension ? $randomName . '.' . strtolower($extension) : $randomName;
    }
}
?>
