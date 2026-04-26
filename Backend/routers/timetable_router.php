<?php
    namespace Backend\Routers;
    require_once __DIR__ . '/../controllers/timetable_controller.php';
    require_once __DIR__ . '/../middleware/validation.php';
    use Backend\Controllers\TimetableController;
    use Backend\Middleware\Validation;
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

            $this->router->get('/',function($req, $res){ $this->timetableController->getALLTimeSchedules($req, $res);});
            $this->router->get('/getByYear',function ($req, $res){$this->timetableController->getTimeSchedulesByYear($req, $res);});
            $this->router->get('/temporary',function($req, $res){ $this->timetableController->getTemporaryTimeSchedules($req, $res);});
            $this->router->get('/subjectCodes',function($req, $res){ $this->timetableController->getSubjectCodes($req, $res);});
            $this->router->get('/years',function($req, $res){ $this->timetableController->getYears($req, $res);});
            $this->router->post('/years',[$yearCreateValidation],function($req, $res){ $this->timetableController->createYear($req, $res);});
            $this->router->post('/years/update',[$yearUpdateValidation],function($req, $res){ $this->timetableController->updateYear($req, $res);});
            $this->router->post('/years/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteYear($req, $res);});
            $this->router->get('/timeSlots',function($req, $res){ $this->timetableController->getTimeSlots($req, $res);});
            $this->router->get('/columnHeadings',function($req, $res){ $this->timetableController->getColumnHeadings($req, $res);});
            $this->router->get('/settings',function($req, $res){ $this->timetableController->getTimetableSettings($req, $res);});
            $this->router->get('/lectureGroups',function($req, $res){ $this->timetableController->getLectureGroups($req, $res);});
            $this->router->post('/lectureGroups',[$lectureGroupCreateValidation],function($req, $res){ $this->timetableController->createLectureGroup($req, $res);});
            $this->router->post('/lectureGroups/update',[$lectureGroupUpdateValidation],function($req, $res){ $this->timetableController->updateLectureGroup($req, $res);});
            $this->router->post('/lectureGroups/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteLectureGroup($req, $res);});
            $this->router->get('/labs',function($req, $res){ $this->timetableController->getLabs($req, $res);});
            $this->router->post('/labs',[$labCreateValidation],function($req, $res){ $this->timetableController->createLab($req, $res);});
            $this->router->post('/labs/update',[$labUpdateValidation],function($req, $res){ $this->timetableController->updateLab($req, $res);});
            $this->router->post('/labs/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteLab($req, $res);});
            $this->router->get('/cells',function($req, $res){ $this->timetableController->getTimetableCells($req, $res);});
            $this->router->post('/',[$timetableCreateValidation],function($req, $res){ $this->timetableController->createTimetableRecord($req, $res);});
            $this->router->post('/update',[$timetableUpdateValidation],function($req, $res){ $this->timetableController->updateTimetableRecord($req, $res);});
            $this->router->post('/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteTimetableRecord($req, $res);});
            $this->router->post('/settings/update',[$settingsUpdateValidation],function($req, $res){ $this->timetableController->updateTimetableSettings($req, $res);});
            $this->router->post('/settings/reset',[$settingsResetValidation],function($req, $res){ $this->timetableController->resetTimetableSettings($req, $res);});
            $this->router->post('/columnHeadings',[$columnHeadingCreateValidation],function($req, $res){ $this->timetableController->createColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/update',[$columnHeadingUpdateValidation],function($req, $res){ $this->timetableController->updateColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteColumnHeading($req, $res);});
            $this->router->post('/timeSlots',[$timeSlotCreateValidation],function($req, $res){ $this->timetableController->createTimeSlot($req, $res);});
            $this->router->post('/timeSlots/update',[$timeSlotUpdateValidation],function($req, $res){ $this->timetableController->updateTimeSlot($req, $res);});
            $this->router->post('/timeSlots/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteTimeSlot($req, $res);});
            $this->router->post('/subjects',[$subjectCreateValidation],function($req, $res){ $this->timetableController->createSubject($req, $res);});
            $this->router->post('/subjects/update',[$subjectUpdateValidation],function($req, $res){ $this->timetableController->updateSubject($req, $res);});
            $this->router->post('/subjects/delete',[$deleteValidation],function($req, $res){ $this->timetableController->deleteSubject($req, $res);});
        }
    }
    new TimetableRouter;
?>
