<?php
    /**
     *  Users: first, pass
     */
    
    
    // Set parameters for the server
    $servername = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $username, "", $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
    
    $firstname=$_POST['firstname'];
    $lastname=$_POST['lastname'];
    $email=$_POST['email'];
    $department = $_POST['department'];
    $hire_date = $_POST['hire_date'];
    $birth_date = $_POST['birth_date'];
    $gender = $_POST['gender'];
    $home_address = $_POST['home_address'];
    $phone = $_POST['phone'];
    $username=$_POST['username'];
    $password=$_POST['password'];
    
    $loginMessage = "<div class='alert alert-info alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Already have an account?</b> <a href='login.php'>Click here to <b>Log In</b></a>
                    </div>
                    <div class='alert alert-warning alert-dismissable'>
                        <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                        <b>Professor?</b> <a href='studentRegister.php'>Click here to create <b>professor account</b></a>
                    </div>";
    
    // Insert statement
    $required = array("firstname", "lastname", "email", "username", "password");            // Array to check, if all the fields are filled
    $error = false;

    if(isset($_POST['createAccountButton'])){
        foreach ($required as $field) {                                 
            if(empty($_POST[$field]) === TRUE){                         // Error - all fields are not filled, do not query the SQL
                $error = true;
            }
        }
        
        if($error == FALSE){                                            // The data are OK, and can be sent to the DB
           
            // Check duplicate entries
            $sqlCountQuery = "SELECT COUNT(user_id) FROM users WHERE username = '$username'";
            $sqlCountResult = @mysqli_query($dbc, $sqlCountQuery) or die("Error: ".mysqli_error($dbc));
                            
            $row = mysqli_fetch_array($sqlCountResult);
            $number = $row['COUNT(user_id)'];
            
            if($number == 0){               // If user, who is creating account doesn't already exist
                
                // Insert basic data into users table
                $pw = hash_hmac('sha512', 'salt' . $password, $_SERVER['site_key']);
                $query = "INSERT INTO users (firstname, lastname, email, username, password) 
                            VALUES ('" . $firstname . "', '" . $lastname ." ', '" . $email . "', '" . $username ." ', '" . $pw . "')";
                mysqli_query($dbc, $query) or die(mysqli_error($dbc));
                
                // Get newly generated userID from users table
                $query = "SELECT user_id FROM users
                                WHERE username = '$username'";
                $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
                $row = mysqli_fetch_array($result);
                $user_id = $row["user_id"]; 
                
                
                $query = "INSERT INTO role (user_id, role)
                            VALUES('$user_id', 'student')";
                mysqli_query($dbc, $query) or die(mysqli_error($dbc));
                
                mysqli_close($dbc);   
                header('Location: https://attendance-system-js5898.c9users.io/login.php');
                
            } else {
                // Duplicate user
                $loginMessage = "<div class='alert alert-warning alert-dismissable'>
                                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    This username already exists. Choose another username.<br>
                                    <b>Already have an account?</b> <a href='login.php'>Click here to <b>Log In</b></a>
                                </div>";
            }
        } else {
            // Bad input
            $loginMessage = "<div class='alert alert-danger alert-dismissable'>
                                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <b>Already fields need to be filled</b><br>
                                    <b>Already have an account?</b> <a href='login.php'>Click here to <b>Log In</b></a>
                                </div>";
        }
    }

    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
    
    
?> 

<html>
    
    <head>
        <title>Login</title>
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
                min-width:120px;
                text-align:left;
            }
            
            
        </style>
        <img src="Images/AttendanceSys.PNG" alt="Logo" style="width:100%;">
            <nav class="navbar navbar-inverse">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <a class="navbar-brand" href="#">AttendanceSystem</a>
                    </div>
                </div>
            </nav>
            <?php echo $loginMessage ?>
        
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    <div class = "panel panel-primary">
                        <div class="panel-heading">
                            <h3>Welcome to AttendanceSystem</h3>
                        </div>
                        <div class="panel-body">
                        <form method="POST">
                            <h4>Create new <b><i>student</i></b> account</h4>
                            <div class="row top-buffer2">
                                <div class="input-group">
                                    <span class="input-group-addon">First name:</span>
                                    <input type="text" class="form-control" name="firstname" placeholder="First name">
                                </div>
                            </div>
                            
                            <div class="row top-buffer2">
                                <div class="input-group">
                                    <span class="input-group-addon">Last name:</span>
                                    <input type="text" class="form-control" name="lastname" placeholder="Last name">
                                </div>
                            </div>
                            
                            <div class="row top-buffer2">
                                <div class="input-group">
                                    <span class="input-group-addon">E-mail:</span>
                                    <input type="text" class="form-control" name="email" placeholder="E-mail">
                                </div>
                            </div>
                            
                            <div class="row top-buffer2">
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Username: </b></span>
                                    <input type="text" class="form-control" name="username" placeholder="Choose your username">
                                </div>
                            </div>
                            
                            <div class="row top-buffer2">
                                <div class="input-group">
                                    <span class="input-group-addon"><b>Password: </b></span>
                                    <input type="text" class="form-control" name="password" placeholder="Choose your password">
                                </div>
                            </div>
                            
                            <div>
                                <?php echo $exitCode ?>
                            </div>
                            
                            <div class="pull-right">
                                <div class="row top-buffer2">
                                    <input class="btn btn-lg btn-success" type="submit" name="createAccountButton" value="Create new account"/>
                                </div>  
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </body>
    </head>
</html>