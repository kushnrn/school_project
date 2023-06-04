<?php
    require_once 'assignInterface.php';
    include_once 'DB.php';
    require_once 'strategyFactory.php';

    if(session_status() == PHP_SESSION_NONE){session_start();}

    
    
    class user
    {
        public $id;
        public $user_type;
        public $first_name;
        public $second_name;
        public $password;
        public $email;
        public $date_created;
        public $isDeleted;
        public $mydb;
        public $link;


        public $usersArray = array();

        // to help subjectsModel in its function
        function __construct()
        {
            $this->mydb = DB::getInstance();
        }

        public function assignAll($condition='')
        {
            $selected1 = $this->select("*", "users u", $condition, 'ORDER BY first_name, second_name, id');
            while($row1 = mysqli_fetch_assoc($selected1))
            {
                $usersObj = new user();

                $this->assignUsersAttributes($row1, $usersObj);

                $rowsArray = array();



                array_push($this->usersArray, $usersObj);
            }
        }

        public function select($which, $tableName, $condition = '', $order = '')
        {
            $selectQuery = "SELECT $which FROM $tableName $condition $order";
            $selectQueryResult = mysqli_query($this->mydb, $selectQuery);
            return $selectQueryResult;
        }

        public function assignUsersAttributes($row, $usersObj)
        {
                $usersObj->id = $row['id'];
                $usersObj->user_type = $row['user_type'];
                $usersObj->first_name = $row['first_name'];
                $usersObj->second_name = $row['second_name'];
                $usersObj->password = $row['password'];
                $usersObj->email = $row['email'];
                $usersObj->date_created = $row['date_created'];
                $usersObj->isDeleted = $row['isDeleted'];
        }

        public function employeeLogin($id, $password)
        {
            
            if(!empty($id) && !empty($password))
            {
                if(strpos($id, "'") === false && strpos($id, "'") === false)
                {
//                    $password = $this->pwdEncryption($password);
                        $selectQuery = "SELECT id, user_type, password FROM users WHERE id = $id AND password = '$password' AND user_type = 1";
                    $selectQueryResult = mysqli_query($this->mydb, $selectQuery);
                    $row = mysqli_fetch_assoc($selectQueryResult);
                    if(mysqli_num_rows($selectQueryResult))
                    {
                        $userId = $row['id'];
                        ?>
                        <div style="color:green">
                            <?php echo "Redirecting..."; ?>
                        </div>
                            <?php
                            $accessCode = $this->pwdEncryption($password);
                            $_SESSION['loggedId'] = $userId;
                        header( "refresh:1;url=/modules/Employee/Controller/employeeController.php?access=$accessCode&page=home");
                    }
                    else
                    {
                        ?>
                        <div style="color:red">
                            <?php echo "Invalid id or password"; ?>
                        </div>
                            <?php
                    }
                }
            }
        }

        public function pwdEncryption($strng)
        {
            $ciphering = "AES-128-CTR";
            
            $iv_length = openssl_cipher_iv_length($ciphering); 
            $options = 0; 
            
            $encryption_iv = '1234567891011121';
            
            $encryption_key = "OOpse314*%*";
            $encryption = openssl_encrypt($strng, $ciphering,  $encryption_key, $options, $encryption_iv); 

            return $encryption;
        }



    }
    
?>