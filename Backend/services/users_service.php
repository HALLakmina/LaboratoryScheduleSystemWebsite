<?php
    namespace Backend\Services; 
    require_once __DIR__ . '/../DB/dbConnection.php';
    use Backend\DB\DbConnection;
    use Exception;
    class UsersService{
        public function create($payload){
            $created_at= date('Y-m-d H:i:s');
            $updated_at= date('Y-m-d H:i:s');
            $hashPassword = password_hash($payload["password"], PASSWORD_DEFAULT);
            $DB_CON = new  DbConnection;
            $query = "INSERT INTO user_details 
                        (full_name, name_with_initials, honorifics, nic, email, mobile_number, password, access, created_by, created_at, updated_by, updated_at)
                        VALUES (
                            '{$payload['full_name']}',
                            '{$payload['name_with_initials']}',
                            '{$payload['honorifics']}',
                            '{$payload['nic']}',
                            '{$payload['email']}',
                            '{$payload['mobile_number']}',
                            '$hashPassword',
                            '{$payload['access']}',
                            '{$payload['created_by']}',
                            '$created_at',
                            '{$payload['updated_by']}',
                            '$updated_at'
                        )";
            $result = mysqli_query($DB_CON->getDbCon(),  $query);
            if($result === false){
                throw new Exception('Sql server sql query error');
            }
            return 'User insert successfully';
        }

        public function getAll(){
            $DB_CON = new  DbConnection;
            $query = "SELECT * FROM user_details";
            $result = mysqli_query($DB_CON->getDbCon(),  $query);
            if($result === false){
                throw new Exception('Sql server sql query error');
            }
            $all_data = mysqli_fetch_all($result, MYSQLI_ASSOC);
            return $all_data;
        }
        public function getByEmail($email){
            $DB_CON = new  DbConnection;
            $query = "SELECT * FROM user_details WHERE email = '$email' LIMIT 1";
            $result = mysqli_query($DB_CON->getDbCon(),  $query);
            if($result === false){
                throw new Exception('Sql server sql query error');
            }
            $data = mysqli_fetch_assoc($result);
            return $data;
        }
    }
?>