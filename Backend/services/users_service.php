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
            $query = "INSERT INTO users 
                        (initials, initials_stand_for, first_name, last_name, honorifics, nic, email, mobile_number, password, role, created_by, created_at, updated_by, updated_at)
                        VALUES (
                            :initials,
                            :initials_stand_for,
                            :first_name,
                            :last_name,
                            :honorifics,
                            :nic,
                            :email,
                            :mobile_number,
                            :password,
                            :role,
                            :created_by,
                            :created_at,
                            :updated_by,
                            :updated_at
                        )";
            $property=[
                'initials'=>$payload['initials'],
                'initials_stand_for'=>$payload['initials_stand_for'],
                'first_name'=>$payload['first_name'],
                'last_name'=>$payload['last_name'],
                'honorifics'=>$payload['honorifics'],
                'nic'=>$payload['nic'],
                'email'=>$payload['email'],
                'mobile_number'=>$payload['mobile_number'],
                'password'=>$hashPassword,
                'role'=>$payload['role'],
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
            $query = "SELECT * FROM users";
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
            $query = "SELECT * FROM users WHERE email = :email LIMIT 1";
            $property = [
                'email'=>$email
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