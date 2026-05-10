<?php
namespace Backend\Controllers;
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../middleware/jwtToken.php";
use Backend\Services\UsersService;
use Backend\Middleware\JwtToken;
use Backend\Utils\Route;
use Exception;

class UsersController {
    private $usersService;

    public function __construct() {
        $this->usersService = new UsersService();
    }

    private function getPayload($req) {
        $payload = $req['body'] ?? [];
        return is_array($payload) ? $payload : [];
    }

    private function getAuthUser() {
        return Route::getInstance()->request['user'] ?? [];
    }

    /**
     * Get all users - GET /api/v1/user
     */
    public function getAll($req = null, $res = null) {
        try {
            $respond = $this->usersService->getAll();
            echo json_encode([
                'status' => '200',
                'data' => $respond,
                'message' => 'Data get successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
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
            echo json_encode([
                'status' => '200',
                'data' => 'User created',
                'message' => 'User created successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $this->usersService->update($payload);
            echo json_encode([
                'status' => '200',
                'data' => 'User updated',
                'message' => 'User updated successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function delete($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $this->usersService->delete($payload['id']);
            echo json_encode([
                'status' => '200',
                'data' => 'User deleted',
                'message' => 'User deleted successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    public function resetPassword($req = null, $res = null) {
        try {
            $payload = $this->getPayload($req);
            $actor = $this->getAuthUser();
            $payload['updated_by'] = $actor['userName'] ?? null;
            $this->usersService->resetPassword($payload);
            echo json_encode([
                'status' => '200',
                'data' => 'Password reset',
                'message' => 'User password reset successfully'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    /**
     * User login - POST /api/v1/user/login
     * Body: { "email": "...", "password": "..." }
     */
    public function login($req = null, $res = null) {
        try {
            $body = $this->getPayload($req);

            $email = trim($body['email']);
            $password = $body['password'];

            $foundUser = $this->usersService->getByEmail($email);

            if (!$foundUser || empty($foundUser)) {
                http_response_code(401);
                echo json_encode([
                    'status' => '401',
                    'message' => 'Wrong email or password.'
                ]);
                exit;
            }

            $user = $foundUser[0];

            if (!password_verify($password, $user['password'])) {
                http_response_code(401);
                echo json_encode([
                    'status' => '401',
                    'message' => 'Wrong email or password.'
                ]);
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

            echo json_encode([
                'status' => '200',
                'message' => 'Login successful.',
                'jwtToken' => $jwtToken,
                'user' => [
                    'id' => $user['id'] ?? null,
                    'email' => $user['email'] ?? null,
                    'role' => $user['role'],
                    'first_name' => $user['first_name'] ?? null,
                    'last_name' => $user['last_name'] ?? null,
                ]
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }

    /**
     * User logout - POST /api/v1/user/logout
     */
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

            echo json_encode([
                'status' => '200',
                'message' => 'Logout successful.'
            ]);
            exit;
        } catch (Exception $e) {
            error_log('[UsersController] ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            echo json_encode([
                'status' => '500',
                'message' => 'An internal error occurred'
            ]);
            exit;
        }
    }
}
