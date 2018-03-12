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
    
    // Select statement - for table creation
    
    // Get users table
    
    $userData = getUserData($username, $dbc);                 // Select in users DB
    
    $row = mysqli_fetch_array($userData);
    
    // Set session variables
    $_SESSION['userIdCode'] = $row["user_id"];
    $_SESSION['firstname'] = $row["firstname"];
    $_SESSION['lastname'] = $row["lastname"];
    $_SESSION['role'] = $row["role"];
    
    
    // Add a new lesson button event listener
    if(isset($_POST["commitChanges"])){
        
        $department = $_POST['department'];
        $hire_date = $_POST['hire_date'];
        $birth_date = $_POST['date_of_birth'];
        $gender = $_POST['gender'];
        $home_address = $_POST['home_address'];
        $email = $_POST['email'];
        $phone = $_POST['phone_number'];
        
        $exitCode = updateUserData($email, $department, $hire_date, $birth_date, $gender, $home_address, $phone, $dbc);
        
        if($exitCode == "0"){
            header('Location: https://attendance-system-js5898.c9users.io/index.php');
        } 
    }

    // Get content to fill the fields
    function getUserData($username, $dbc){
        $query = "SELECT * FROM users u, role r, prof_info p
                    WHERE u.username = '$username'
                    AND u.user_id = r.user_id
                    AND p.user_id = u.user_id";
        $result = @mysqli_query($dbc, $query);
        return $result;
    }
    
    // UPDATE data in the DB
    function updateUserData($email, $department, $hire_date, $birth_date, $gender, $home_address, $phone, $dbc){
        
        $user_id = $_SESSION['userIdCode'];
        
        if($email != "" && $user_id != "" && $department != "" && $hire_date != "" && $birth_date != "" && $gender != "" && $home_address != "" && $phone != ""){
            
            $query1 = "UPDATE users
                        SET email = '$email'
                        WHERE user_id = '$user_id'";
                    
            mysqli_query($dbc, $query1) or die(mysqli_error($dbc));
        
            $query2 = "UPDATE prof_info
                        SET user_id = '$user_id', 
                            department = '$department', 
                            hire_date = '$hire_date', 
                            birth_date = '$birth_date', 
                            gender = '$gender',
                            home_address = '$home_address',
                            phone = '$phone'
                        WHERE user_id = '$user_id'";
                        
            mysqli_query($dbc, $query2) or die(mysqli_error($dbc));
            
            $exitCode = 0;
            return $exitCode;
        } else {
            return "<div class='alert alert-warning'>
                        <b>Failed:</b> All fields need to be filled!
                    </div>";
        }
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
                
                .input-group {
                    margin-left: 80px;
                    min-width:300px;
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
                            <div class="col-md-3 col-lg-3 " align="center"> <img alt="User Pic" src="Images/profilePic.PNG" class="img-circle img-responsive"> </div>
                                <div class=" col-md-9 col-lg-9 "> 
                                    <table class="table table-user-information table-hover table-stiped">
                                        <tbody>
                                            <form action="#" method="post">
                                            <tr>
                                                <td>Department:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="department" placeholder="Department" value='<?php echo $row["department"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Hire date:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="hire_date" placeholder="Department" value='<?php echo $row["hire_date"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                           <tr>
                                                <td>Date of Birth:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="date_of_birth" placeholder="Date of birth" value='<?php echo $row["birth_date"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Gender:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="gender" placeholder="Gender" value='<?php echo $row["gender"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Home Address:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="home_address" placeholder="Home address" value='<?php echo $row["home_address"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Email:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="email" placeholder="E-mail" value='<?php echo $row["email"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                                <td>Phone Number:</td>
                                                <td>
                                                    <div class="input-group">
                                                        <input type="text" class="form-control" name="phone_number" placeholder="Phone number" value='<?php echo $row["phone"]; ?>'>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <div>
                                        <?php echo $exitCode ?>
                                    </div>
                                    <div class="pull-right">
                                            <a href="index.php" class="btn btn-lg btn-danger"><span class="glyphicon glyphicon-remove"></span> Cancel</a>
                                            <input class="btn btn-lg btn-success" type="submit" name="commitChanges">
                                    </div>
                                </form>
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
        </body>
    </head>
</html>