<?php

    session_start();
    if(!isset($_SESSION['userId'])){
        header('Location: https://attendance-system-js5898.c9users.io/login.php');
        exit();
    }
    
    $welcomeMessage = "<h4>Welcome, <i>".$_SESSION['firstname']." ".$_SESSION['lastname']."</i></h4>";
    
    // Set parameters for the DB
    $servername = "localhost";
    $DBusername = "js5898";
    $DBpassword = "";
    $dbname = "attendanceDB";
    
    // Get session variables
    $username = $_SESSION['userId'];
    $userId = $_SESSION['userIdCode'];
    $professorId = $userId;
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $DBusername, $DBpassword, $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
            
    
    // Fill both dropdown menus
    $getStudents = getUsers($professorId, $dbc);
    $dropdownContent = fillStudentsDropdownMenu($getStudents);
    $getCourses = getCourses($professorId, $dbc);
    $dropdownCourses = fillCoursesDropdownMenu($getCourses);
    
    // Sets top user report message
    $studReportTop = "";

    // Event listener for the UPDATE button
    if(isset($_POST['selectLessons'])){
        
        $selectedStudId = $_POST['studentSelect'];              // Get ID from selected student from dropdown list
        $courseId = $_POST['courseSelect'];                     // Get ID from selected course from dropdown list
        
        if($selectedStudId == "" || $courseId == ""){
            $studReportTop = "<div class='alert alert-warning'>
                                 <b>Warning:</b> Please select student and course!
                               </div>";
        } else {
            $selectedTab = getStudent($selectedStudId, $dbc);       // Returns array $selectedTab('firstname', 'lastname', 'user_id')
            $studReportTop = "Lessons student <b><i>".$selectedTab['firstname']." ".$selectedTab['lastname']."</b></i> (ID = ".$selectedTab['user_id'].") attended";
            
            // Does this student go to this course?
            $goesTo = goesTo($selectedStudId, $courseId, $dbc);
            
            if($goesTo != 0){           // Student goes to this course
            
                $tableContent = createTableContent($selectedStudId, $courseId, $dbc);
                $generatedTable = fillTable($tableContent);             // HTML content in $generatedTable - this var is echo-ed!
                
                // Counts lessons and attendance for the calculation
                $countLessons = countLessons($courseId, $dbc);
                $countLessonsAttended = countLessonsAttended($courseId, $selectedStudId, $dbc);
                
                // Avoid division by zero!
                if($countLessons > 0){
                    $percent = "<div class='alert alert-info'>
                                    Student attended <b>".$countLessonsAttended."</b> out of <b>".$countLessons."</b> lessons <i><b>(".round(($countLessonsAttended/$countLessons)*100)."% attendance)</b></i>
                                </div>";   
                } else {
                    $percent = "<div class='alert alert-info'>
                                    Student attended <b>".$countLessonsAttended."</b> out of <b>".$countLessons."</b> lessons <i>(0% attendance)</i>
                                </div>";
                }
            } else {    // Student doesn't visit this course --> goesTo == 0
                $studReportTop = "<div class='alert alert-warning'>
                                    Student <b><i>".$selectedTab['firstname']." ".$selectedTab['lastname']."</b></i> (ID = ".$selectedTab['user_id'].") doesn't visit this course!
                                  </div>";
            }
        }
    }
    
    /** 
     * FUNCTIONS BELOW 
    */
    function goesTo($studentId, $courseId, $dbc){
        
        consoleLog("StudentId: ".$studentId." | courseId: ".$courseId);
        
        $query = "SELECT COUNT(course_id) FROM goesTo
                    WHERE user_id = '$studentId'
                    AND course_id = '$courseId'";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return $row["COUNT(course_id)"];             
    }
    
    
    
    function countLessonsAttended($courseId, $selectedStudId, $dbc){
        
        $query = "SELECT COUNT(l.lesson_id) FROM lessons l, courses c, attended a
                    WHERE c.course_id = l.course_id
                    AND c.course_id = '$courseId'
                    AND l.lesson_id = a.lesson_id
                    AND a.user_id = '$selectedStudId'";
                    
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return $row["COUNT(l.lesson_id)"];        
    }
    
    function countLessons($courseId, $dbc){
        
        
        consoleLog("CourseId: ".$courseId);
        $query = "SELECT COUNT(l.lesson_id) FROM lessons l, courses c
                    WHERE c.course_id = l.course_id
                    AND c.course_id = '$courseId'";
                    
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return $row["COUNT(l.lesson_id)"];
    }
    
    function getCourses($professorId, $dbc){
        
        consoleLog("ProfessorID = ".$professorId);
        $query = "SELECT course_id, course_name FROM courses 
                    WHERE prof_id = '$professorId'
                    ORDER BY course_id";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
        
    }

    function getUsers($professorId, $dbc){
        
        $query = "SELECT DISTINCT u.user_id, u.firstname, u.lastname FROM users u, role r, goesTo g, courses c
                    WHERE u.user_id = r.user_id 
                    AND r.role = 'student'
                    AND u.user_id = g.user_id
                    AND g.course_id = c.course_id
                    AND c.prof_id = '$professorId'";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
    }
    
    function getStudent($studentId, $dbc){
        
        $query = "SELECT user_id, firstname, lastname
                    FROM users 
                    WHERE user_id = '$studentId'";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        $out['user_id'] = $row["user_id"];
        $out['firstname'] = $row["firstname"];
        $out['lastname'] = $row["lastname"];
        
        return $out;
    }
    
    function createTableContent($studentId, $courseId, $dbc){
        $query = "SELECT l.lesson_timestamp, l.lesson_description, l.lesson_title, l.unique_code, l.lesson_id, l.unique_code
                    FROM lessons l, users u, attended a
                    WHERE u.user_id = '$studentId'
                    AND a.user_id = '$studentId'
                    AND a.lesson_id = l.lesson_id
                    AND l.course_id = '$courseId'
                    ORDER BY lesson_id";

        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
    }
    
    function createTableHeader(){
        
        $tableLine =    '<table class="table table-bordered table-hover table-sm table-striped">';
        $tableLine .=   "<thead>
                            <tr>
                                <th>#</th>
                                <th>Timestamp</th>
                                <th>Lesson ID
                                <th>Title</th>
                                <th>Description</th>
                                <th>Unique code</th>
                            </tr>
                        </thead>";
        return $tableLine;
    }
    
    function fillTable($result){
        
        $tableLine = createTableHeader();
        $tableLine .= "<tbody>";
        $rowNum = 1;
        
        while($row = mysqli_fetch_array($result)){
             $tableLine .=  '<tr>
                                <th scope="row"> '.$rowNum.'</th>
                                <td> '.$row["lesson_timestamp"].'</td>
                                <td> '.$row["lesson_id"].'</td>
                                <td> '.$row["lesson_title"].'</td>
                                <td> '.$row["lesson_description"].'</td>
                                <td> '.$row["unique_code"].'</td>
                            </tr>';
            $rowNum++;
        }
        $tableLine .= "</tbody></table>";
        
        return $tableLine;
    }
    
    function fillStudentsDropdownMenu($result){
        
        $dropdownContent = '<option value="" disabled selected><i>No student selected</i></option>';
        
        while($row = mysqli_fetch_array($result)){
            $dropdownContent .= '<option value="'.$row["user_id"].'">'.$row["firstname"].' '.$row["lastname"].'</option>';
            
        }

        return $dropdownContent;
    }
    
    function fillCoursesDropdownMenu($result){
        
        $dropdownContent = '<option value="" disabled selected><i>No course selected</i></option>';
        
        while($row = mysqli_fetch_array($result)){
            $dropdownContent .= '<option value="'.$row["course_id"].'">'.$row["course_name"].'</option>';
            
        }
        
        return $dropdownContent;
        
    }
    
    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
    
    
