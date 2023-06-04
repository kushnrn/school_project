<?php
    include_once '../../userModel.php';
    include_once '../../assignInterface.php';
    if(session_status() == PHP_SESSION_NONE){session_start();}

    class EmployeeModel implements Assign
    {
        public $usersObj;
        public $studentsArr = array();
        public $teachersArr = array();
        public $employeesArr = array();
        public $usersArr = array();

        public $mydb;

        function __construct()
        {
            $this->mydb = DB::getInstance();
        }

        public function selectAllUsers($condition = "")
        {
            $user = new user();
            $user->assignAll($condition);
            $this->usersObj = $user->usersArray;
            for($i = 0; $i < count($this->usersObj); $i++)
            {
                array_push($this->usersArr, $this->usersObj[$i]);
            }
            return $this->usersArr;
        }

        public function selectAllStudents()
        {
            $user = new user();
            $user->assignAll();
            $this->usersObj = $user->usersArray;
            for($i = 0; $i < count($this->usersObj); $i++)
            {
                if($this->usersObj[$i]->user_type == 3)
                {
                    array_push($this->studentsArr, $this->usersObj[$i]);
                }
            }
            return $this->studentsArr;
        }

        public function selectAllTeachers()
        {
            $user = new user();
            $user->assignAll();
            $this->usersObj = $user->usersArray;
            for($i = 0; $i < count($this->usersObj); $i++)
            {
                if($this->usersObj[$i]->user_type == 2)
                {
                    array_push($this->teachersArr, $this->usersObj[$i]);
                }
            }
            return $this->teachersArr;
        }


        public function selectAllEmployees()
        {
            $user = new user();
            $user->assignAll();
            $this->usersObj = $user->usersArray;
            for($i = 0; $i < count($this->usersObj); $i++)
            {
                if($this->usersObj[$i]->user_type == 1)
                {
                    array_push($this->employeesArr, $this->usersObj[$i]);
                }
            }
            return $this->employeesArr;
        }

        public function pwdEncryption($pwd)
        {
          $ciphering = "AES-128-CTR";
          
          $iv_length = openssl_cipher_iv_length($ciphering); 
          $options = 0; 
          
          $encryption_iv = '1234567891011121'; 
          
          $encryption_key = "OOpse314*%*"; 
          $encryption = openssl_encrypt($pwd, $ciphering,  $encryption_key, $options, $encryption_iv); 

          return $encryption;
        }

        public function deleteUser($id)
        {
            $query = "UPDATE users SET isDeleted = 1, date_modified = CURRENT_TIMESTAMP() WHERE id = $id";
            if($this->mydb->query($query) !== true)
            {
                echo "Something went wrong";
            }
        }

        public function reActivateUser($id)
        {
            $query = "UPDATE users SET isDeleted = 0, date_modified = CURRENT_TIMESTAMP() WHERE id = $id";
            if($this->mydb->query($query) !== true)
            {
                echo "Something went wrong";
            }
            header("Refresh:0");
        }

        public function assignAll()
        {
        }

        public function insertSubjectsToTeacher($teacherId, $subjectsIds)
        {
            $delete_subjects_query = "DELETE FROM  subject_name_user_relation WHERE user_id = $teacherId";
            if($this->mydb->query($delete_subjects_query) !== true)
                echo mysqli_error($this->mydb);

            # iterate over subjectsIds and insert into subject_name_user_relation table
            for($i = 0; $i < count($subjectsIds); $i++)
            {
                $query = "INSERT INTO subject_name_user_relation (user_id, subject_id) VALUES ($teacherId, $subjectsIds[$i])";
                if($this->mydb->query($query) !== true)
                    echo mysqli_error($this->mydb);
            }
        }
        public function insertClassesToUsers($user_id, $classesIds)
        {
            $delete_classes_query = "DELETE FROM  classes_users_relation WHERE user_id = $user_id";
            if($this->mydb->query($delete_classes_query) !== true)
                echo mysqli_error($this->mydb);

            # iterate over subjectsIds and insert into subject_name_user_relation table
            for($i = 0; $i < count($classesIds); $i++)
            {
                $query = "INSERT INTO classes_users_relation (user_id, class_id) VALUES ($user_id, $classesIds[$i])";
                if($this->mydb->query($query) !== true)
                    echo mysqli_error($this->mydb);
            }
        }

        public function updateUserProperties($userObj)
        {
            #function to update user in the database according to the properties from userObj
            $id = $userObj["id"];
            $firstName = $userObj["firstName"];
            $secondName = $userObj["secondName"];
            $userType = $userObj["userType"] ?? 0;
            $email = $userObj["email"];

            # instead of update do upsert
            $query = "SELECT * FROM users WHERE id = $id";
            $queryResult = mysqli_query($this->mydb, $query);
            $rows = mysqli_num_rows($queryResult);
            if($rows == 0)
            {
                $query = "INSERT INTO users (id, first_name, second_name, email, user_type, password, isDeleted) VALUES ($id, '$firstName', '$secondName', '$email', $userType, '', 0)";
                if($this->mydb->query($query) !== true)
                    echo mysqli_error($this->mydb);
                else
                {
                    echo "<div class='text-primary' style='font-size: 16px; margin-top:20px; text-align:center;'>Successfully added</div>";
                }
            }
            else
            {
                $query = "UPDATE users SET first_name = '$firstName', second_name = '$secondName', email = '$email' WHERE id = $id";
                if($this->mydb->query($query) !== true)
                    echo mysqli_error($this->mydb);
                else
                {
                    new SystemLog("Employee Updated User Properties", $_SESSION['loggedId']);
                    echo "<div class='text-primary' style='font-size: 16px; margin-top:20px; text-align:center;'>Successfully updated</div>";
                }
            }
        }

        public function getNumberOfUsersInRole($role)
        {
            $query = "SELECT * FROM users WHERE user_type = $role";
            $queryResult = mysqli_query($this->mydb, $query);
            return mysqli_num_rows($queryResult);
        }

    }
?>