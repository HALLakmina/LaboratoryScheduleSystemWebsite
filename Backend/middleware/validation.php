<?php
namespace Backend\Middleware;

require_once __DIR__ . '/../vendor/autoload.php';

use Exception;
use Respect\Validation\Validator as v;
use Respect\Validation\Exceptions\NestedValidationException;
use Backend\Utils\Route;
class Validation{

    public function userBodyDataValidation($req){
        $payload = $req['body'];
        try{
            v::key('full_name', v::stringType()->notEmpty()->length(3, 150))
            ->key('name_with_initials',v::optional( v::stringType()->notEmpty()->length(2, 60)->regex('/^[A-Za-z.\s]+$/')))
            ->key('honorifics', v::optional(v::in(['Mr','Mrs','Ms','Miss','Dr','Prof','Eng'])))
            ->key('nic', v::oneOf(v::regex('/^[0-9]{9}[VvXx]$/')->setName('NIC (old format)'),v::regex('/^[0-9]{12}$/')->setName('NIC (new format)')))
            ->key('email', v::email()->length(3, 254))
            ->key('mobile_number', v::phone()->length(9,10)->regex('/^0?7[0-9]{8}$/'))
            ->key('password', v::stringType()->notEmpty()->length(8, 128)->regex('/[A-Z]/')->regex('/[a-z]/')->regex('/\d/')->regex('/[^A-Za-z0-9]/')->regex('/[@$!%*?&]/'))
            ->key('confirmPassword', v::equals($payload['password']))
            ->key('access', v::in(['admin', 'user', 'editor']))
            ->assert($payload);
            return true;
        }catch(NestedValidationException $exception){
            $errors = [];
            $messages = $exception->getMessages();
            
            foreach ($messages as $message) {
                $errors[] = $message;
            }
            
            http_response_code(400);
            echo json_encode([
                "status" => "400",
                "message" => "Validation failed",
                "errors" => $errors
            ]);
        }
    }
}
?>