<?php
    namespace Backend\Routers;
    require_once __DIR__ . '/../controllers/timetable_controller.php';
    use Backend\Controllers\TimetableController;
    use Backend\Utils\Route;
    class TimetableRouter{
        private $timetableController;
        private $router;
        private $validation;
        public function __construct(){
            $this->router = Route::getInstance();
            $this->timetableController = new TimetableController();
            $this->routeTimetable();
            $this->router->dispatch();
        }

        private function routeTimetable(){
            $this->router->get('/',function($req, $res){ $this->timetableController->getALLTimeSchedules($req, $res);});
            $this->router->get('/getByYear',function ($req, $res){$this->timetableController->getTimeSchedulesByYear($req, $res);});
            $this->router->get('/subjectCodes',function($req, $res){ $this->timetableController->getSubjectCodes($req, $res);});
            $this->router->get('/years',function($req, $res){ $this->timetableController->getYears($req, $res);});
            $this->router->get('/timeSlots',function($req, $res){ $this->timetableController->getTimeSlots($req, $res);});
            $this->router->get('/columnHeadings',function($req, $res){ $this->timetableController->getColumnHeadings($req, $res);});
            $this->router->get('/settings',function($req, $res){ $this->timetableController->getTimetableSettings($req, $res);});
            $this->router->get('/lectureGroups',function($req, $res){ $this->timetableController->getLectureGroups($req, $res);});
            $this->router->get('/labs',function($req, $res){ $this->timetableController->getLabs($req, $res);});
            $this->router->get('/cells',function($req, $res){ $this->timetableController->getTimetableCells($req, $res);});
            $this->router->post('/',function($req, $res){ $this->timetableController->createTimetableRecord($req, $res);});
            $this->router->post('/update',function($req, $res){ $this->timetableController->updateTimetableRecord($req, $res);});
            $this->router->post('/delete',function($req, $res){ $this->timetableController->deleteTimetableRecord($req, $res);});
            $this->router->post('/settings/update',function($req, $res){ $this->timetableController->updateTimetableSettings($req, $res);});
            $this->router->post('/settings/reset',function($req, $res){ $this->timetableController->resetTimetableSettings($req, $res);});
            $this->router->post('/columnHeadings',function($req, $res){ $this->timetableController->createColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/update',function($req, $res){ $this->timetableController->updateColumnHeading($req, $res);});
            $this->router->post('/columnHeadings/delete',function($req, $res){ $this->timetableController->deleteColumnHeading($req, $res);});
            $this->router->post('/timeSlots',function($req, $res){ $this->timetableController->createTimeSlot($req, $res);});
            $this->router->post('/timeSlots/update',function($req, $res){ $this->timetableController->updateTimeSlot($req, $res);});
            $this->router->post('/timeSlots/delete',function($req, $res){ $this->timetableController->deleteTimeSlot($req, $res);});
            $this->router->post('/subjects',function($req, $res){ $this->timetableController->createSubject($req, $res);});
            $this->router->post('/subjects/update',function($req, $res){ $this->timetableController->updateSubject($req, $res);});
            $this->router->post('/subjects/delete',function($req, $res){ $this->timetableController->deleteSubject($req, $res);});
        }
    }
    new TimetableRouter;
?>
