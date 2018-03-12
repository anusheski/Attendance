<?php

    include('phpqrcode.php');
    
    // https://www.sitepoint.com/generate-qr-codes-in-php/
    
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
    
    $username = $_SESSION['userId'];
    $userId = $_SESSION['userIdCode'];
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $DBusername, $DBpassword, $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
            
    
    // Fill the dropdown menu
    $result = getCourses($userId, $dbc);
    $dropdownContent = fillDropdownMenu($result);
    
    $selectedCourseName = "";
    $courseName = "";
    $lessonAddress = "";
    $lessonCoordinates = "";
    
    // Get dateTime for Adding lesson
    //$dt = new DateTime("now", new DateTimeZone('Europe/Moscow'));
    //$dateTimeForm->format('Y-m-d H:i:s');
    date_default_timezone_get('Europe/Moscow');
    $dateTimeForm = date("'Y-m-d h:m:s'", time());
    
    // Show lesson list
    if(isset($_POST['selectLesons'])){
        
        $selectedCourseName = $_POST['courseSelect'];                 
        $_SESSION['selectedCourseQR'] = $selectedCourseName;          // Saving needed --> QR is generated on refresh
        
        // User message above table
        $courseName = "<div class='alert alert-info alert-dismissable'>
                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                            Lessons for <i><b>".$selectedCourseName."</i></b>
                       </div>";  
        
        $tableContent = getLessons($selectedCourseName, $userId, $dbc);
        $tableLine = fillTable($tableContent);
        
        // Always generate QR code for the last lesson of the selected course --> Avoid refresh issue
        generateQRcode($_SESSION['selectedCourseQR'], $dbc);
    }
    
    // Add a new lesson button event listener
    if(isset($_POST['insertButton'])){
        
        $selectedCourseName = $_POST['lessonInsert'];
        $dateTime = $_POST['dateTime'];
        $lessonTitle = $_POST['lessonTitle'];
        $lessonDescription =$_POST['lessonDescription'];
        $lessonAddress = $_POST['lessonAddress'];
        
        $exitCode = insertLesson($selectedCourseName, $dateTime, $lessonTitle, $lessonDescription, $lessonAddress, $dbc);
        $userMessage = "";
        if($exitCode == 0){
            $userMessage = "<div class='alert alert-success alert-dismissable'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                <b>Success:</b> Lesson successfully added!
            </div>";
        } else if ($exitCode == 1){
            $userMessage = "<div class='alert alert-warning alert-dismissable'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                <b>Warning:</b> All fields need to be filled!
            </div>";
        } else if ($exitCode == 3){
            $userMessage = "<div class='alert alert-warning alert-dismissable'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                <b>Warning:</b> Address not found - please check the address
            </div>";
        } else {
            $userMessage = "<div class='alert alert-danger alert-dismissable'>
                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                <b>Error:</b> You cannot add lessons, if there are no courses created! 
                <br><a href='courses.php'>Click here to create your first course</a>
            </div>";
        }
    }
    
    // Just redirect - PNG file previously generated
    if(isset($_POST['showFullscreen'])){
        header('Location: https://attendance-system-js5898.c9users.io/fullscreen.php');
    }
    
    /** 
     * FUNCTIONS BELOW 
    */
    
    function insertLesson($selectedCourseName, $dateTime, $lessonTitle, $lessonDescription, $lessonAddress, $dbc){
        
        consoleLog("CourseName: ".$selectedCourseName." | DataTime: ".$dateTime." | Title: ".$lessonTitle." | Description: ".$lessonDescription." | Address: ".$lessonAddress);
        $error = true;
        
        if($selectedCourseName != "" && $dateTime != "" && $lessonTitle != "" && $lessonDescription != "" && $lessonAddress != ""){
            $error = false;
        }
        
        
        for($i = 0; $i < 3; $i++){
            $lessonCoordinates = getCoordinates($lessonAddress);
            //consoleLog("Lat: ".$lessonCoordinates[0]." | Long: ".$lessonCoordinates[1]);
            
            if($lessonCoordinates[0] != "" && $lessonCoordinates[1] != ""){
                break;
            }
        }
        
        if($lessonCoordinates[0] == "" || $lessonCoordinates[1] == ""){          // getAddress failed!
            return 3;
        }
        
        
        consoleLog("LessonCoordinates: ".$lessonCoordinates[0]." ".$lessonCoordinates[1]);
        $latitude = $lessonCoordinates[0];
        $longitude = $lessonCoordinates[1];
        
        
        if($error == false){                                            // The data are OK, and can be sent to the DB

            // Get course ID
            $sqlSelect = "SELECT course_id FROM courses WHERE '$selectedCourseName' = course_name";
            $result = @mysqli_query($dbc, $sqlSelect) or die(mysqli_error($dbc));
            $row = mysqli_fetch_array($result);
            $courseId = $row["course_id"];
            consoleLog("CourseID: ".$courseId);
            
            $uniqueCode = generateRandomString(6, $dbc);
            
            // Insert new lesson
            $sql = "INSERT INTO lessons (course_id, lesson_timestamp, unique_code, lesson_title, lesson_description, latitude, longitude)
                    VALUES ('$courseId', '$dateTime', '$uniqueCode', '$lessonTitle', '$lessonDescription', '$latitude', '$longitude')";
            consoleLog("SQL: ".$sql);
           
            mysqli_query($dbc, $sql) or die(mysqli_error($dbc));
            mysqli_close($dbc);   
            
            return 0;
        } else if (countCourses($user_id, $dbc) == 0){
            return 2;
        } else {
            return 1;
        }
    }
    
    // Gets coordinates from address
    function getCoordinates($address){
 
        $address = str_replace(" ", "+", $address); // replace all the white space with "+" sign to match with google search pattern
        $url = "http://maps.google.com/maps/api/geocode/json?sensor=false&address=$address";
        $response = file_get_contents($url);
        $json = json_decode($response, TRUE); //generate array object from the response from the web
        
        $returnArray = array($json['results'][0]['geometry']['location']['lat'], $json['results'][0]['geometry']['location']['lng']);
         
        return $returnArray;
        //return ($json['results'][0]['geometry']['location']['lat'].",".$json['results'][0]['geometry']['location']['lng']);
         
    }
    
    function generateQRcode($selectedCourseName, $dbc){
        
        $sqlSelect = "SELECT l.unique_code, l.lesson_title FROM courses c, lessons l 
        WHERE '$selectedCourseName' = c.course_name AND c.course_id = l.course_id
        ORDER BY lesson_id DESC LIMIT 1";
        
        $result = @mysqli_query($dbc, $sqlSelect) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        $lessonTitle = $row["lesson_title"];
        $uniCode = $row["unique_code"];
        consoleLog("UniCode: ".$uniCode);
        QRcode::png($uniCode, "smallQR.png", "L", 6, 1);
        QRcode::png($uniCode, "bigQR.png", "L", 24, 1);
        
    }
    
    function generateRandomString($length, $dbc) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        
        // Safety check for duplicates
        $sql = "SELECT COUNT(lesson_id) FROM lessons 
                WHERE unique_code = '$randomString'";
        $result = @mysqli_query($dbc, $sql) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        $number = $row["COUNT(lesson_id)"];

        
        if($number != 0){
            consoleLog("Result: ".$number);
            generateRandomString(6);
        } 
        
        return $randomString;
    }


    function getCourses($user_id, $dbc){
        
        $query = "SELECT c.course_id, c.course_name FROM courses c
                    WHERE c.prof_id = '$user_id'
                    ORDER BY c.course_id ASC";
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
    }
    
    function countCourses($user_id, $dbc){
        $query = "SELECT COUNT(c.course_id) FROM courses c
                    WHERE c.prof_id = '$user_id'";
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        $row = mysqli_fetch_array($result);
        $number = $row["COUNT(c.course_id)"];
        
        return $number;
    }
    
    function getLessons($selectedCourse, $userId, $dbc){
        $query = "SELECT l.lesson_timestamp, l.unique_code, l.lesson_title, l.lesson_description, l.lesson_id 
                    FROM courses c, lessons l 
                    WHERE c.prof_id ='$userId' 
                    AND c.course_id = l.course_id 
                    AND c.course_name = '$selectedCourse'";
        
        //consoleLog("Query: ".$query);
        $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
        return $result;
    }
    
    function createTableHeader(){
        $tableLine =    '<table class="table table-bordered table-hover table-striped">';
        $tableLine .=   "<thead>
                            <tr>
                                <th>#</th>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Timestamp</th>
                                <th>Unique code</th>
                            </tr>
                        </thead>";
        return $tableLine;
    }
    
    function fillTable($result){
        
        $tableLine = createTableHeader();
        $rowNum = 1;
        $tableLine .= "<tbody>";
        
        while($row = mysqli_fetch_array($result)){
            
            
            $tableLine .=  '<tr>
                                <th scope="row"> '.$rowNum.'</th>
                                <td> '.$row["lesson_id"].'</td>
                                <td> '.$row["lesson_title"].'</td>
                                <td> '.$row["lesson_description"].'</td>
                                <td> '.$row["lesson_timestamp"].'</td>
                                <td> '.$row["unique_code"].'</td>
                            </tr>';
            $rowNum++;
        }
        $tableLine .= "</tbody></table>";
        
        $tableLine .= '<form action="#" method="post">
                                    <input class="btn btn-primary" id="QRShow" type="submit" name="generateQR" value="Generate QR code for last lesson" />
                                    <div id="result"></div>
                                </form>';
        
        return $tableLine;
    }
    
    function fillDropdownMenu($result){
        
        $dropdownContent = '<option value="" disabled selected><i>No course selected</i></option>';
        
        while($row = mysqli_fetch_array($result)){
            $dropdownContent .= '<option value="'.$row["course_name"].'">'.$row["course_name"].'</option>';
            
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
            <title>Lessons</title>
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
                        <li class="active"><a href="lesson.php">Lessons</a></li>
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
                    <br>
                </div>
            </div> 
            
            <div class="container">
                <div class="row">
                    <div class="col-sm-7">
                        <div class = "panel panel-primary">
                            <div class="panel-heading"><h4>Show lesson list</h4></div>
                            <div class = "panel-body">
                                <label for="select_1">Select a course</label>
                                    <form action="#" method="post">
                                       <select class="form-control" data-style="btn-primary" id="select_1" name="courseSelect">
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
                                        <input class="btn btn-primary" type="submit" name="selectLesons" value="Update" />
                                    </form>
                                
                                    <h4><?php echo $courseName ?></h4>
                                    <?php echo $tableLine  ?>
                                
                                    <script>
                                    $(function(){
                                        // don't cache ajax or content won't be fresh
                                        $.ajaxSetup ({
                                            cache: false
                                        });
                                        var ajax_load = "<br><div class='alert alert-success alert-dismissable'><a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a><h4>QR Code for successfully generated<h4></div><img src='smallQR.png' alt='qrCode'>" + 
                                                        "<br><br>" + 
                                                        "<input class='btn btn-primary' type='submit' name='showFullscreen' value='Show QR code fullscreen' />";
                                    
                                        $("#QRShow").click(function(e){
                                            e.preventDefault();
                                            $("#result").html(ajax_load);
                                        });
                                    
                                    // end  
                                    });
                                    </script>
                                </div>
                            </div>
                        </div>
                    
                    
                    <div class="col-sm-5">
                        <div class = "panel panel-default">
                            <div class="panel-heading"><h4>Add a lesson</h4></div>
                                <div class = "panel-body">
                                    <form method="POST">
                                    <div class="row top-buffer">
                                    <select class="form-control" id="select_1" name="lessonInsert">
                                        <?php
                                            echo $dropdownContent
                                        ?>
                                    </select>
                                    
                                    <div class="row top-buffer2">
                                    <div class="input-group">
                                        <span class="input-group-addon">Timestamp:</span>
                                        <input id="dateTime" type="text" class="form-control" name="dateTime" placeholder="DateTime" value=<?php echo $dateTimeForm ?>>
                                    </div>
                                    
                                    <div class="row top-buffer2">
                                    <div class="input-group">
                                        <span class="input-group-addon">Lesson title:</span>
                                        <input id="lessonTitle" type="text" class="form-control" name="lessonTitle" placeholder="Enter the lesson title">
                                    </div>
                                    
                                    <div class="row top-buffer2">
                                    <div class="input-group">
                                        <span class="input-group-addon">Lesson description:</span>
                                        <input id="lessonDescription" type="text" class="form-control" name="lessonDescription" placeholder="Enter the lesson description">
                                    </div>
                                    <div class="row top-buffer2">
                                    <div class="input-group">
                                        <span class="input-group-addon">Lesson address:</span>
                                        <input id="lessonAddress" type="text" class="form-control" name="lessonAddress" placeholder="Enter the lesson address">
                                    </div>
                                    <div class="row top-buffer2">
                                        <div>
                                            <?php echo $userMessage ?>
                                        </div>
                                    </div>
                                    <input class="btn btn-primary" type="submit" name="insertButton" value="Add"/>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </head>
</html>