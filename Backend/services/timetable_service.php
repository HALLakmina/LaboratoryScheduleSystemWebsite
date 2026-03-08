<?php
    namespace Backend\Services; 
    require_once __DIR__ . '/../DB/dbConnection.php';
    use Backend\DB\DbConnection;
    use Exception;
    class TimetableService{
        public function getAllTimeSchedules(){
            $DB_CON = new  DbConnection;
                    $query = "SELECT 
                        tc.cell_number AS cell_id,
                        lg.group_name,
                        l.lab_name AS lab,
                        t.action,
                        ps.subject_cord,
                        ps.subject,
                        y.year,
                        CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name
                    FROM timetable t

                    LEFT JOIN timetable_cells tc 
                        ON t.cell_id = tc.id

                    LEFT JOIN lecture_groups lg 
                        ON t.lecture_group_id = lg.id

                    LEFT JOIN labs l
                        ON t.lab_id = l.id

                    LEFT JOIN practical_subjects ps 
                        ON t.subject_cord = ps.subject_cord

                    LEFT JOIN years y 
                        ON ps.year_id = y.id

                    LEFT JOIN subject_lecture_relations sl 
                        ON ps.subject_cord = sl.subject_cord

                    LEFT JOIN users u 
                        ON sl.lecturer_id = u.id

                    ORDER BY tc.cell_number ASC;";
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
                        tc.cell_number AS cell_id,
                        lg.group_name,
                        l.lab_name AS lab,
                        t.action,
                        ps.subject_cord,
                        ps.subject,
                        y.year,
                        CONCAT(u.first_name, ' ', u.last_name) AS lecturer_name
                    FROM timetable t
                    LEFT JOIN timetable_cells tc ON t.cell_id = tc.id
                    LEFT JOIN lecture_groups lg ON t.lecture_group_id = lg.id
                    LEFT JOIN labs l ON t.lab_id = l.id
                    INNER JOIN practical_subjects ps ON t.subject_cord = ps.subject_cord
                    INNER JOIN years y ON ps.year_id = y.id
                    LEFT JOIN subject_lecture_relations sl ON ps.subject_cord = sl.subject_cord
                    LEFT JOIN users u ON sl.lecturer_id = u.id
                    WHERE y.year = :year
                    ORDER BY tc.cell_number ASC";
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

        public function getSubjectCodes(){
            $DB_CON = new DbConnection;
            $query = "SELECT DISTINCT ps.subject_cord, ps.subject, ps.year_id, y.year 
                      FROM practical_subjects ps 
                      LEFT JOIN years y ON ps.year_id = y.id 
                      ORDER BY ps.subject_cord";
            $DB_CON->selectData($query);
            $result = $DB_CON->fetchAll();
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return $result;
        }

        public function getYears(){
            $DB_CON = new DbConnection;
            $query = "SELECT id, year FROM years ORDER BY year";
            $DB_CON->selectData($query);
            $result = $DB_CON->fetchAll();
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return $result;
        }
    }
?>
