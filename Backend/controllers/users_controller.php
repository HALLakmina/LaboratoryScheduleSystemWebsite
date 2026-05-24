<?php
namespace Backend\Controllers;

require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . '/../services/logs_service.php';
require_once __DIR__ . "/../middleware/jwtToken.php";
require_once __DIR__ . '/../utils/logger.php';

use Backend\Services\UsersService;
use Backend\Services\LogsService;
use Backend\Middleware\JwtToken;
use Backend\Utils\Route;
use Backend\Utils\Logger;
use Exception;

class UsersController {
    private $usersService;
    private $logsService;

    public function __construct() {
        $this->usersService = new UsersService();
        $this->logsService  = new LogsService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
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
            $respond = $this->usersService->getAll();
            $actor = $this->getAuthUser();
            Logger::info('[UsersController::getAll]', ['user' => $actor['userName'] ?? 'anonymous', 'count' => count($respond)]);
            echo json_encode([
                'status'  => '200',
                'data'    => $respond,
                'message' => 'Data get successfully',
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function create($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['created_by'] = $actor['userName'] ?? null;
            $payload['updated_by'] = $actor['userName'] ?? null;
            $this->usersService->create($payload);
            $logPayload = $payload;
            unset($logPayload['password']);
            $this->dbLog('INSERT', 'users', null, $logPayload);
            echo json_encode([
                'status'  => '200',
                'data'    => 'User created',
                'message' => 'User created successfully',
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('users', $payload['id'] ?? null);
            if ($old !== null) unset($old['password']);
            $this->usersService->update($payload);
            $logPayload = $payload;
            unset($logPayload['password']);
            $this->dbLog('UPDATE', 'users', $old, $logPayload);
            echo json_encode([
                'status'  => '200',
                'data'    => 'User updated',
                'message' => 'User updated successfully',
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $old = $this->logsService->fetchRowById('users', $payload['id'] ?? null);
            if ($old !== null) unset($old['password']);
            $this->usersService->delete($payload['id']);
            $this->dbLog('DELETE', 'users', $old, null);
            echo json_encode([
                'status'  => '200',
                'data'    => 'User deleted',
                'message' => 'User deleted successfully',
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function resetPassword($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $old = $this->logsService->fetchRowById('users', $payload['id'] ?? null);
            if ($old !== null) unset($old['password']);
            $this->usersService->resetPassword($payload);
            $this->dbLog('UPDATE', 'users', $old, ['id' => $payload['id'] ?? null, 'action' => 'password_reset']);
            echo json_encode([
                'status'  => '200',
                'data'    => 'Password reset',
                'message' => 'User password reset successfully',
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function login($req = null, $res = null) {
        try {
            $body  = $this->getPayload($req);
            $email = trim($body['email']);
            $password = $body['password'];

            $foundUser = $this->usersService->getByEmail($email);

            if (!$foundUser || empty($foundUser)) {
                http_response_code(401);
                echo json_encode(['status' => '401', 'message' => 'Wrong email or password.']);
                exit;
            }

            $user = $foundUser[0];

            if (!password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode(['status' => '401', 'message' => 'Wrong email or password.']);
                exit;
            }

            $jwt = new JwtToken();
            $userName = trim(($user['initials'] ?? '') . ' ' . ($user['last_name'] ?? ''));
            if ($userName === '') {
                $userName = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            }
            $jwtToken = $jwt->createJwtToken($userName, $user['role'], $user['email'], $user['id']);

            $isSecure = ($_ENV['APP_ENV'] ?? 'local') === 'production';
            $res = $res ?? [];
            if (is_callable($res['cookie'] ?? null)) {
                $res['cookie']('token', $jwtToken, [
                    'expires'  => time() + (60 * 60 * 24),
                    'path'     => '/',
                    'secure'   => $isSecure,
                    'httponly' => true,
                    'samesite' => $isSecure ? 'none' : 'lax',
                ]);
            }

            Logger::info('[UsersController::login]', ['userId' => $user['id'], 'role' => $user['role']]);

            echo json_encode([
                'status'    => '200',
                'message'   => 'Login successful.',
                'jwtToken'  => $jwtToken,
                'user'      => [
                    'id'         => $user['id'] ?? null,
                    'email'      => $user['email'] ?? null,
                    'role'       => $user['role'],
                    'first_name' => $user['first_name'] ?? null,
                    'last_name'  => $user['last_name'] ?? null,
                ],
            ]);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }

    public function logout($req = null, $res = null) {
        try {
            $isSecure = ($_ENV['APP_ENV'] ?? 'local') === 'production';
            $res = $res ?? [];
            if (is_callable($res['cookie'] ?? null)) {
                $res['cookie']('token', '', [
                    'expires'  => time() - 3600,
                    'path'     => '/',
                    'secure'   => $isSecure,
                    'httponly' => true,
                    'samesite' => $isSecure ? 'none' : 'lax',
                ]);
            }
            echo json_encode(['status' => '200', 'message' => 'Logout successful.']);
            exit;
        } catch (Exception $e) {
            Logger::error('[UsersController] ' . $e->getMessage(), ['file' => $e->getFile(), 'line' => $e->getLine()]);
            http_response_code(500);
            echo json_encode(['status' => '500', 'message' => 'An internal error occurred']);
            exit;
        }
    }
}
