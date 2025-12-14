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
                            :full_name,
                            :name_with_initials,
                            :honorifics,
                            :nic,
                            :email,
                            :mobile_number,
                            :hashPassword,
                            :access,
                            :created_by,
                            :created_at,
                            :updated_by,
                            :updated_at
                        )";
            $property=[
                'full_name'=>$payload['full_name'],
                'name_with_initials'=>$payload['name_with_initials'],
                'honorifics'=>$payload['honorifics'],
                'nic'=>$payload['nic'],
                'email'=>$payload['email'],
                'mobile_number'=>$payload['mobile_number'],
                'hashPassword'=>$hashPassword,
                'access'=>$payload['access'],
                'created_by'=>$payload['created_by'],
                'created_at'=>$created_at,
                'updated_by'=>$payload['updated_by'],
                'updated_at'=>$updated_at,
            ];
            $result = $DB_CON->execute($query, $property);

            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return 'User insert successfully';
        }

        public function getAll(){
            $DB_CON = new  DbConnection;
            $query = "SELECT * FROM user_details";
            $DB_CON->selectData($query);
            $result = $DB_CON->fetchAll(); 
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return $result;
        }
        public function getByEmail($email){
            $DB_CON = new  DbConnection;
            $query = "SELECT * FROM user_details WHERE email = '$email' LIMIT 1";
            $property = [
                'email'=>$email
            ];
            $DB_CON->execute($query, $property);
            $result = $DB_CON->fetchAll();
            if($result === false){
                $error = $DB_CON->getError();
                throw new Exception($error ? $error : 'Sql server sql query error');
            }
            return $result;
        }
    }
?>