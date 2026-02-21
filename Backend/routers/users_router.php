<?php
namespace Backend\Routers;
require_once __DIR__ . "/../services/users_service.php";
require_once __DIR__ . "/../middleware/validation.php";
require_once __DIR__ . "/../middleware/jwtToken.php";
use Backend\Services\UsersService;
use Backend\Utils\Route;
use Backend\Middleware\Validation;
use Backend\Middleware\JwtToken;
use Exception;
    class UsersRouter {
        private $usersService;
        private $router;
        private $validation;
        public function __construct(){
            $this->router = Route::getInstance();
            $this->usersService = new UsersService();
            $this->validation = new Validation();
            $this->routeService();
            $this->router->dispatch();
        }
        private function routeService(): void{

            $this->router->get('/', function($req=null, $res=null){
                try{
                    $respond = $this->usersService->getAll();
                    echo json_encode([
                        "status"  => "200",
                        "data" => $respond,
                        "message" => 'Data get Successfully'
                    ]);
                    exit;
                }
                catch(Exception $e){
                    echo json_encode([
                        "status"  => "500",
                        "message" => $e->getMessage()
                    ]);
                }
            });
            $validationMiddleware = function($req=null, $res=null) {
                $this->validation->userBodyDataValidation($req);
            };
            $authorMiddleware =function($req=null, $res=null){
                $jwt = new JwtToken();
                $jwt->getJwtToken($req);
            };

            $this->router->post('/', [$validationMiddleware,$authorMiddleware], function($req=null, $res=null){
                
                try{
                    $payload = $req["body"];
                    $respond = $this->usersService->create($payload);
                    $respond = "User Create";
                    echo json_encode([
                        "status"  => "200",
                        "data" => $respond,
                        "message" => 'User created successfully'
                    ]);
                    exit;
                }
                catch(Exception $e){
                    echo json_encode([
                        "status"  => "500",
                        "message" => $e->getMessage()
                    ]);
                }
            });
            
            $this->router->post('/login', function($req=null, $res=null){
                try{
                    $email = $req["body"]['email'];
                    $foundUser = $this->usersService->getByEmail($email);
                    if(!$foundUser){
                        echo json_encode([
                            "status"  => "400",
                            "message" => 'Worn Email or Password.'
                        ]);
                        exit;
                    }
                    
                    $isMatchPassword = password_verify($req["body"]['password'], $foundUser[0]['password']);
                    if($isMatchPassword){
                        $isDeployment = false;
                        $jwt = new JwtToken();
                        $jwtToken = $jwt->createJwtToken($foundUser[0]['initials'].$foundUser[0]['last_name'], $foundUser[0]['role'], $foundUser[0]['email']);
                        $res['cookie']("token",$jwtToken, [
                            'expires' => time() + (60 * 60 * 24), 
                            'path' => '/', 
                            'secure' => $isDeployment,
                            'httponly' => true, 
                            'samesite' => $isDeployment ? "none":"lax",
                        ]);
                        echo json_encode([
                            "status"  => "200",
                            "jwtToken" => $jwtToken,
                            "message" => 'User Login Successfully.'
                        ]);
                        exit;

                    }else{
                        echo json_encode([
                            "status"  => "400",
                            "message" => 'Worn Email or Password.'
                        ]);
                        exit;
                    }
                }
                catch(Exception $e){
                    echo json_encode([
                        "status"  => "500",
                        "message" => $e->getMessage()
                    ]);
                }
            });
        }
    }
    new UsersRouter;
?>