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
        }
    }
    new TimetableRouter;
?>