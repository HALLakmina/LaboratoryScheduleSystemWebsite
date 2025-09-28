<?php
    namespace Backend\DB;
    require_once __DIR__ . '/../vendor/autoload.php';
    use Dotenv\Dotenv;
    class DbConnection{
        private $dotenv; 
        function __construct(){
            $this-> dotenv = Dotenv::createImmutable(__DIR__ . '/../'); // __DIR__ is the directory where your .env file resides
            $this->dotenv->load();
        } 
        private function bdCon(){
            $db_sever = $_ENV['DB_HOST'];
            $db_user = $_ENV['DB_USER'];
            $db_password = $_ENV['DB_PASSWORD'];
            $db_name = $_ENV['DB_NAME'];
            $DB_CON = mysqli_connect($db_sever, $db_user, $db_password, $db_name) or die ("Connection failed");
            return $DB_CON;
            }
        public function getDbCon(){
            return $this->bdCon();
        }
    }
?>