<?php
namespace Backend\Routers;
require_once __DIR__ . "/../services/users_service.php";
use Backend\Services\UsersService;
use Backend\Utils\Route;
use Exception;
    class UsersRouter {
        private $usersService;
        private $router;
        public function __construct(){
            $this->router = new Route;
            $this->usersService = new UsersService();
            $this->routeService();
        }
        private function routeService(){
            $this->router->get('/',function($req){
                try{
                    $respond = $this->usersService->getAll();
                    echo json_encode([
                        "status"  => "200",
                        "data" => $respond,
                        "message" => 'Data get Successfully'
                    ]);
                    return 0;
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