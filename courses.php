<?php
    session_start();
    if(!isset($_SESSION['userId'])){
        header('Location: https://attendance-system-js5898.c9users.io/login.php');
        exit();
    }
    
    // Set parameters for the DB
    $servername = "localhost";
    $DBusername = "js5898";
    $DBpassword = "";
    $dbname = "attendanceDB";
    
    $username = $_SESSION['userId'];
    $userId = $_SESSION['userIdCode'];
    $welcomeMessage = "<h4>Welcome, <i>".$_SESSION['firstname']." ".$_SESSION['lastname']."</i></h4>";
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $DBusername, $DBpassword, $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
            
    $result = getCourses($userId, $dbc);
    $tableLine = fillTable($result);
    
    // Fill the dropdown menu
    $result = getCourses($userId, $dbc);
    $dropdownContent = fillDropdownMenu($result);
    $userMessageCourse = "";
    
    // Students attending course part --> Select course, draw table
    if(isset($_POST['courseSelect'])){
        
        $courseId = $_POST['courseDropdown'];
        $userMessageCourse = getCourseName($courseId, $dbc);
        $studentTab = getStudentsTab($courseId, $dbc);
    }
    
    // Add new course
    $insertCourseUserMessage = "";
    if(isset($_POST['addCourseButton'])){
        $courseName=$_POST['courseName'];
        $requiredAttendance=$_POST['requiredAttendance'];
        $exitCode = insertCourse($courseName, $requiredAttendance, $userId, $dbc);

        if($exitCode == 0){
            $insertCourseUserMessage = "<div class='alert alert-success alert-dismissable'>
                                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                            <b>Success:</b> Course successfully added!
                                        </div>";            
        } else if($exitCode == 1) {
            $insertCourseUserMessage = "<div class='alert alert-warning alert-dismissable'>
                                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                            <b>Warning:</b> Please enter course name!
                                        </div>";
        } else {
            $insertCourseUserMessage = "<div class='alert alert-warning alert-dismissable'>
                                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                            <b>Warning:</b> Required attendance should be a number (0-100)!
                                        </div>";
        }
    }
    
    
    /** 
     * FUNCTIONS BELOW 
    */
    
    
    function getCourseName($courseId, $dbc){
        $query = "SELECT c.course_name FROM courses c
	                WHERE c.course_id = '$courseId'";
	                
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return "Students attending <i><b>".$row['course_name']."</i></b>";
    }
    
    function getStudentsTab($courseId, $dbc){
        
        $query = "SELECT u.user_id, u.firstname, u.lastname, u.email FROM users u, goesTo g
                    WHERE u.user_id = g.user_id
                    AND g.course_id = '$courseId'
                    ORDER BY user_id ASC";
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        
        $tableLine = fillTable2($result);
        
        return $tableLine;
        
    }
    
    function createTableHeader2(){
        
        $tableLine = '<table class="table table-bordered table-hover table-sm table-striped">';
        
        $tableLine .=   "<thead>
                            <tr>
                                <th>Student ID</th>
                                <th>First name</th>
                                <th>Last name</th>
                                <th>E-mail</th>
                            </tr>
                        </thead>";
        return $tableLine;
    }
    
    function fillTable2($result){
        
        $tableLine = createTableHeader2();
        
        $tableLine .= "<tbody>";
        
        while($row = mysqli_fetch_array($result)){
             $tableLine .=  '<tr>
                            <th scope="row"> '.$row["user_id"].'</th>
                            <td> '.$row["firstname"].'</td>
                            <td> '.$row["lastname"].'</td>
                            <td> '.$row["email"].'</td>
                            </tr>';
        }
        $tableLine .= "</tbody></table>";
        
        return $tableLine;
    }
    
    function fillDropdownMenu($result){
        
        $dropdownContent = '<option value="" disabled selected><i>No course selected</i></option>';
        
        while($row = mysqli_fetch_array($result)){
            $dropdownContent .= '<option value="'.$row["course_id"].'">'.$row["course_name"].'</option>';
            
        }
        
        return $dropdownContent;
    }


    function insertCourse($courseName, $requiredAttendance, $profId, $dbc){
        
        $exit = 0;
        
        $required = array("courseName", "requiredAttendance");
        
        if($courseName == "") $exit = 1;
        if($requiredAttendance == "" || $requiredAttendance < 0 || $requiredAttendance > 100) $exit = 2;
        /*
        foreach ($required as $field) {                                 
            if(empty($_POST[$field]) === TRUE){                         // Error - all fields are not filled, do not query the SQL
                $error = true;
            }
        }
        */
        
        if($exit == 0){                                            // The data are OK, and can be sent to the DB
            $sql = "INSERT INTO courses (course_id, course_name, prof_id, required_attendance)
                    VALUES (NULL, '$courseName', '$profId', '$requiredAttendance')";
            mysqli_query($dbc, $sql);
            mysqli_close($dbc);   
            header('Location: https://attendance-system-js5898.c9users.io/courses.php');
        } 
        
        return $exit;
    }

    function getCourses($profId, $dbc){
        
        $query = "SELECT c.course_id, c.course_name, c.required_attendance FROM courses c
                	WHERE c.prof_id = '$profId'
                	ORDER BY c.course_id ASC";
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
    }
    
    
    function createTableHeader(){
        
        $tableLine = '<table class="table table-bordered table-hover table-sm table-striped">';
        
        $tableLine .=   "<thead>
                            <tr>
                                <th>Course ID</th>
                                <th>Course Name</th>
                                <th>Required attendance</th>
                            </tr>
                        </thead>";
        return $tableLine;
    }
    
    function fillTable($result){
        
        $tableLine = createTableHeader();
        
        $tableLine .= "<tbody>";
        
        while($row = mysqli_fetch_array($result)){
            
            consoleLog("Count: ".$row2["COUNT(g.user_id)"]);
             $tableLine .=  '<tr>
                            <th scope="row"> '.$row["course_id"].'</th>
                            <td>'.$row["course_name"].'</td>
                             <td>'.$row["required_attendance"].'</td>
                            </tr>';
        }
        $tableLine .= "</tbody></table>";
        
        return $tableLine;
    }

    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
