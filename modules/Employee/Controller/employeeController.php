<?php
//require_once '../../../vendor/autoload.php';

use Ramsey\Uuid\Uuid;

require_once 'employeeControllerFactory.php';
$factory = new EmployeeControllerFactory();
$employeeModelTemp = $factory->createObject("employeeModel");
$employees = $employeeModelTemp->selectAllEmployees();

for ($i = 0; $i < count($employees); $i++) {
    if ($_REQUEST['access'] == $employeeModelTemp->pwdEncryption($employees[$i]->password)) {
        break;
    }
    die("No Permission");
}

?>

<!DOCTYPE html>

<html lang="en">


<head>

    <meta name="viewport" content="width=device-width, inital-scale=1.0">
    <meta charset="utf-8">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="https://cdn.ckeditor.com/ckeditor5/23.1.0/classic/ckeditor.js"></script>
    <link rel="stylesheet" type="text/css" href="http://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <script src="https://kit.fontawesome.com/4733528720.js" crossorigin="anonymous"></script>

    <title>Панель керування</title>

</head>

<body>

<?php

class EmployeeController
{
    public $factory;
    public $employeeModel;
    public $employeeView;
    public $subjectsModel;
    public $scheduledSubjects;

    public function __construct()
    {
        $this->factory = new EmployeeControllerFactory();
        $this->employeeView = $this->factory->createObject("employeeView");
        $this->employeeModel = $this->factory->createObject("employeeModel");
        $this->subjectsModel = $this->factory->createObject("subjectModel");
        $this->scheduledSubjects = $this->factory->createObject("scheduledSubjects");

    }

    public function showAllTypesView()
    {
        $this->employeeView->showAllTypes($this->employeeModel->getNumberOfUsersInRole(3), $this->employeeModel->getNumberOfUsersInRole(2));
    }


    public function studentsView()
    {
        $students = $this->employeeModel->selectAllStudents();
        $this->employeeView->displayAll($students, "учнів");
    }

    public function teachersView()
    {
        $teachers = $this->employeeModel->selectAllTeachers();
        $this->employeeView->displayAll($teachers, "вчителів");
    }


    public function nameSearchInputView()
    {
        $employees = $this->employeeModel->selectAllEmployees();
        $encID = $_REQUEST['access'];
        $url = $employees[0]->link . "?access=" . $encID . "&" . $_REQUEST['selected'] . "name";
        $nameArray = explode(" ", $_POST['nameSearchInput']);
        $printedName = "";
        for ($i = 0; $i < count($nameArray); $i++) {
            if ($i > 0) {
                $printedName .= "_";
            }
            $printedName .= $nameArray[$i];
        }
        header('location:' . $url . '=' . $printedName);
    }

    public function searchWithNameStudentsView()
    {
        $students = $this->employeeModel->selectAllStudents();
        $nameArray = explode("_", $_REQUEST['studentname']);
        $this->employeeView->searchWithName($students, $nameArray);
    }

    public function searchWithNameTeachersView()
    {
        $teachers = $this->employeeModel->selectAllTeachers();
        $nameArray = explode("_", $_REQUEST['teachername']);
        $this->employeeView->searchWithName($teachers, $nameArray);
    }

    public function displaySpecificUserView()
    {
        $users = $this->employeeModel->selectAllUsers("where id=" . $_REQUEST['id'] . "");
        $userObj = $users[0];
        $subjects = $this->subjectsModel->selectUserSubjects($userObj->id);
        $classes = $this->subjectsModel->selectUserClasses($userObj->id);
        $this->employeeView->displaySpecificUser($userObj, $subjects, $classes);
    }

    public function deleteUserView()
    {
        $id = $_REQUEST['id'];
        $this->employeeModel->deleteUser($id);
    }

    public function reactivateUserView()
    {
        $id = $_REQUEST['id'];
        $this->employeeModel->reActivateUser($id);
    }

    public function editScheduledSubject($scheduledSubject, $all_subject_names, $all_classes, $all_cabinets, $all_teachers)
    {
        ob_start(); // start output buffering

        $this->employeeView->editScheduledSubjectPage($scheduledSubject, $all_subject_names, $all_classes, $all_cabinets, $all_teachers);
        if (isset($_POST['saveScheduledSubject'])) {
            $this->scheduledSubjects->upsertScheduledSubject(
                $_POST['subject_name_id'], $_POST['class_id'], $_POST['date'],
                $_POST['order'], $_POST['cabinet_id'], $_POST['teacher_id'], $scheduledSubject->id ?? null);
            header('Location: /modules/Employee/Controller/employeeController.php?access=SA==&page=SpecificSearch');
            exit();

        }
        ob_end_flush(); // end output buffering and send the output to the browser
    }

