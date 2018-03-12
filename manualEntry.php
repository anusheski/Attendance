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
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $DBusername, $DBpassword, $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
            
    
    // Fill both dropdown menus
    $professorId = $userId;
    
    $dropdownStudents = fillStudentsDropdownMenu($professorId, $dbc);
    $dropdownCourses = fillCoursesDropdownMenu($professorId, $dbc);
    
    // Sets top user report message
    $addToCourseResult = "";
    $addToLessonResult = "";

    // Adding student's attendance to lessons
    if(isset($_POST['addToLessonButton'])){
        
        $selectedStudId = $_POST['studentSelectLeft'];              // Get ID from selected student from dropdown list
        $selectedCourseId = $_POST['courseSelectLeft'];
        $uniqueCode = $_POST['uniqueCode'];
        $addToLessonResult = addToLesson($selectedStudId, $selectedCourseId, $uniqueCode, $dbc);
    }
    
    
    // Adding students to courses
    if(isset($_POST['addToCourseButton'])){
        
        $selectedStudId = $_POST['studentSelectRight'];             // Get ID from selected student from dropdown list
        $courseId = $_POST['courseSelectRight'];                    // Get ID from selected course from dropdown list
        $goesTo = goesTo($selectedStudId, $courseId, $dbc);         // Check, if this combination appears in goesTo table
        
        $selectedTab = getStudent($selectedStudId, $dbc);       // Returns array $selectedTab('firstname', 'lastname', 'user_id')
        
        if($goesTo == 0){       
        
            $exitCode = addToCourse($selectedStudId, $courseId, $dbc);
            
            if($exitCode == 0){
            $addToCourseResult = "<div class='alert alert-success alert-dismissable'>
                                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <b>Success:</b> Student <b><i>".$selectedTab['firstname']." ".$selectedTab['lastname']."</b></i> (ID = ".$selectedTab['user_id'].") added successfully!
                                </div>";
            } else {
                $addToCourseResult = "<div class='alert alert-warning alert-dismissable'>
                                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                        <b>Warning:</b> Student or course not selected!
                                    </div>";
            }
                            
        } else {
            $addToCourseResult = "<div class='alert alert-warning alert-dismissable'>
                                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                        <b>Warning:</b> Student <b><i>".$selectedTab['firstname']." ".$selectedTab['lastname']."</b></i> (ID = ".$selectedTab['user_id'].") is already applied to this course!
                                  </div>";
        }
    }
    
    /** 
     * FUNCTIONS BELOW 
    */
    
    
    function addToCourse($selectedStudId, $courseId, $dbc){
        
        if($selectedStudId != "" && $courseId != ""){
        
            $sql = "INSERT INTO goesTo (user_id, course_id)
                        VALUES ('$selectedStudId', '$courseId')";
            
            mysqli_query($dbc, $sql);
            mysqli_close($dbc);
            
            return 0; 
        } else {
            return 1;
        }
    }
    
    function attendedLesson($studentId, $uniqueCode, $dbc){
        
        $query = "SELECT COUNT(a.user_id) FROM attended a, lessons l
                    WHERE a.user_id = '$studentId'
                    AND a.lesson_id = l.lesson_id
                    AND l.unique_code = '$uniqueCode'";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return $row["COUNT(a.user_id)"];    
    }
    
    function addToLesson($studentId, $courseId, $uniqueCode, $dbc){
        
        if($studentId == "" || $courseId == "" || $uniqueCode == ""){
            return "Empty field!";
        }
        
        $goesToThisCourse = goesTo($studentId, $courseId, $dbc);
        $attendedThisLesson = attendedLesson($studentId, $uniqueCode, $dbc);
        
        consoleLog("attendedThisLesson: ".$attendedThisLesson);
        
        if($goesToThisCourse == 0){
            
            return "<div class='alert alert-warning alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <strong>Warning!</strong> This student is not attending this course!
                        <strong>Add this student to course first</strong>
                    </div>";
        } else if ($attendedThisLesson == 1) {
            
            return "<div class='alert alert-warning alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <strong>Warning!</strong> This student attended this lesson!
                    </div>";
            
        } else {
            
            $query = "SELECT lesson_id FROM lessons
                        WHERE unique_code = '$uniqueCode'";
                        
            $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
            $row = mysqli_fetch_array($result);
            $lessonId = $row["lesson_id"];
            
            $sql = "INSERT INTO attended (user_id, lesson_id)
                    VALUES ('$studentId', '$lessonId')";
        
            mysqli_query($dbc, $sql);
            mysqli_close($dbc);   
            
            return "<div class='alert alert-success alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <strong>Success!</strong> Student successfully to this lesson!
                    </div>";
        }
    }
    
    function goesTo($studentId, $courseId, $dbc){
        
        consoleLog("StudentId: ".$studentId." | courseId: ".$courseId);
        
        $query = "SELECT COUNT(course_id) FROM goesTo
                    WHERE user_id = '$studentId'
                    AND course_id = '$courseId'";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        
        return $row["COUNT(course_id)"];             
    }
    
    
    function getCourses($professorId, $dbc){
        $query = "SELECT course_id, course_name FROM courses
                    WHERE prof_id = '$professorId'
                    ORDER BY course_id";
        
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
        
    }

    function getStudents($professorId, $dbc){
        
        // Select only students
        $query = "SELECT DISTINCT u.user_id, u.firstname, u.lastname FROM users u, role r, goesTo g, courses c
                    WHERE u.user_id = r.user_id 
                    AND r.role = 'student'";
        
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
    
    function fillStudentsDropdownMenu($professorId, $dbc){
        
        $result = getStudents($professorId, $dbc);
        
        $dropdownContent = '<option value="" disabled selected><i>No student selected</i></option>';
        
        while($row = mysqli_fetch_array($result)){
            $dropdownContent .= '<option value="'.$row["user_id"].'">'.$row["firstname"].' '.$row["lastname"].'</option>';
            
        }

        return $dropdownContent;
    }
    
    function fillCoursesDropdownMenu($professorId, $dbc){
        
        consoleLog("Prof ID:".$professorId);
        $result = getCourses($professorId, $dbc);
        
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
                        <li><a href="courses.php">Courses</a></li>
                        <li><a href="lesson.php">Lessons</a></li>
                        <li><a href="students.php">Student Attendance</a></li>
                        <li class="active"><a href="manualEntry.php">Manual entry</a></li>
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
                    <div class="col-sm-6">
                        <div class = "panel panel-primary">
                            <div class="panel-heading"><h4>Manually add student to lesson</h4></div>
                            <div class = "panel-body">
                                <label for="select_1">Select a student</label>
                                    <form action="#" method="post">
                                        <select class="form-control" id="select_1_left" name="studentSelectLeft">
                                            <?php
                                                echo $dropdownStudents
                                            ?>
                                        </select>
                                        
                                        <script>
                                            $(function() {
                                                if (localStorage.getItem('select_1_left')) {
                                                    $("#select_1_left option").eq(localStorage.getItem('select_1_left')).prop('selected', true);
                                                }
                                            
                                                $("#select_1_left").on('change', function() {
                                                    localStorage.setItem('select_1_left', $('option:selected', this).index());
                                                });
                                            });
                                        </script>
                                        
                                        <div class="row top-buffer2">
                                            <label for="select_2_left">Select a course</label>
                                            <select class="form-control" id="select_2" name="courseSelectLeft">
                                                <?php
                                                    echo $dropdownCourses
                                                ?>
                                            </select>
                                            
                                            <script>
                                                $(function() {
                                                    if (localStorage.getItem('select_2_left')) {
                                                        $("#select_2_left option").eq(localStorage.getItem('select_2_left')).prop('selected', true);
                                                    }
                                                
                                                    $("#select_2_left").on('change', function() {
                                                        localStorage.setItem('select_2_left', $('option:selected', this).index());
                                                    });
                                                });
                                            </script>
                                        </div>
                                        
                                        <div class="row top-buffer2">
                                            <div class="input-group">
                                                <span class="input-group-addon">Unique code:</span>
                                                <input id="courseName" type="text" class="form-control" name="uniqueCode" placeholder="Enter unique code">
                                            </div>
                                        </div>
                                        <div class="row top-buffer2">
                                            <input class="btn btn-primary" type="submit" name="addToLessonButton" value="Add student to lesson" />
                                        </div>
                                    </form>
                                <h4><?php echo $addToLessonResult ?></h4>
                            </div>
                        </div>
                    </div>
                
                
                <div class="row">
                    <div class="col-sm-6">
                        <div class = "panel panel-primary">
                            <div class="panel-heading"><h4>Manually add student to course</h4></div>
                            <div class = "panel-body">
                                <label for="select_1">Select a student</label>
                                <form action="#" method="post">
                                    <select class="form-control" id="select_1" name="studentSelectRight">
                                        <?php
                                            echo $dropdownStudents
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
                                        <select class="form-control" id="select_2" name="courseSelectRight">
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
                                        <input class="btn btn-primary" type="submit" name="addToCourseButton" value="Add student to course" />
                                    </div>
                                </form>
                                <h4><?php echo $addToCourseResult ?></h4>
                            </div>
                        </div>    
                    </div>
                </div>
            </div>    
        </body>
    </head>
</html>