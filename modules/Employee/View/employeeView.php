<?php
require_once '../Model/employeeModel.php';
include_once('../../../constants.php');


require_once 'employeeControllerFactory.php';
$factory = new EmployeeControllerFactory();
$employeeModelTemp = $factory->createObject("employeeModel");
$employees = $employeeModelTemp->selectAllEmployees();

for($i = 0; $i < count($employees); $i++)
{
    if($_REQUEST['access'] == $employeeModelTemp->pwdEncryption($employees[$i]->password))
    {
        break;
    }
    die("No Permission");
}
class EmployeeView
{
    public $employeeModel;
    public $employees;
    public $url;
    public $encID;
    public $imageURL;

    function __construct()
    {
        $this->employeeModel = new EmployeeModel();
        $this->employees = $this->employeeModel->selectAllEmployees();
        $this->encID = $_REQUEST['access'];
        $this->url = $this->employees[0]->link . "?access=" . $this->encID;
        $this->imageURL = "../../../";
    }

    public function showAllTypes($noOfStudents, $noOfTeachers)
    {
        echo "<h1 style='text-align:left; margin-left:40px; margin-top:40px; margin-bottom: 100px;'>Панель керування</h1>";
        ?>

        <div style="width:100%;">
            <div class="d-flex justify-content-center" style="margin: 0 auto;">
                <div class="col-sm-3">
                    <div class="card text-white bg-success mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $noOfStudents ?></h3>
                            <i class="ion-ios-people" style="font-size:30px;"></i>
                            <p class="card-text">Загльна кількість учнів</p>
                        </div>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="card text-white bg-danger mb-3">
                        <div class="card-body">
                            <h3 class="card-title"><?php echo $noOfTeachers ?></h3>
                            <i class="ion-person-stalker" style="font-size:30px;"></i>
                            <p class="card-text">Загальна кількість вчителів</p>
                        </div>
                    </div>
                </div>

            </div>

            <div style="width: 75%; margin: 0 auto;">
                <div class="d-flex justify-content-center" style="margin-top: 50px;">
                    <div class="col-sm-2" style="text-align:center;">
                        <a href="<?php echo $this->url ?>&selected=student">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                <i class="ion-ios-people" style="font-size:50px;"></i>
                                <p class="card-text">Учні</p>
                            </button>
                        </a>
                    </div>


                    <div class="col-sm-2" style="text-align:center;">
                        <a href="<?php echo $this->url ?>&selected=teacher">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                <i class="ion-person-stalker" style="font-size:50px;"></i>
                                <p class="card-text">Вчителя</p>
                            </button>
                        </a>
                    </div>
                    <div class="col-sm-2" style="text-align:center;">
                        <a href="<?php echo $this->url ?>&page=SpecificSearch">
                            <button type="button" class="btn btn-outline-dark" style="width: 180px; padding: 15px 0;">
                                <i class="ion-ios-search-strong" style="font-size:50px;"></i>
                                <p class="card-text">Пошук по розкладу</p>
                            </button>
                        </a>
                    </div>

                </div>
            </div>

        </div>


        <?php


    }


    public function displayAll($userType, $headerTitle)
    {
        echo "<h1 style='text-align:center;  margin-top:35px; '>Список $headerTitle</h1>";
        ?>
        <form action=" " method="POST" style="margin-left: 10px; margin-bottom:0px;">
            <div class="form-row">
                <div class="col-md-3">
                    <input type="text" class="form-control" placeholder="Search by name" name="nameSearchInput" required>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-outline-dark">Search</button>
                </div>
            </div>
        </form>
        <?php


        echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
        for ($i = 0; $i < count($userType); $i++) {
            $fullName = $userType[$i]->first_name . " " . $userType[$i]->second_name;
            $Id = $userType[$i]->id;

                if ($userType[$i]->isDeleted == 0)
                    echo "<a  href=$this->url&id=" . $userType[$i]->id . " style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>Імʼя: $fullName <br> ID: $Id</strong></button></a>";
                else
                    echo "<a href=$this->url&id=" . $userType[$i]->id . " style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-danger list-group-item list-group-item-action'><strong>Імʼя: $fullName [DELETED]<br> ID: $Id</strong></button></a>";
        }
        echo "</div>";

        # button to add new user

        echo "<a href=$this->url&page=home><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'><< До головного меню</button></a>";
//        echo "<a href=$this->url&action=addUser&userType=" . TEACHER. "><button type='button' style='width:30%; padding:6px; font-size:18px; margin-bottom:10px;' class='btn btn-outline-dark'>Додати</button></a>";
    }