?> 

<html lang="en">
        <head>
            <title>Student attendance</title>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
            <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
            <link rel="apple-touch-icon" sizes="120x120" href="Images/apple-touch-icon.png">
            <link rel="icon" type="image/png" sizes="32x32" href="Images/favicon-32x32.png">
            <link rel="icon" type="image/png" sizes="16x16" href="Images/favicon-16x16.png">
            <link rel="manifest" href="/manifest.json">
            <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
            <meta name="theme-color" content="#ffffff">
        </head>
        <body>
            <style>
                .top-buffer {
                    margin-top:10px; 
                    margin-left: 10px;
                    margin-right: 10px;
                }
                .top-buffer2 {
                    margin-top:10px;
                    margin-left: 0px;
                    margin-right: 0px;
                }
                
                .input-group-addon {
                    min-width:150px;
                    text-align:left;
                }
            </style>
            <img src="Images/AttendanceSys.PNG" alt="Logo" style="width:100%;">
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="index.php">AttendanceSystem</a>
                    </div>
                    <ul class="nav navbar-nav">
                        <li><a href="index.php">Home</a></li>
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="lesson.php">Lessons</a></li>
                        <li class="active"><a href="students.php">Student Attendance</a></li>
                        <li><a href="manualEntry.php">Manual entry</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li><a href="logout.php"><span class="glyphicon glyphicon-log-out"></span> Logout</a></li>
                    </ul>
              </div>
            </nav>
            
            <div class="container">
                <div class = "row">
                    <div class = "col">
                        <?php 
                            echo $welcomeMessage
                        ?>
                    </div>
                    <br>
                </div>
            </div> 
            
            <div class="container">
                <div class="row">
                    <div class="col-sm-12">
                        <div class = "panel panel-primary">
                            <div class="panel-heading"><h4>Check student attendance</h4></div>
                            <div class = "panel-body">
                                <label for="select_1">Select a student</label>
                                    <form action="#" method="post">
                                        <select class="form-control" id="select_1" name="studentSelect">
                                            <?php
                                                echo $dropdownContent
                                            ?>
                                        </select>
                                        
                                        <script>
                                            $(function() {
                                                if (localStorage.getItem('select_1')) {
                                                    $("#select_1 option").eq(localStorage.getItem('select_1')).prop('selected', true);
                                                }
                                            
                                                $("#select_1").on('change', function() {
                                                    localStorage.setItem('select_1', $('option:selected', this).index());
                                                });
                                            });
                                        </script>
                                        
                                        <div class="row top-buffer2">
                                            <label for="select_2">Select a course</label>
                                            <select class="form-control" id="select_2" name="courseSelect">
                                                <?php
                                                    echo $dropdownCourses
                                                ?>
                                            </select>
                                            
                                            <script>
                                                $(function() {
                                                    if (localStorage.getItem('select_2')) {
                                                        $("#select_2 option").eq(localStorage.getItem('select_2')).prop('selected', true);
                                                    }
                                                
                                                    $("#select_2").on('change', function() {
                                                        localStorage.setItem('select_2', $('option:selected', this).index());
                                                    });
                                                });
                                            </script>
                                        </div>
                                        <div class="row top-buffer2">
                                            <input class="btn btn-primary" type="submit" name="selectLessons" value="Update" />
                                        </div>
                                    </form>
                                    <h4><?php echo $studReportTop ?></h4>
                                    <h3><?php echo $percent ?></h3>
                                    <?php
                                        echo $generatedTable
                                    ?>
                                </div>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>
        </body>
    </head>
</html>