<?php
    namespace Backend\Middleware;
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../utils/route.php';
    require_once __DIR__ . '/../utils/response.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Backend\Utils\Route;
    use Backend\Utils\Response;
    use Dotenv\Dotenv;
    use Exception;
    class JwtToken{
        
        private $secret_key = "";
        private $domain="";
        private $dotenv;
        public function __construct(){
            $this-> dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // __DIR__ is the directory where your .env file resides
            $this->dotenv->load();
            $this->secret_key = $_ENV['JWT_KEY'];
            $this->domain =$_ENV['DOMAIN'];
        }
        public function createJwtToken($userName, $role, $email, $userId) {

            $jwt_secret_key = $this->secret_key;

            $payload = [
                'iss' => 'http://'.$this->domain,
                'aud' => 'http://'.$this->domain,
                'iat' => time(),
                'nbf' => time(),
                'exp' => time() + (60 * 60 * 24),
                'data' => [
                    'userId'   => (int)$userId,
                    'userName' => $userName,
                    'role'     => $role,
                    'email'    => $email,
                ]
            ];

            $jwt = JWT::encode($payload, $jwt_secret_key, 'HS256');
            return $jwt;
        }
        public function validateToken($req = null, $res = null) {
            $jwtToken = is_callable($req['cookie'] ?? null) ? $req['cookie']('token') : null;

            if (!$jwtToken) {
                Response::error('401', 'No token found');
            }

            try {
                $decoded = JWT::decode($jwtToken, new Key($this->secret_key, 'HS256'));
                Route::getInstance()->request['user'] = (array)$decoded->data;
            } catch (Exception $e) {
                Response::error('401', 'Invalid or expired token.');
            }
        }

        public static function requireRole(string $requiredRole): callable {
            return function ($req = null, $res = null) use ($requiredRole) {
                $user = Route::getInstance()->request['user'] ?? [];
                if (($user['role'] ?? '') !== $requiredRole) {
                    Response::error('403', 'Forbidden: admin access required');
                }
            };
        }
    }

?>