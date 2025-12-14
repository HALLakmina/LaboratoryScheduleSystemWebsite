<?php
    namespace Backend\DB;
    require_once __DIR__ . '/../vendor/autoload.php';
    use Dotenv\Dotenv;
    use Exception;
    use PDO;
    use PDOException;
    class DbConnection{
        private $dotenv; 
        private $pdo;
        private $stmt;
        private $error;
        function __construct(){
            $this-> dotenv = Dotenv::createImmutable(__DIR__ . '/../');
            $this->dotenv->load();
            $this->bdCon();
        } 
        private function bdCon(){
            $dbSever = $_ENV['DB_HOST'];
            $dbUser = $_ENV['DB_USER'];
            $dbPassword = $_ENV['DB_PASSWORD'];
            $dbName = $_ENV['DB_NAME'];
            try{
                $this->pdo = new PDO("mysql:host=$dbSever;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPassword);
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }catch(Exception $e){
                die('Connection failed: ' . $e->getMessage());
            }
        }
        // public function getDbCon(){
        //     return $this->pdo;
        // }
        public function selectData($query){
            $this->stmt = $this->pdo->query($query);
        }
        public function selectDataByProperty($query, $property){
            $this->stmt = $this->pdo->prepare($query);
            $this->stmt->execute($property);
        }

        public function execute($query, $property){
            try{
                $this->stmt = $this->pdo->prepare($query);
                $this->stmt->execute($property);
                return true;
            }catch (PDOException $e) {
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
        }
        public function fetchOne(){
            try{
                $result = $this->stmt->fetch();
                return $result;
            }catch(PDOException $e){
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
        }
        public function fetchRow(){
            try{
                $result = $this->stmt->fetch(PDO::FETCH_ASSOC);
                return $result;
            }catch(PDOException $e){
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
        }
        public function fetchAll(){
            try{
                $result = $this->stmt->fetchAll(PDO::FETCH_ASSOC);
                return $result;
            }catch(PDOException $e){
                $this->error = "Error: " . $e->getMessage();
                return false;
            }
        }
        public function getError(){
            return $this->error;
        } 
    }
?>