    public function displaySpecificUser($userType, $subjects = array(), $classes = array())
    {
        echo "<h1 style='text-align:center;  margin-top:25px; margin-bottom:35px; '>$userType->first_name $userType->second_name </h1>";
        ?>
        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">ID</th>
                <th scope="col">Повне Ім'я</th>
                <th scope="col">Email</th>
                <th scope="col">Дата додавання</th>
                <?php if ($userType->user_type == TEACHER): ?>
                    <th scope="col">Предмети які викладає</th>
                    <th scope="col">Класи в яких викладає</th>
                <?php elseif ($userType->user_type == STUDENT): ?>
                    <th scope="col">Предмети які вивчає</th>
                    <th scope="col">Клас в якому навчається</th>
                <?php endif ?>

            </tr>
            </thead>
            <tbody>
            <tr>
                <th scope="row"><?php echo $userType->id ?></th>
                <td><?php echo $userType->first_name . " " . $userType->second_name  ?></td>
                <td><?php echo $userType->email ?></td>
                <td><?php echo $userType->date_created ?></td>
                <?php if ($userType->user_type == TEACHER || $userType->user_type == STUDENT): ?>
                    <td>
                        <?php
                        for ($i = 0; $i < count($subjects); $i++) {
                            if ($subjects[$i]["checked"] != "") {
                                echo $subjects[$i]["subject_name"] . "<br>";

                            }

                        }
                        ?>
                    </td>
                    <td>
                        <?php
                        for ($i = 0; $i < count($classes); $i++) {
                            if ($classes[$i]["checked"] != "") {
                                echo $classes[$i]["class_name"] . "<br>";

                            }

                        }
                        ?>
                    </td>
                <?php endif ?>

            </tr>

            </tbody>
        </table>

        <form action="" method="POST" style="text-align: center;">
            <?php if ($userType->isDeleted == 1): ?>
                <button type="submit" class="btn btn-success" name="reActivate">Відновити</button
            <?php endif ?>
            <a href="<?php echo $this->url ?>&access=SA==&selected=<?php echo USER_TYPE_MAPPING[$userType->user_type]?>">
                <button type="button" class="btn btn-outline-dark" name="subjectRegister"><< Назад до списку</button>
            </a>
            <a href="<?php echo $this->url ?>&id=<?php echo $userType->id ?>&action=editUser">
                <button type="button" class="btn btn-outline-dark" name="subjectRegister">Редагувати інформацію</button>
            </a>
            <?php if ($userType->isDeleted == 0): ?>
                <button type="submit" class="btn btn-danger" name="delete">Видалити</button>
            <?php endif ?>

        </form>
        <?php
    }

    public function searchWithName($userType, $nameArray)
    {
        echo "<h1 style='text-align:center;  margin-top:35px; '>Search Results</h1>";
        echo "<div style='width:100%; margin-top:40px;' class='list-group'>";
        for ($i = 0; $i < count($userType); $i++) {
            $fullName = $userType[$i]->first_name . " " . $userType[$i]->second_name;
            $firstName = $userType[$i]->first_name;
            $secondName = $userType[$i]->second_name;
            $id = $userType[$i]->id;
            for ($k = 0; $k < count($nameArray); $k++) {
                if (strtolower($nameArray[$k]) == strtolower($firstName) || strtolower($nameArray[$k]) == strtolower($secondName)) {
                    echo "<a target=”_blank” href=$this->url&id=" . $userType[$i]->id . " style='text-decoration: none; margin-bottom:-1px;'><button type='button' class='text-success list-group-item list-group-item-action'><strong>Імʼя: $fullName <br> ID: $id</strong></button></a>";
                    break;
                }
            }


        }
        echo "</div>";
    }


//    funtion to edit scheduled subjects

