<?php
    namespace Backend\Services; 
    require_once __DIR__ . '/../DB/dbConnection.php';
    use Backend\DB\DbConnection;
    use Exception;
    class UsersService{
        public function create($payload){
            echo 'run';
            // $DB_CON = new  DbConnection;
            // $query = "  INSERT 
            //             INTO 
            //             user_details
            //             (full_name, name_with_initials, honorifics, nic, email, mobile_number, password, access) 
            //             VALUES 
            //             ($payload[full_name], $payload[name_with_initials] $payload[honorifics], $payload[nic], $payload[email], $payload[mobile_number], $payload[password], $payload[access])";
            // $result = mysqli_query($DB_CON->getDbCon(),  $query);
            // if($result === false){
            //     throw new Exception('Sql server sql query error');
            // }
            // return 'User insert successfully';
        }

        public function getAll(){
            $DB_CON = new  DbConnection;
            $query = "SELECT * FROM user_details";
            $result = mysqli_query($DB_CON->getDbCon(),  $query);
            if($result === false){
                throw new Exception('Sql server sql query error');
            }
            return $result;
        }
    }
?>