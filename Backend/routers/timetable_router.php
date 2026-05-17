<?php
    namespace Backend\Routers;
    require_once __DIR__ . '/../controllers/timetable_controller.php';
    require_once __DIR__ . '/../middleware/validation.php';
    require_once __DIR__ . '/../middleware/jwtToken.php';
    use Backend\Controllers\TimetableController;
    use Backend\Middleware\Validation;
    use Backend\Middleware\JwtToken;
    use Backend\Utils\Route;
    class TimetableRouter{
        private $timetableController;
        private $router;
        private $validation;
        public function __construct(){
            $this->router = Route::getInstance();
            $this->timetableController = new TimetableController();
            $this->validation = new Validation();
            $this->routeTimetable();
            $this->router->dispatch();
        }

        private function routeTimetable(){
            $yearCreateValidation = function($req = null, $res = null){ $this->validation->yearCreate($req, $res); };
            $yearUpdateValidation = function($req = null, $res = null){ $this->validation->yearUpdate($req, $res); };
            $deleteValidation = function($req = null, $res = null){ $this->validation->deleteById($req, $res); };
            $lectureGroupCreateValidation = function($req = null, $res = null){ $this->validation->lectureGroupCreate($req, $res); };
            $lectureGroupUpdateValidation = function($req = null, $res = null){ $this->validation->lectureGroupUpdate($req, $res); };
            $labCreateValidation = function($req = null, $res = null){ $this->validation->labCreate($req, $res); };
            $labUpdateValidation = function($req = null, $res = null){ $this->validation->labUpdate($req, $res); };
            $timetableCreateValidation = function($req = null, $res = null){ $this->validation->timetableCreate($req, $res); };
            $timetableUpdateValidation = function($req = null, $res = null){ $this->validation->timetableUpdate($req, $res); };
            $settingsUpdateValidation = function($req = null, $res = null){ $this->validation->timetableSettingsUpdate($req, $res); };
            $settingsResetValidation = function($req = null, $res = null){ $this->validation->timetableSettingsReset($req, $res); };
            $columnHeadingCreateValidation = function($req = null, $res = null){ $this->validation->columnHeadingCreate($req, $res); };
            $columnHeadingUpdateValidation = function($req = null, $res = null){ $this->validation->columnHeadingUpdate($req, $res); };
            $timeSlotCreateValidation = function($req = null, $res = null){ $this->validation->timeSlotCreate($req, $res); };
            $timeSlotUpdateValidation = function($req = null, $res = null){ $this->validation->timeSlotUpdate($req, $res); };
            $subjectCreateValidation = function($req = null, $res = null){ $this->validation->subjectCreate($req, $res); };
            $subjectUpdateValidation = function($req = null, $res = null){ $this->validation->subjectUpdate($req, $res); };

            $authorMiddleware = function($req = null, $res = null){ (new JwtToken())->validateToken($req, $res); };
            $adminMiddleware  = JwtToken::requireRole('admin');

            $this->router->get('/',function($req, $res){ $this->timetableController->getALLTimeSchedules($req, $res);});
            $this->router->get('/getByYear',function ($req, $res){$this->timetableController->getTimeSchedulesByYear($req, $res);});
            $this->router->get('/temporary',function($req, $res){ $this->timetableController->getTemporaryTimeSchedules($req, $res);});
            $this->router->get('/subjectCodes',function($req, $res){ $this->timetableController->getSubjectCodes($req, $res);});
            $this->router->get('/years',function($req, $res){ $this->timetableController->getYears($req, $res);});
            $this->router->post('/years',[$authorMiddleware, $adminMiddleware, $yearCreateValidation],function($req, $res){ $this->timetableController->createYear($req, $res);});
            $this->router->post('/years/update',[$authorMiddleware, $adminMiddleware, $yearUpdateValidation],function($req, $res){ $this->timetableController->updateYear($req, $res);});
            $this->router->post('/years/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteYear($req, $res);});
            $this->router->get('/timeSlots',function($req, $res){ $this->timetableController->getTimeSlots($req, $res);});
            $this->router->get('/columnHeadings',function($req, $res){ $this->timetableController->getColumnHeadings($req, $res);});
            $this->router->get('/settings',function($req, $res){ $this->timetableController->getTimetableSettings($req, $res);});
            $this->router->get('/lectureGroups',function($req, $res){ $this->timetableController->getLectureGroups($req, $res);});
            $this->router->post('/lectureGroups',[$authorMiddleware, $adminMiddleware, $lectureGroupCreateValidation],function($req, $res){ $this->timetableController->createLectureGroup($req, $res);});
            $this->router->post('/lectureGroups/update',[$authorMiddleware, $adminMiddleware, $lectureGroupUpdateValidation],function($req, $res){ $this->timetableController->updateLectureGroup($req, $res);});
            $this->router->post('/lectureGroups/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteLectureGroup($req, $res);});
            $this->router->get('/labs',function($req, $res){ $this->timetableController->getLabs($req, $res);});
            $this->router->post('/labs',[$authorMiddleware, $adminMiddleware, $labCreateValidation],function($req, $res){ $this->timetableController->createLab($req, $res);});
            $this->router->post('/labs/update',[$authorMiddleware, $adminMiddleware, $labUpdateValidation],function($req, $res){ $this->timetableController->updateLab($req, $res);});
            $this->router->post('/labs/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteLab($req, $res);});
            $this->router->get('/cells',function($req, $res){ $this->timetableController->getTimetableCells($req, $res);});
            $this->router->post('/',[$authorMiddleware, $adminMiddleware, $timetableCreateValidation],function($req, $res){ $this->timetableController->createTimetableRecord($req, $res);});
            $this->router->post('/update',[$authorMiddleware, $adminMiddleware, $timetableUpdateValidation],function($req, $res){ $this->timetableController->updateTimetableRecord($req, $res);});
            $this->router->post('/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteTimetableRecord($req, $res);});
            $this->router->post('/settings/update',[$authorMiddleware, $adminMiddleware, $settingsUpdateValidation],function($req, $res){ $this->timetableController->updateTimetableSettings($req, $res);});
            $this->router->post('/settings/reset',[$authorMiddleware, $adminMiddleware, $settingsResetValidation],function($req, $res){ $this->timetableController->resetTimetableSettings($req, $res);});
            $this->router->post('/columnHeadings',[$authorMiddleware, $adminMiddleware, $columnHeadingCreateValidation],function($req, $res){ $this->timetableController->createColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/update',[$authorMiddleware, $adminMiddleware, $columnHeadingUpdateValidation],function($req, $res){ $this->timetableController->updateColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteColumnHeading($req, $res);});
            $this->router->post('/timeSlots',[$authorMiddleware, $adminMiddleware, $timeSlotCreateValidation],function($req, $res){ $this->timetableController->createTimeSlot($req, $res);});
            $this->router->post('/timeSlots/update',[$authorMiddleware, $adminMiddleware, $timeSlotUpdateValidation],function($req, $res){ $this->timetableController->updateTimeSlot($req, $res);});
            $this->router->post('/timeSlots/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteTimeSlot($req, $res);});
            $this->router->post('/subjects',[$authorMiddleware, $adminMiddleware, $subjectCreateValidation],function($req, $res){ $this->timetableController->createSubject($req, $res);});
            $this->router->post('/subjects/update',[$authorMiddleware, $adminMiddleware, $subjectUpdateValidation],function($req, $res){ $this->timetableController->updateSubject($req, $res);});
            $this->router->post('/subjects/delete',[$authorMiddleware, $adminMiddleware, $deleteValidation],function($req, $res){ $this->timetableController->deleteSubject($req, $res);});
        }
    }
    new TimetableRouter;
?>
