<?php
require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class classModel
{
    public $id;
    public $name;

    public $mydb;

    public function __construct()
    {
        $this->mydb = DB::getInstance();
    }
    public function fetchClasses()
    {
        $query = "SELECT * FROM classes";
        $queryResult = mysqli_query($this->mydb, $query);
        $reportsArray = array();
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $reportsObj = new classModel();
            $reportsObj->id = $row['id'];
            $reportsObj->name = $row['name'];
            $reportsArray[] = $reportsObj;
        }
        return $reportsArray;

    }

}


require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