    public function editScheduledSubjectPage($current_subject, $all_subject_names, $all_classes, $all_cabinets, $all_teachers)
    {
        echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Предмет</h1>";
        ?>
        <form action=""  style="width: 35%; margin: 50px auto;" class="form-group" method="POST" enctype="multipart/form-data">

                <div class="form-group">
                <label for='select_subjects'>Обрати предмет:</label><br>
                <select id='select_subjects' name="subject_name_id" class="form-control">
                    <?php if ($current_subject->subject_name != null): ?>
                        <option value='<?php echo $current_subject->subject_id; ?>' selected><?php echo $current_subject->subject_name; ?></option>
                    <?php else: ?>
                        <option value='' disabled selected>Предмети:</option>
                    <?php endif; ?>
                    <?php foreach ($all_subject_names as $value): ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->Name; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
                <div class="form-group">
                <label for='select_classes'>Обрати класс:</label><br>
                <select id='select_classes'  name="class_id" class="form-control">
                    <?php if ($current_subject->class_name != null): ?>
                        <option value='<?php echo $current_subject->class_id; ?>' selected><?php echo $current_subject->class_name; ?></option>
                    <?php else: ?>
                        <option value='' disabled selected>Класи:</option>
                    <?php endif; ?>
                    <?php foreach ($all_classes as $value): ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->name; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
                <div class="form-group">
                <label for='select_cabinets'>Обрати кабінет:</label><br>
                <select id='select_cabinets' name="cabinet_id" class="form-control">
                    <?php if ($current_subject->cabinet_number != null): ?>
                        <option value='<?php echo $current_subject->cabinet_id; ?>' selected><?php echo $current_subject->cabinet_number; ?></option>
                    <?php else: ?>
                        <option value='' disabled selected>Кабінети:</option>
                    <?php endif; ?>
                    <?php foreach ($all_cabinets as $value): ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->number; ?></option>
                    <?php endforeach; ?>
                </select>
                </div>
                <div class="form-group">
                <label for='select_teachers'>Обрати вчителя:</label><br>
                <select id='select_teachers' name="teacher_id" class="form-control">
                    <?php if ($current_subject->teacher_name != null): ?>
                        <option value='<?php echo $current_subject->teacher_id; ?>' selected><?php echo $current_subject->teacher_name; ?></option>
                    <?php else: ?>
                        <option value='' disabled selected>Вчителі:</option>
                    <?php endif; ?>
                    <?php foreach ($all_teachers as $value): ?>
                        <option value="<?php echo $value->id; ?>"><?php echo $value->first_name . " " . $value->second_name; ?></option>
                    <?php endforeach; ?>
                <input type="hidden" name="selectedSubject" id="selectedSubject" value=""/>
            </div>
            <div class="form-group">
                <label for="date">Дата</label>
<!--                Input to specify date. If current subject already has date use it as preselected-->
                <input type="date" class="form-control" name="date" id="date" value="<?php echo $current_subject->subject_date; ?>">
            </div>
            <div class="form-group">
                <label for="order">Порядок</label>
<!--                Select number starting from 1 to 8. If order is present in current subject use it as selected.-->
                <select class="form-control" name="order" id="order">
                    <?php for ($i = 1; $i <= 8; $i++): ?>
                        <?php if ($current_subject->order == $i): ?>
                            <option value="<?php echo $i; ?>" selected><?php echo $i; ?></option>
                        <?php else: ?>
                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                        <?php endif; ?>
                    <?php endfor; ?>
                </select>
            </div>


            <button type="submit" name="saveScheduledSubject" class="btn btn-outline-dark" style="width: 100%;">Зберегти</button>
        </form>
        <?php

    }


