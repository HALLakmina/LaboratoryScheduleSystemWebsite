<?php
namespace Backend;
use Backend\Utils\Route;
use Exception;
require_once __DIR__ . '/utils/route.php';

header('Access-Control-Allow-Origin: *');
header("Content-Type: application/json");
class Server {
    public function __construct(){
        $this->server();
    }
    private static function server(){
        // $router = new Route;
        $router = Route::getInstance();
        try{
            $router->accessEndpoint('/api/v1/user', 'routers/users_router.php');
            $router->accessEndpoint('/api/v1/timetable', 'routers/timetable_router.php');
            echo json_encode([
                "status"  => "404",
                "message" => 'Endpoint Not Found'
            ]);
            exit;
        }
        catch(Exception $e){
            echo json_encode([
                "status"  => "500",
                "message" => $e->getMessage()
            ]);
        }
    }
}
new Server;
?>