<?php
require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

class scheduledSubjects
{
    public $id;
    public $subject_name;
    public $subject_id;
    public $class_id;
    public $class_name;
    public $subject_date;
    public $subject_order;
    public $cabinet_id;
    public $cabinet_number;
    public $teacher_id;
    public $teacher_name;


    public $mydb;

    public function __construct()
    {
        $this->mydb = DB::getInstance();
    }

    public function fetchScheduledSubjects($subject_date=null, $class_id=null, $subject_id=null)
    {
        $query = "SELECT subject_name_id, class_id, date as subject_date, s_order as subject_order, cabinet_id, 
    teacher_id, c_names.id as class_id, c_names.name as class_name, subject_name,  cabinets.id, 
    cabinets.number as cabinet_number, teacher_id,
    CONCAT_WS(' ', users.first_name, users.second_name)  as teacher_name FROM scheduled_subject s_subjects
JOIN subjects_names s_name
ON s_subjects.subject_name_id=s_name.id
JOIN classes c_names
ON s_subjects.class_id=c_names.id
JOIN cabinets cabinets
ON s_subjects.cabinet_id=cabinets.id
JOIN users users
ON s_subjects.teacher_id=users.id
WHERE ".($subject_id ? " s_subjects.id=$subject_id" : "s_subjects.date='$subject_date' AND s_subjects.class_id=$class_id")."
order by subject_order asc

";

        $reportsArray = array();

        $queryResult = mysqli_query($this->mydb, $query);

        while ($row = mysqli_fetch_assoc($queryResult)) {
            $reportsObj = new scheduledSubjects();
            $reportsObj->id = $row['id'];
            $reportsObj->subject_name = $row['subject_name'];
            $reportsObj->subject_id = $row['subject_name_id'];
            $reportsObj->class_id = $row['class_id'];
            $reportsObj->class_name = $row['class_name'];
            $reportsObj->subject_date = $row['subject_date'];
            $reportsObj->subject_order = $row['subject_order'];
            $reportsObj->cabinet_id = $row['cabinet_id'];
            $reportsObj->cabinet_number = $row['cabinet_number'];
            $reportsObj->teacher_id = $row['teacher_id'];
            $reportsObj->teacher_name = $row['teacher_name'];

            $reportsArray[] = $reportsObj;
        }
        return $reportsArray;

    }

    public function upsertScheduledSubject($subject_name_id, $class_id, $subject_date, $subject_order, $cabinet_id, $teacher_id, $id=null)
    {
        if ($id) {
            $query = "UPDATE scheduled_subject SET subject_name_id=$subject_name_id, class_id=$class_id, date='$subject_date', s_order=$subject_order, cabinet_id=$cabinet_id, teacher_id=$teacher_id WHERE id=$id";
        }
        else {
            $query = "INSERT INTO scheduled_subject (subject_name_id, class_id, date, s_order, cabinet_id, teacher_id) VALUES ($subject_name_id, $class_id, '$subject_date', $subject_order, $cabinet_id, $teacher_id)";
        }
        $queryResult = mysqli_query($this->mydb, $query);
        return $queryResult;
    }

}


require_once 'DB.php';
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}


