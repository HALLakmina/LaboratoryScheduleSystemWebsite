<?php
namespace Backend\Middleware;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../services/timetable_service.php';

use Backend\Services\TimetableService;
use Respect\Validation\Exceptions\NestedValidationException;
use Respect\Validation\Validator as v;

class Validation {
    private $timetableService;

    public function __construct() {
        $this->timetableService = new TimetableService();
    }

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

    private function positiveIntRule($fieldName) {
        return v::key(
            $fieldName,
            v::anyOf(
                v::intVal()->positive(),
                v::stringType()->regex('/^[1-9][0-9]*$/')
            )->setName($fieldName)
        );
    }

    private function nonNegativeIntRule($fieldName) {
        return v::key(
            $fieldName,
            v::anyOf(
                v::intVal()->min(0),
                v::stringType()->regex('/^[0-9]+$/')
            )->setName($fieldName)
        );
    }

    private function optionalPositiveIntValueRule() {
        return v::optional(
            v::anyOf(
                v::intVal()->positive(),
                v::stringType()->regex('/^[1-9][0-9]*$/')
            )
        );
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
            ->key('role', v::in(['admin', 'lecturer'])->setName('role'));

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

    private function yearRule($requireId = false) {
        $validator = v::arrayType()
            ->key('year', v::stringType()->notEmpty()->length(1, 50)->setName('year'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function lectureGroupRule($requireId = false) {
        $validator = v::arrayType()
            ->key('group_name', v::stringType()->notEmpty()->length(1, 100)->setName('group_name'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function labRule($requireId = false) {
        $validator = v::arrayType()
            ->key('lab_name', v::stringType()->notEmpty()->length(1, 150)->setName('lab_name'))
            ->key('lab_location', v::stringType()->notEmpty()->length(1, 255)->setName('lab_location'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function subjectRule($requireId = false) {
        $validator = v::arrayType()
            ->key('subject_cord', v::stringType()->notEmpty()->length(1, 50)->setName('subject_cord'))
            ->key('subject', v::stringType()->notEmpty()->length(1, 255)->setName('subject'))
            ->key('year_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('year_id'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function timeSlotRule($requireId = false) {
        $validator = v::arrayType()
            ->key('time_slot_number', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('time_slot_number'))
            ->key('start_time', v::time('H:i')->setName('start_time'))
            ->key('end_time', v::time('H:i')->setName('end_time'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function columnHeadingRule($requireId = false) {
        $validator = v::arrayType()
            ->key('column_heading', v::stringType()->notEmpty()->length(1, 100)->setName('column_heading'))
            ->key('column_number', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('column_number'))
            ->key('column_heading_number', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('column_heading_number'))
            ->key('status', v::in(['active', 'deactive'])->setName('status'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function timetableRule($requireId = false) {
        $validator = v::arrayType()
            ->key('time_slot_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('time_slot_id'))
            ->key('column_heading_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('column_heading_id'))
            ->key('lecture_group_id', $this->optionalPositiveIntValueRule()->setName('lecture_group_id'))
            ->key('lab_id', $this->optionalPositiveIntValueRule()->setName('lab_id'))
            ->key('subject_cord', v::optional(v::stringType()->length(1, 50))->setName('subject_cord'))
            ->key('action', v::in(['active', 'free', 'cancel'])->setName('action'));

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    private function timetableSettingsRule($requireReset = false) {
        $validator = v::arrayType()
            ->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));

        if (!$requireReset) {
            $validator = $validator
                ->key('table_row_count', v::anyOf(v::intVal()->min(0), v::stringType()->regex('/^[0-9]+$/'))->setName('table_row_count'))
                ->key('table_column_count', v::anyOf(v::intVal()->min(0), v::stringType()->regex('/^[0-9]+$/'))->setName('table_column_count'))
                ->key('break_row_number', v::anyOf(v::intVal()->min(0), v::stringType()->regex('/^[0-9]+$/'))->setName('break_row_number'));
        }

        return $validator;
    }

    public function yearCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->yearRule(false));
    }

    public function yearUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->yearRule(true));
    }

    public function deleteById($req = null, $res = null) {
        $this->assertPayload(
            $this->getPayload($req),
            v::arrayType()->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'))
        );
    }

    public function lectureGroupCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->lectureGroupRule(false));
    }

    public function lectureGroupUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->lectureGroupRule(true));
    }

    public function labCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->labRule(false));
    }

    public function labUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->labRule(true));
    }

    public function subjectCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->subjectRule(false));
    }

    public function subjectUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->subjectRule(true));
    }

    public function timeSlotCreate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->timeSlotRule(false));

        $timeSlotNumber = (int)($payload['time_slot_number'] ?? 0);
        $startTime = (string)($payload['start_time'] ?? '');
        $endTime = (string)($payload['end_time'] ?? '');
        $settings = $this->timetableService->getTimetableSettings();
        $rowLimit = (int)($settings['table_row_count'] ?? 0);

        if (strtotime('1970-01-01 ' . $startTime) >= strtotime('1970-01-01 ' . $endTime)) {
            $this->failValidation(['end_time must be later than start_time.']);
        }

        if ($timeSlotNumber > $rowLimit) {
            $this->failValidation(['time_slot_number must be between 1 and the timetable Rows value.']);
        }

        if ($this->timetableService->countTimeSlots() >= $rowLimit) {
            $this->failValidation(['Time slot count has reached the timetable Rows limit.']);
        }

        if ($this->timetableService->isTimeSlotNumberTaken($timeSlotNumber)) {
            $this->failValidation(['time_slot_number must be unique.']);
        }
    }

