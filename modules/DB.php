<?php 
    class DB        //Singleton Pattern
    {
        private static $instance;
        private $db_conn;

        private function __construct()
        {
            $this->db_conn = $this->databaseConnection();
            $this->databaseSelection();
        }

        private function databaseConnection()
        {
            if($db = mysqli_connect('0.0.0.0', 'root', 'root', 'oops', 3306))
            {
                return $db;
            }
            else
            {
                die(mysqli_error($db));
            }
        }

        private function databaseSelection()
        {
            mysqli_select_db($this->db_conn, 'oops');
        }

        public static function getInstance()
        {
            if(!isset(self::$instance)){
                self::$instance = new DB();
            }
            return self::$instance->db_conn;
        }
       
    }
?>