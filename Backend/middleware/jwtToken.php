<?php
    namespace Backend\Middleware;
    require_once __DIR__ . '/../vendor/autoload.php';
    require_once __DIR__ . '/../utils/route.php';
    use Firebase\JWT\JWT;
    use Firebase\JWT\Key;
    use Backend\Utils\Route;
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
        public function createJwtToken($userName, $role, $email){

            $jwt_secret_key = $this->secret_key;

            // Payload
            $payload = [
                'iss' => 'http://'.$this->domain,        // Issuer
                'aud' => 'http://'.$this->domain,        // Audience
                'iat' => time(),                    // Issued at
                'nbf' => time(),                    // Not before
                'exp' => time() + (60 * 60*24),        // Expiration time (24 hour)
                'data' => [                         // Custom data
                    'userName'=>$userName,
                    'role' => $role,
                    'email' => $email
                ]
            ];

            // Generate token
            $jwt = JWT::encode($payload, $jwt_secret_key, 'HS256');
            return $jwt;
        }
        public function validateToken($req = null, $res = null) {
            $jwtToken = is_callable($req['cookie'] ?? null) ? $req['cookie']('token') : null;

            if (!$jwtToken) {
                http_response_code(401);
                echo json_encode(['error' => 'No token found']);
                exit;
            }

            try {
                $decoded = JWT::decode($jwtToken, new Key($this->secret_key, 'HS256'));
                Route::getInstance()->request['user'] = (array)$decoded->data;
            } catch (Exception $e) {
                http_response_code(401);
                echo json_encode(['error' => 'Invalid token: ' . $e->getMessage()]);
                exit;
            }
        }
    }

?>