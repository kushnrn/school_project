<?php

require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class cabinetModel
{
    public $id;
    public $number;

    public $mydb;

    public function __construct()
    {
        $this->mydb = DB::getInstance();
    }

    public function fetchCabinets()
    {
        $query = "SELECT * FROM cabinets";
        $queryResult = mysqli_query($this->mydb, $query);
        $reportsArray = array();
        while ($row = mysqli_fetch_assoc($queryResult)) {
            $reportsObj = new cabinetModel();
            $reportsObj->id = $row['id'];
            $reportsObj->number = $row['number'];
            $reportsArray[] = $reportsObj;
        }
        return $reportsArray;

    }

}


require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


