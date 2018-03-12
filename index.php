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
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $DBusername, $DBpassword, $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
    
    
    // Get users table
    $userData = getUserData($username, $dbc);  
    $row = mysqli_fetch_array($userData);
    
    // Set session variables - for professor account
    $_SESSION['userIdCode'] = $row["user_id"];
    $_SESSION['firstname'] = $row["firstname"];
    $_SESSION['lastname'] = $row["lastname"];
    $_SESSION['role'] = $row["role"];
    
    // Get professor details
    function getUserData($username, $dbc){
        $query = "SELECT * FROM users u, role r, prof_info p
                    WHERE u.username = '$username'
                    AND u.user_id = r.user_id
                    AND p.user_id = u.user_id
                    AND r.role = 'professor'";
        $result = @mysqli_query($dbc, $query);
        return $result;
    }
    
    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
    
?> 

<html lang="en">
    <head>
        <title>Home</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
        <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
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
                
                .pull-left {
                     text-align: left
                }
            </style>
            <img src="Images/AttendanceSys.PNG" alt="Logo" style="width:100%;">
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="index.php">AttendanceSystem</a>
                    </div>
                    <ul class="nav navbar-nav">
                        <li class="active"><a href="index.php">Home</a></li>
                        <li><a href="courses.php">Courses</a></li>
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
            
            <div class="container">
                <div class="row">
                    <div class="col-sm-8">
                        <div class = "panel panel-primary">
                            <div class="panel-heading">
                                <h3><?php echo $_SESSION['firstname']." ".$_SESSION['lastname']; ?></h3>
                            </div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="Images/profilePic.PNG" class="img-circle img-responsive"></div>
                                    <div class=" col-md-9 col-lg-9 "> 
                                        <table class="table table-user-information table-hover table-stiped">
                                        <tbody>
                                            <tr>
                                                <td>Department:</td>
                                                <td><?php echo $row["department"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Hire date:</td>
                                                <td><?php echo $row["hire_date"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Date of Birth</td>
                                                <td><?php echo $row["birth_date"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Gender</td>
                                                <td><?php echo $row["gender"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Home Address</td>
                                                <td><?php echo $row["home_address"]; ?></td>
                                            </tr>
                                            <tr>
                                                <td>Email</td>
                                                <td><a href="mailto:info@support.com"><?php echo $row["email"]; ?></a></td>
                                            </tr>
                                            <tr>
                                                <td>Phone Number</td>
                                                <td><?php echo $row["phone"]; ?>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-sm-4">
                    <a href="courses.php" class="btn btn-block btn-lg btn-primary"><span class="glyphicon glyphicon-book"></span> My Courses</a>
                    <a href="lesson.php" class="btn btn-block btn-lg btn-primary"><span class="glyphicon glyphicon-calendar"></span> My Lessons</a>
                    <a href="students.php" class="btn btn-block btn-lg btn-primary"><span class="glyphicon glyphicon-check"></span> Check student attendance</a>
                    <a href="manualEntry.php" class="btn btn-block btn-lg btn-primary"><span class="glyphicon glyphicon-cog"></span> Manualy enter data</a>
                    <a href="edit.php" class="btn btn-block btn-lg btn-info"><span class="glyphicon glyphicon-edit"></span> Edit personal data</a>
                </div>
            </div>
        </div>
    </body>
</html>