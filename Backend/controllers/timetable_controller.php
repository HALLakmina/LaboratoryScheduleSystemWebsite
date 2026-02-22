<?php
    namespace Backend\Controllers;
    require_once __DIR__ . "/../services/timetable_service.php";
    use Backend\Services\TimetableService;
use Exception;

    class TimetableController{
        private $timetableService;
        public function __construct(){
            $this->timetableService = new TimetableService();
        }
        public function getAllTimeSchedules($req=null, $res=null){
            try{
                $respond = $this->timetableService->getAllTimeSchedules();
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
        }

        public function getTimeSchedulesByYear($req=null, $res=null){
            $year  = $req['query']['year'] ?? '' ;
            // var_dump($req);
            echo $year;
            try{
                $respond = $this->timetableService->getTimeSchedulesByYear($year);
                echo json_encode([
                    "status"  => "200",
                    "data" => $respond,
                    "message" => 'Data get Successfully'
                ]);
                exit;
            }catch(Exception $e){
                echo json_encode([
                    "status"  => "500",
                    "message" => $e->getMessage()
                ]);
            }
        }

        public function getSubjectCodes($req=null, $res=null){
            try{
                $respond = $this->timetableService->getSubjectCodes();
                echo json_encode([
                    "status"  => "200",
                    "data" => $respond,
                    "message" => 'Subject codes fetched successfully'
                ]);
                exit;
            }catch(Exception $e){
                echo json_encode([
                    "status"  => "500",
                    "message" => $e->getMessage()
                ]);
            }
        }

        public function getYears($req=null, $res=null){
            try{
                $respond = $this->timetableService->getYears();
                echo json_encode([
                    "status"  => "200",
                    "data" => $respond,
                    "message" => 'Years fetched successfully'
                ]);
                exit;
            }catch(Exception $e){
                echo json_encode([
                    "status"  => "500",
                    "message" => $e->getMessage()
                ]);
            }
        }
    }

?>