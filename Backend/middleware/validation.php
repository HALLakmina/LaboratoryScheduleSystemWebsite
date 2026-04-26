<?php
namespace Backend\Middleware;

require_once __DIR__ . '/../vendor/autoload.php';

use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class Validation {
    private function getPayload($req) {
        $payload = $req['body'] ?? [];

        if (!is_array($payload)) {
            $payload = [];
        }

        if (!empty($_POST)) {
            $payload = array_merge($payload, $_POST);
        }

        return $payload;
    }

    private function failValidation(array $errors) {
        http_response_code(400);
        echo json_encode([
            'status' => '400',
            'message' => 'Validation failed',
            'errors' => array_values($errors),
        ]);
        exit;
    }

    private function assertPayload($payload, $validator) {
        try {
            $validator->assert($payload);
        } catch (NestedValidationException $exception) {
            $messages = array_values(array_filter($exception->getMessages()));
            $this->failValidation($messages ?: ['Validation failed.']);
        }
    }

    private function userRule($requireId = false, $requirePassword = true) {
        $validator = v::arrayType()
            ->key('initials', v::stringType()->notEmpty()->length(1, 100)->setName('initials'))
            ->key('initials_stand_for', v::stringType()->notEmpty()->length(3, 255)->regex('/^[A-Za-z.\s]+$/')->setName('initials_stand_for'))
            ->key('first_name', v::stringType()->notEmpty()->length(3, 150)->setName('first_name'))
            ->key('last_name', v::stringType()->notEmpty()->length(3, 150)->setName('last_name'))
            ->key('honorifics', v::optional(v::in(['Mr', 'Mrs', 'Ms', 'Miss', 'Dr', 'Prof', 'Eng']))->setName('honorifics'))
            ->key(
                'nic',
                v::oneOf(
                    v::regex('/^[0-9]{9}[VvXx]$/')->setName('nic'),
                    v::regex('/^[0-9]{12}$/')->setName('nic')
                )
            )
            ->key('email', v::email()->length(3, 254)->setName('email'))
            ->key('mobile_number', v::regex('/^0?7[0-9]{8}$/')->setName('mobile_number'))
            ->key('role', v::in(['admin', 'lecturer'])->setName('role'))
            ->key('created_by', v::optional(v::stringType()->notEmpty()->length(1, 255))->setName('created_by'))
            ->key('updated_by', v::optional(v::stringType()->notEmpty()->length(1, 255))->setName('updated_by'));

        if ($requirePassword) {
            $validator = $validator->key(
                'password',
                v::stringType()
                    ->notEmpty()
                    ->length(8, 128)
                    ->regex('/[A-Z]/')
                    ->regex('/[a-z]/')
                    ->regex('/\d/')
                    ->regex('/[^A-Za-z0-9]/')
                    ->setName('password')
            );
        } else {
            $validator = $validator->key(
                'password',
                v::optional(
                    v::stringType()
                        ->length(8, 128)
                        ->regex('/[A-Z]/')
                        ->regex('/[a-z]/')
                        ->regex('/\d/')
                        ->regex('/[^A-Za-z0-9]/')
                )->setName('password')
            );
        }

        if ($requireId) {
            $validator = $validator->key('id', v::intVal()->positive()->setName('id'));
        }

        return $validator;
    }

    public function userCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->userRule(false, true));
    }

    public function userUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->userRule(true, false));
    }

    public function userDelete($req = null, $res = null) {
        $this->assertPayload(
            $this->getPayload($req),
            v::arrayType()->key('id', v::intVal()->positive()->setName('id'))
        );
    }

    public function userResetPassword($req = null, $res = null) {
        $this->assertPayload(
            $this->getPayload($req),
            v::arrayType()
                ->key('target_user_id', v::intVal()->positive()->setName('target_user_id'))
                ->key('actor_user_id', v::intVal()->positive()->setName('actor_user_id'))
                ->key('current_password', v::stringType()->notEmpty()->length(1, 255)->setName('current_password'))
                ->key(
                    'new_password',
                    v::stringType()
                        ->notEmpty()
                        ->length(8, 128)
                        ->regex('/[A-Z]/')
                        ->regex('/[a-z]/')
                        ->regex('/\d/')
                        ->regex('/[^A-Za-z0-9]/')
                        ->setName('new_password')
                )
                ->key('updated_by', v::optional(v::stringType()->notEmpty()->length(1, 255))->setName('updated_by'))
        );
    }

    public function userLogin($req = null, $res = null) {
        $this->assertPayload(
            $this->getPayload($req),
            v::arrayType()
                ->key('email', v::email()->length(3, 254)->setName('email'))
                ->key('password', v::stringType()->notEmpty()->length(1, 255)->setName('password'))
        );
    }

    public function userBodyDataValidation($req = null, $res = null) {
        $this->userCreate($req, $res);
    }
}
?>