?> 

<html lang="en">
        <head>
            <title>Courses</title>
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
                }
                .top-buffer2 {
                    margin-top:10px;
                    margin-left: 0px;
                    margin-right: 0px;
                }
                
                .input-group-addon {
                    min-width:100px;
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
                        <li class="active"><a href="courses.php">Courses</a></li>
                        <li><a href="lesson.php">Lessons</a></li>
                        <li><a href="students.php">Student Attendance</a></li>
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
                </div>
            </div> 
            <br>
            
            <div class="container">
                <div class="row">
                    <div class="col-sm-6">
                        <div class = "panel panel-primary">
                        <div class="panel-heading"><h4>Courses overview</h4></div>
                            <div class = "panel-body">
                                <?php
                                    echo $tableLine
                                ?>
                            </div>
                        </div>
                        
                        <div class = "panel panel-primary">
                            <div class="panel-heading"><h4>Students attending course</h4></div>
                            <div class = "panel-body">
                                <label for="select_1">Select a course</label>
                                    <form action="#" method="post">
                                        <select class="form-control" data-style="btn-primary" id="select_1" name="courseDropdown">
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
                                        
                                        <br>
                                        <input class="btn btn-primary" type="submit" name="courseSelect" value="Update" />
                                    </form>
                                    <h4><?php echo $userMessageCourse ?></h4>
                                    <?php echo $studentTab ?>
                                </div>
                            </div>
                        </div>
                    <div class="col-sm-6">
                        <div class = "panel panel-default">
                            <div class="panel-heading"><h4>Add a course</h4></div>
                            <div class = "panel-body">
                                <form method="POST">
                                    <div class="row top-buffer"></div>
                                    <div class="input-group">
                                        <span class="input-group-addon">Course name:</span>
                                        <input id="courseName" type="text" class="form-control" name="courseName" placeholder="Enter course name" >
                                    </div>
                                    <div class="row top-buffer"></div>
                                    <div class="input-group">
                                        <span class="input-group-addon">Required attendance:</span>
                                        <input id="requiredAttendance" type="number" min="0" max="100" class="form-control" name="requiredAttendance" placeholder="Reqired attendance (0-100)" >
                                        <span class="input-group-addon">%</span>
                                    </div>
                                    
                                    <div>
                                        <?php echo $insertCourseUserMessage ?>
                                    </div>
                                    <div class="row top-buffer2">
                                       <input class="btn btn-primary" type="submit" name="addCourseButton" value="Add course"/>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </head>
</html>