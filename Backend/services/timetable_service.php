<?php
    namespace Backend\Services; 
    require_once __DIR__ . '/../DB/dbConnection.php';
    use Backend\DB\DbConnection;
    use Exception;
    class TimetableService{
        public function getAllTimeSchedules(){
            $DB_CON = new  DbConnection;
            $query = "SELECT 
                t.cell_id,
                t.practical_group,
                t.Action,
                s.Subject_cord,
                s.Subject,
                s.year,
                l.full_name 
            FROM 
                timetable t
                INNER JOIN 
                    practical_subject s
                ON 
                    t.Subject_cord = s.Subject_cord 
                INNER JOIN 
                    lecture_details l
                ON 
                    s.lecture_id = l.lecture_id";
            $DB_CON->selectData($query);
            $result = $DB_CON->fetchAll(); 
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            };
            return $result;
        }
        public function getTimeSchedulesByYear($year){
            $DB_CON = new  DbConnection;
            $query = "SELECT 
                t.id,
                t.practical_group,
                t.Action,
                s.Subject_cord,
                s.Subject,
                s.year,
                l.full_name 
            FROM 
                timetable t
                INNER JOIN 
                    practical_subject s
                ON 
                    t.Subject_cord = s.Subject_cord 
                INNER JOIN 
                    lecture_details l
                ON 
                    s.lecture_id = l.lecture_id
            WHERE
                s.year = :year";
            $property= [
                'year'=>$year
            ];
            $DB_CON->selectDataByProperty($query, $property);
            $result = $DB_CON->fetchAll(); 
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return $result;
        } 
    }
?>