    public function editUser($userObj, $subjectsObj = array(), $classesArray = array())
    {
        $this->employeeView->editUserPage($userObj, $subjectsObj, $classesArray);
        if (isset($_POST['addSubject'])) {
            $this->employeeModel->updateUserProperties($_REQUEST);
            $this->employeeModel->insertSubjectsToTeacher($_REQUEST['id'], $_POST['checkbox_subjects'] ?? array());
            $this->employeeModel->insertClassesToUsers($_REQUEST['id'], $_POST['checkbox_classes'] ?? array());
            header("Location: /modules/Employee/Controller/employeeController.php?access=SA==&id=" . $_REQUEST['id']);

            exit;

        }
    }

    public function specificResearchView($classes)
    {
        $this->employeeView->specificSearchPage($classes);
    }

    public function specificSearchResultsView($results)
    {
        $this->employeeView->specificSearchResultsView($results);
    }

}

$employeeController = new EmployeeController();
$semesterModel = $factory->createObject("semesterModel");
$employeeModel = $factory->createObject("employeeModel");
$subjectsModel = $factory->createObject("subjectModel");
$subjectsNamesModel = $factory->createObject("subjectModel");
$customizedReports = $factory->createObject("customizedReports");
$classModel = $factory->createObject("classModel");
$cabinetModel = $factory->createObject("cabinetModel");
$scheduledSubjects = $factory->createObject("scheduledSubjects");

if (isset($_REQUEST['page'])) {
    if ($_REQUEST['page'] == "home") {
        $employeeController->showAllTypesView();
    }
    if ($_REQUEST['page'] == "SpecificSearch") {
        $employeeController->specificResearchView($classModel->fetchClasses());
        if (isset($_POST['class']) && isset($_POST['date'])) {
            $employeeController->specificSearchResultsView($scheduledSubjects->fetchScheduledSubjects($_POST['date'], $_POST['class']));
        }
    }
}

if (isset($_REQUEST['selected'])) {
    if ($_REQUEST['selected'] == "student") {
        $employeeController->studentsView();
    } else if ($_REQUEST['selected'] == "teacher") {
        $employeeController->teachersView();
    }
}

if (isset($_POST['nameSearchInput'])) {
    $employeeController->nameSearchInputView();
}

if (isset($_REQUEST['studentname'])) {
    $employeeController->searchWithNameStudentsView();
} else if (isset($_REQUEST['teachername'])) {
    $employeeController->searchWithNameTeachersView();
}

if (isset($_REQUEST['id']) && !isset($_REQUEST['action'])) {
    $employeeController->displaySpecificUserView();
}

if (isset($_POST['delete'])) {
    /*$query1 = "DELETE FROM users  WHERE id = $id";
    $query2 = "DELETE FROM address  WHERE user_id = $id";
    $query3 = "DELETE FROM identity_images  WHERE user_id = $id";
    $query4 = "DELETE FROM phone_numbers  WHERE user_id = $id";
    $query5 = "DELETE FROM students_data  WHERE user_id = $id";
    if($mydb->query($query1) !== true || $mydb->query($query2) !== true || $mydb->query($query3) !== true || $mydb->query($query4) !== true || $mydb->query($query5) !== true)
    {
        die("Something went wrong.");
    }
    else
    {
        $encID = $_REQUEST['access'];
        $url = $employees[0]->link."?access=".$encID."&page=home";
        header("location:$url");
    }*/
    $employeeController->deleteUserView();
}

if (isset($_POST['reActivate'])) {
    $employeeController->reactivateUserView();
}

if (isset($_REQUEST['action'])) {
    $min = 100000; // The minimum value
    $max = 999999; // The maximum value
    $random_int = mt_rand($min, $max);

    $_REQUEST['id'] = $_REQUEST['id'] ?? $random_int;
    $users = $employeeModel->selectAllUsers("where id = " . $_REQUEST['id'] . "");


    # if at least one user is found assign it to $userObj else assing to it a new user object with default values
    $userObj = count($users) > 0 ? $users[0] : null;
    # if user userObj has no id assign new one to it


    if ($_REQUEST['action'] == "editUser" || $_REQUEST['action'] == "addUser") {
        $userObj = $userObj ?? $factory->createObject("user");
        $userObj->id = $userObj->id ?? $_REQUEST['id'];
        $userObj->user_type = $userObj->user_type ?? (int)$_REQUEST['userType'];

        $employeeController->editUser($userObj, $subjectsModel->selectUserSubjects($userObj->id), $subjectsModel->selectUserClasses($userObj->id));
    }

    if ($_REQUEST['action'] == "editScheduledSubject") {
//        if subject_id is set then fetch the subject with that id else create new subject
        if (isset($_REQUEST['subject_id']))
            $currentSubject = $scheduledSubjects->fetchScheduledSubjects(null, null, $_REQUEST['subject_id'])[0];
        else
            $currentSubject = $factory->createObject("scheduledSubject");
        $employeeController->editScheduledSubject(
            $currentSubject,
            $subjectsModel->selectAllSubjects(),
            $classModel->fetchClasses(),
            $cabinetModel->fetchCabinets(),
            $employeeModel->selectAllTeachers()
        );
    }
}

?>


</body>
</html>