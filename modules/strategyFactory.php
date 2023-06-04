<?php

    class LoginCredentials
    {
        public $ref;

        public function setCredentials($x)
        {
            $ref = $x;
            $ref->login();
        }


    }
    
    class StrategyFactory
    {
        public $object;

        public function createObject($type)
        {
            if($type == "loginCredentials")
            {
                $this->object = new LoginCredentials();
            }
            
            return $this->object;
        }
    }
?>