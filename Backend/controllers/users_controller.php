<?php
namespace Backend\Controllers;
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../middleware/jwtToken.php";
use Backend\Services\UsersService;
use Backend\Middleware\JwtToken;
use Exception;

class UsersController {
    private $usersService;

    public function __construct() {
        $this->usersService = new UsersService();
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
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * Create user - POST /api/v1/user
     * Body: user payload (validated by middleware)
     */
    public function create($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            $this->usersService->create($payload);
            echo json_encode([
                'status' => '200',
                'data' => 'User created',
                'message' => 'User created successfully'
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

    private function validateUserPayload($payload, $requirePassword = true) {
        $requiredFields = [
            'initials',
            'initials_stand_for',
            'first_name',
            'last_name',
            'nic',
            'email',
            'mobile_number',
            'role',
        ];

        foreach ($requiredFields as $field) {
            if (trim((string)($payload[$field] ?? '')) === '') {
                return $field . ' is required.';
            }
        }

        if ($requirePassword && trim((string)($payload['password'] ?? '')) === '') {
            return 'password is required.';
        }

        if (!in_array($payload['role'] ?? '', ['admin', 'lecturer'], true)) {
            return 'role must be admin or lecturer.';
        }

        return null;
    }

    public function update($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];
            if (trim((string)($payload['id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.'
                ]);
                exit;
            }

            $validationMessage = $this->validateUserPayload($payload, false);
            if ($validationMessage !== null) {
                echo json_encode([
                    'status' => '400',
                    'message' => $validationMessage,
                ]);
                exit;
            }

            $this->usersService->update($payload);
            echo json_encode([
                'status' => '200',
                'data' => 'User updated',
                'message' => 'User updated successfully'
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
            $payload = $req['body'] ?? [];
            if (trim((string)($payload['id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'id is required.'
                ]);
                exit;
            }

            $this->usersService->delete($payload['id']);
            echo json_encode([
                'status' => '200',
                'data' => 'User deleted',
                'message' => 'User deleted successfully'
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

    public function resetPassword($req = null, $res = null) {
        try {
            $payload = $req['body'] ?? [];

            if (trim((string)($payload['target_user_id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'target_user_id is required.'
                ]);
                exit;
            }

            if (trim((string)($payload['actor_user_id'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'actor_user_id is required.'
                ]);
                exit;
            }

            if (trim((string)($payload['current_password'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'current_password is required.'
                ]);
                exit;
            }

            if (trim((string)($payload['new_password'] ?? '')) === '') {
                echo json_encode([
                    'status' => '400',
                    'message' => 'new_password is required.'
                ]);
                exit;
            }

            $this->usersService->resetPassword($payload);
            echo json_encode([
                'status' => '200',
                'data' => 'Password reset',
                'message' => 'User password reset successfully'
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

    /**
     * User login - POST /api/v1/user/login
     * Body: { "email": "...", "password": "..." }
     */
    public function login($req = null, $res = null) {
        try {
            $body = $req['body'] ?? [];

            if (empty($body['email']) || empty($body['password'])) {
                echo json_encode([
                    'status' => '400',
                    'message' => 'Email and password are required.'
                ]);
                exit;
            }

            $email = trim($body['email']);
            $password = $body['password'];

            $foundUser = $this->usersService->getByEmail($email);

            if (!$foundUser || empty($foundUser)) {
                echo json_encode([
                    'status' => '401',
                    'message' => 'Wrong email or password.'
                ]);
                exit;
            }

            $user = $foundUser[0];

            if (!password_verify($password, $user['password'])) {
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
            $jwtToken = $jwt->createJwtToken($userName, $user['role'], $user['email']);

            $isDeployment = false;
            $res = $res ?? [];
            if (is_callable($res['cookie'] ?? null)) {
                $res['cookie']('token', $jwtToken, [
                    'expires' => time() + (60 * 60 * 24),
                    'path' => '/',
                    'secure' => $isDeployment,
                    'httponly' => true,
                    'samesite' => $isDeployment ? 'none' : 'lax',
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
            echo json_encode([
                'status' => '500',
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    /**
     * User logout - POST /api/v1/user/logout
     */
    public function logout($req = null, $res = null) {
        try {
            $isDeployment = false;
            $res = $res ?? [];

            if (is_callable($res['cookie'] ?? null)) {
                $res['cookie']('token', '', [
                    'expires' => time() - 3600,
                    'path' => '/',
                    'secure' => $isDeployment,
                    'httponly' => true,
                    'samesite' => $isDeployment ? 'none' : 'lax',
                ]);
            }

            echo json_encode([
                'status' => '200',
                'message' => 'Logout successful.'
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
}
