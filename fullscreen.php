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

?> 

<html lang="en">
        <head>
            <title>QR Code</title>
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
                <div class = "panel panel-default">
                    <div class="panel-heading">
                        <div class = "pull-right">
                            <a href="lesson.php" class="btn btn-lg btn-danger pull-right">
                            <span class="glyphicon glyphicon-remove">
                            </span> Close</a>
                        </div>
                            <h4>QR Code for the last lesson</h4>
                    </div>
                        <div class = "panel-body">
                            
                            <div class="row">
                                <div class="col-sm-12">
                                    <img src='bigQR.png' alt='qrCode' class='img-responsive center-block'>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
    </head>
</html>