    public function timeSlotUpdate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->timeSlotRule(true));

        $timeSlotNumber = (int)($payload['time_slot_number'] ?? 0);
        $startTime = (string)($payload['start_time'] ?? '');
        $endTime = (string)($payload['end_time'] ?? '');
        $settings = $this->timetableService->getTimetableSettings();
        $rowLimit = (int)($settings['table_row_count'] ?? 0);

        if (strtotime('1970-01-01 ' . $startTime) >= strtotime('1970-01-01 ' . $endTime)) {
            $this->failValidation(['end_time must be later than start_time.']);
        }

        if ($timeSlotNumber > $rowLimit) {
            $this->failValidation(['time_slot_number must be between 1 and the timetable Rows value.']);
        }

        if ($this->timetableService->isTimeSlotNumberTaken($timeSlotNumber, $payload['id'])) {
            $this->failValidation(['time_slot_number must be unique.']);
        }
    }

    public function columnHeadingCreate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->columnHeadingRule(false));

        $settings = $this->timetableService->getTimetableSettings();
        $columnLimit = (int)($settings['table_column_count'] ?? 0);
        $columnNumber = (int)($payload['column_number'] ?? 0);
        $columnHeadingNumber = (int)($payload['column_heading_number'] ?? 0);

        if ($columnNumber < 1 || $columnNumber > $columnLimit) {
            $this->failValidation(['column_number must be between 1 and the timetable Columns value.']);
        }

        if ($columnHeadingNumber > $columnLimit) {
            $this->failValidation(['column_heading_number must be between 1 and the timetable Columns value.']);
        }

        if ($this->timetableService->countColumnHeadings() >= $columnLimit) {
            $this->failValidation(['Column heading count has reached the timetable Columns limit.']);
        }

        if ($this->timetableService->isColumnNumberTaken($columnNumber)) {
            $this->failValidation(['column_number must be unique.']);
        }

        if ($this->timetableService->isColumnHeadingNumberTaken($columnHeadingNumber)) {
            $this->failValidation(['column_heading_number must be unique.']);
        }
    }

    public function columnHeadingUpdate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->columnHeadingRule(true));

        $settings = $this->timetableService->getTimetableSettings();
        $columnLimit = (int)($settings['table_column_count'] ?? 0);
        $columnNumber = (int)($payload['column_number'] ?? 0);
        $columnHeadingNumber = (int)($payload['column_heading_number'] ?? 0);

        if ($columnNumber < 1 || $columnNumber > $columnLimit) {
            $this->failValidation(['column_number must be between 1 and the timetable Columns value.']);
        }

        if ($columnHeadingNumber > $columnLimit) {
            $this->failValidation(['column_heading_number must be between 1 and the timetable Columns value.']);
        }

        if ($this->timetableService->isColumnNumberTaken($columnNumber, $payload['id'])) {
            $this->failValidation(['column_number must be unique.']);
        }

        if ($this->timetableService->isColumnHeadingNumberTaken($columnHeadingNumber, $payload['id'])) {
            $this->failValidation(['column_heading_number must be unique.']);
        }
    }

    public function timetableCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->timetableRule(false));
    }

    public function timetableUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->timetableRule(true));
    }

    public function timetableSettingsUpdate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->timetableSettingsRule(false));

        $rowCount = (int)($payload['table_row_count'] ?? 0);
        $columnCount = (int)($payload['table_column_count'] ?? 0);
        $breakRowNumber = (int)($payload['break_row_number'] ?? 0);

        if ($breakRowNumber < 0 || $breakRowNumber > $rowCount) {
            $this->failValidation(['break_row_number must be between 0 and table_row_count.']);
        }

        if ($this->timetableService->countColumnHeadings() > $columnCount) {
            $this->failValidation(['Existing column heading count is greater than the new Columns value.']);
        }

        if ($this->timetableService->countTimeSlots() > $rowCount) {
            $this->failValidation(['Existing time slot count is greater than the new Rows value.']);
        }
    }

    public function timetableSettingsReset($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->timetableSettingsRule(true));
    }

    private function newsRule($requireId = false) {
        $validator = v::arrayType()
            ->key('title', v::stringType()->notEmpty()->length(1, 255)->setName('title'))
            ->key('description', v::optional(v::stringType()->length(1, 5000))->setName('description'), false)
            ->key('start_date', v::optional(v::date('Y-m-d'))->setName('start_date'), false)
            ->key('end_date', v::optional(v::date('Y-m-d'))->setName('end_date'), false)
            ->key('start_at', v::optional(v::time('H:i'))->setName('start_at'), false)
            ->key('end_at', v::optional(v::time('H:i'))->setName('end_at'), false);

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    public function newsCreate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->newsRule(false));
    }

    public function newsUpdate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->newsRule(true));
    }

    public function newsDelete($req = null, $res = null) {
        $this->deleteById($req, $res);
    }

    private function lecturerRequestRule($requireId = false) {
        $validator = v::arrayType()
            ->key('lecturer_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('lecturer_id'))
            ->key('subject_id', v::stringType()->notEmpty()->length(1, 50)->setName('subject_id'))
            ->key('year_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('year_id'))
            ->key('lecture_group_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('lecture_group_id'))
            ->key('timetable_time_slot_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('timetable_time_slot_id'))
            ->key('timetable_column_heading_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('timetable_column_heading_id'))
            ->key('date', v::date('Y-m-d')->setName('date'))
            ->key('lecturer_request', v::stringType()->notEmpty()->length(1, 1000)->setName('lecturer_request'))
            ->key('action', v::optional(v::in(['requested', 'confirmed', 'canceled']))->setName('action'), false)
            ->key('lab_id', v::optional(v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/')))->setName('lab_id'), false)
            ->key('admin_message', v::optional(v::stringType()->length(1, 255))->setName('admin_message'), false);

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    public function lecturerRequestCreate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->lecturerRequestRule(false));

        $requestDate = strtotime((string)($payload['date'] ?? ''));
        $today = strtotime(date('Y-m-d'));
        if ($requestDate === false || $requestDate < $today) {
            $this->failValidation(['date must be today or a future date.']);
        }
    }

    public function lecturerRequestUpdate($req = null, $res = null) {
        $payload = $this->getPayload($req);
        $this->assertPayload($payload, $this->lecturerRequestRule(true));

        $requestDate = strtotime((string)($payload['date'] ?? ''));
        $today = strtotime(date('Y-m-d'));
        if ($requestDate === false || $requestDate < $today) {
            $this->failValidation(['date must be today or a future date.']);
        }

        if (($payload['action'] ?? '') === 'confirmed' && trim((string)($payload['lab_id'] ?? '')) === '') {
            $this->failValidation(['lab_id is required when confirming a lecturer request.']);
        }

        if (($payload['action'] ?? '') === 'canceled' && trim((string)($payload['admin_message'] ?? '')) === '') {
            $this->failValidation(['admin_message is required when canceling a lecturer request.']);
        }
    }

    public function lecturerRequestDelete($req = null, $res = null) {
        $this->deleteById($req, $res);
    }

    // ── Lecturer Responsibility ───────────────────────────────────────

    private function responsibilityRule($requireId = false) {
        $validator = v::arrayType()
            ->key('responsibility', v::stringType()->notEmpty()->length(1, 100)->setName('responsibility'))
            ->key('responsible_level', v::optional(v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/')))->setName('responsible_level'), false);

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    public function responsibilityCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->responsibilityRule(false));
    }

    public function responsibilityUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->responsibilityRule(true));
    }

    // ── Subject–Lecturer Assignment ───────────────────────────────────

    private function assignmentRule($requireId = false) {
        $validator = v::arrayType()
            ->key('subject_cord', v::stringType()->notEmpty()->length(1, 20)->setName('subject_cord'))
            ->key('lecturer_id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('lecturer_id'))
            ->key('responsibility_id', v::optional(v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/')))->setName('responsibility_id'), false);

        if ($requireId) {
            $validator = $validator->key('id', v::anyOf(v::intVal()->positive(), v::stringType()->regex('/^[1-9][0-9]*$/'))->setName('id'));
        }

        return $validator;
    }

    public function assignmentCreate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->assignmentRule(false));
    }

    public function assignmentUpdate($req = null, $res = null) {
        $this->assertPayload($this->getPayload($req), $this->assignmentRule(true));
    }
}
?>
