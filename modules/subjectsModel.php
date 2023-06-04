<?php
    require_once 'assignInterface.php';
    require_once 'userModel.php';


    class SubjectModel implements Assign
    {
        public $id;
        public $Name;

        public $Time;
        public $mydb;
        public $subjectObj;
        public $subjectsArray = array();
        public $classesArray = array();

        //for studentModel Help in its function getSubjects()
        function __construct()
        {
            $this->mydb = DB::getInstance();
        }

        public function assignAll()
        {
            $query = "SELECT * from subjects_names";
            $queryResult = mysqli_query($this->mydb, $query);
            
            while($row = mysqli_fetch_assoc($queryResult))
            {
                $tempObj = new SubjectModel();
                $tempObj->id = $row['id'];
                $tempObj->Name = $row['subject_name'];
                array_push($this->subjectsArray, $tempObj);
            }


        }



        public function selectAllSubjects()
        {
            $subjects = new SubjectModel();
            $subjects->assignAll();
            $this->subjectObj = $subjects->subjectsArray;
            for($i = 0; $i < count($this->subjectObj); $i++)
            {
                array_push($this->subjectsArray, $this->subjectObj[$i]);
            }
            return $this->subjectsArray;
        }

        public function selectUserSubjects($userId)
        {
            $query = "SELECT sn.subject_name, snur.user_id, sn.id
                        FROM (select * from subject_name_user_relation where user_id=$userId)  snur	 
                        right JOIN subjects_names sn 
                        ON snur.subject_id = sn.id 
                     ";

            $queryResult = mysqli_query($this->mydb, $query);
            while($row = mysqli_fetch_assoc($queryResult))
            {
                $tempObj = array(
                    'subject_name' => $row['subject_name'],
                    'id' => $row['id'],
                    'checked' => $row['user_id'] == $userId ? "checked" : "",
                );
                array_push($this->subjectsArray, $tempObj);
            }
            return $this->subjectsArray;
        }
        public function selectUserClasses($userId)
        {
            $query = "SELECT sn.name, snur.user_id, sn.id
                        FROM (select * from classes_users_relation where user_id=$userId)  snur	 
                        right JOIN classes sn 
                        ON snur.class_id = sn.id 
                     ";

            $queryResult = mysqli_query($this->mydb, $query);
            while($row = mysqli_fetch_assoc($queryResult))
            {
                $tempObj = array(
                    'class_name' => $row['name'],
                    'id' => $row['id'],
                    'checked' => $row['user_id'] == $userId ? "checked" : "",
                );
                array_push($this->classesArray, $tempObj);
            }
            return $this->classesArray;
        }


    }
?>