    public function editUserPage($userType, $subjectsArray, $classesArray)
    {
        if ($userType->user_type == STUDENT)
            echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Інформація про учня $userType->first_name</h1>";
        else if ($userType->user_type == TEACHER)
            echo "<h1 style='text-align:center;  margin-top:40px; margin-bottom: 50px; '>Інформація про вчителя $userType->first_name</h1>";


        ?>
        <form action="" style="width: 35%; margin: 50px auto;" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="firstName">Імʼя</label>
                <input type="text" class="form-control" id="firstName" name="firstName"
                       value="<?php echo $userType->first_name ?>" required>
                <label for="secondName">Фамілія</label>
                <input type="text" class="form-control" id="secondName" name="secondName"
                       value="<?php echo $userType->second_name ?>" required>
                <label for="email">Емейл</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo $userType->email ?>"
                       required>

                <?php
                if ($userType->user_type == TEACHER) {
                    echo "<label for='checkbox_subjects'>Предмети які викладає:</label><br>";

                    foreach ($subjectsArray as $value) {
                        $id = $value['id'];
                        $subject_name = $value['subject_name'];
                        $checked = $value['checked'] ? "checked" : "";

                        echo "<input type='checkbox' name='checkbox_subjects[]' id='checkbox$id' value='$id' $checked>";
                        echo "<label for='checkbox$id'>$subject_name</label><br>";
                    }
                    echo "<label for='checkbox_classes'>Класи в яких викладає:</label><br>";

                    foreach ($classesArray as $value) {
                        $id = $value['id'];
                        $class_name = $value['class_name'];
                        $checked = $value['checked'] ? "checked" : "";

                        echo "<input type='checkbox' name='checkbox_classes[]' id='checkbox_classes$id' value='$id' $checked>";
                        echo "<label for='checkbox_classes$id'>$class_name</label><br>";
                    }
                }
                ?>

                <input type="hidden" name="selectedSubject" id="selectedSubject" value=""/>
            </div>


            <button type="submit" name="addSubject" class="btn btn-outline-dark" style="width: 100%;">Зберегти</button>
        </form>
        <?php
    }



    public function specificSearchPage($classes)
    {
        echo "<h1 style='font-size:50px; text-align:center;  margin-top:35px;'>Пошук по розкладу</h1>";

        ?>
        <form action="" style="width: 35%; margin: 50px auto;" method="POST">
            <div class="form-group">
                <label for="classSelect">Оберіть клас:</label>
                <div class="input-group mb-4">
                    <select id="classSelect" class="form-control" name="class">
                        <option value="" disabled selected>Клас:</option>
                        <?php
                        for ($i = 0; $i < count($classes); $i++) {
                            echo "<option value='" . $classes[$i]->id . "'>" . $classes[$i]->name . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <label for="dateInput">Оберіть дату:</label>
                <div class="input-group mb-4">
                    <input id="dateInput" type="date" class="form-control" name="date" placeholder="Date">
                </div>


                <button type="submit" name="submitSearch" class="btn btn-outline-info" style="width: 100%;">Пошук</button>
                <br>
                <br>

                <a class="btn btn-outline-success" style="width: 100%;" href="<?php $this->url ?>?access=SA==&action=editScheduledSubject" >Запланувати предмет</a>
            </div>
        </form>
        <?php
    }


    public function specificSearchResultsView($results)
    {

?>

        <table class="table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Назва предмету</th>
                <th scope="col">Імʼя класу</th>
                <th scope="col">Дата предмету</th>
                <th scope="col">Порядок предмуту в дні</th>
                <th scope="col">Номер кабінету</th>
                <th scope="col">Імʼя вчителя</th>
                <th scope="col">Редагування</th>
            </tr>
            </thead>
            <tbody>
            <?php
             foreach ($results as $report) { {
//                list every object
                    echo '<tr>';
                    echo '<td>' . $report->subject_name . '</td>';
                    echo '<td>' . $report->class_name . '</td>';
                    echo '<td>' . $report->subject_date . '</td>';
                    echo '<td>' . $report->subject_order . '</td>';
                    echo '<td>' . $report->cabinet_number . '</td>';
                    echo '<td>' . $report->teacher_name . '</td>';
                 echo '<td>';
                 echo '<div style="display: flex; flex-direction: column;">';
                 echo "<a href=$this->url&action=editScheduledSubject&subject_id=" . $report->id . " class='btn btn-primary' >Редагувати предмет</a>";

//                 echo '<button class="btn btn-secondary" >Додати оцінки</button>';
                 echo '</div>';
                 echo '</td>';
                 echo '</tr>';
                }
            }
            ?>

            <tr>

            </tr>

            </tbody>
        </table>

    <?php
    }

}

?>
