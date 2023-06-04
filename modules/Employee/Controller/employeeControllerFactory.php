<?php
    require_once '../Model/employeeModel.php';
    require_once '../View/employeeView.php';
    require_once '../../subjectsModel.php';
    require_once '../../classModel.php';
    require_once '../../scheduledSubjects.php';
    require_once '../../cabinetModel.php';

    class EmployeeControllerFactory
    {
        public $object;

        public function createObject($type)
        {
            if($type == "employeeModel")
            {
                $this->object = new EmployeeModel();
            }
            else if($type == "subjectModel")
            {
                $this->object = new SubjectModel();
            }
            else if($type == "employeeView")
            {
                $this->object = new EmployeeView();
            }
            else if($type == "user")
            {
                $this->object = new User();
            }
            else if($type == "classModel")
            {
                $this->object = new classModel();
            }
            else if($type == "scheduledSubjects")
            {
                $this->object = new scheduledSubjects();
            }
            else if($type == "cabinetModel")
            {
                $this->object = new cabinetModel();
            }

            return $this->object;
        }

    }
?>