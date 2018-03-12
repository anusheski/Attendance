<?php
    
    // TODO Cannot enter? Code seems to be OK
    
    // Set parameters for the server
    $servername = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
    
    // Creates a DB connection
    $dbc=   mysqli_connect($servername, $username, "", $dbname) or
            die('Error - cannot connect to the server' . mysqli_connect_error());
    
    $username=$_POST['username'];
    $password=$_POST['password'];
    
    // Insert statement
    $required = array("username", "password");            // Array to check, if all the fields are filled
    $error = false;

    foreach ($required as $field) {                                 
        if(empty($_POST[$field]) === TRUE){                         // Error - all fields are not filled, do not query the SQL
            $error = true;
        }
    }
    $createNewAccountMessage = "<div class='alert alert-info alert-dismissable'>
                                    <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                    <b>Don't have an account?</b> <a href='register.php'>Click here to create new account!</a>
                                </div>";
    
    if($error == false){                                            // The data are OK, and can be sent to the DB
        $pw = hash_hmac('sha512', 'salt' . $password, $_SERVER['site_key']);                // Encrypt password -->  // https://stackoverflow.com/questions/5059151/encrypting-decrypting-passwords-to-and-from-a-mysql-database
         
         
        consoleLog("Username: ".$username);       
        $query = "SELECT u.password FROM users u, role r
                    WHERE u.username = '$username'
                    AND u.user_id = r.user_id
                    AND r.role = 'professor'";
        $result = @mysqli_query($dbc, $query) 
                    or die("Error: ".mysqli_error($dbc));
        
        $row = mysqli_fetch_array($result);
        $dbPass = $row['password'];
        //mysqli_close($dbc);   
        
        if($pw == $dbPass){
            session_start();
            $_SESSION['userId'] = $username;
            header('Location: https://attendance-system-js5898.c9users.io/');
            exit();
        } else {
            
            $createNewAccountMessage = "<div class='alert alert-warning alert-dismissable'>
                                            <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                            <b>Your username or password is incorrect.</b><br>
                                            <b>Don't have an account?</b> <a href='register.php'>Click here to create new account!</a>
                                        </div>";
            
            $query = "SELECT COUNT(u.user_id) FROM users u, role r
                        WHERE u.username = '$username'
                        AND u.user_id = r.user_id
                        AND r.role = 'student'";
                        
            $result = @mysqli_query($dbc, $query) or die("Error: ".mysqli_error($dbc));
            $row = mysqli_fetch_array($result);
            $isStudent = $row['COUNT(u.user_id)'];
            if($isStudent == 1){
                $createNewAccountMessage .= "<div class='alert alert-danger alert-dismissable'>
                                                <a href='#' class='close' data-dismiss='alert' aria-label='close'>&times;</a>
                                                <b>Warning!</b> Is is <b>not possible to Log In</b> with <b>student account.</b> Students can only use mobile application!
                                            </div>";
            }
        }
    }
    
    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
    
    $error = false;
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
            
            .input-group {
                margin-left: 0px;
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
                        <a class="navbar-brand" href="#">AttendanceSystem</a>
                    </div>
                </div>
            </nav>
            <?php echo $createNewAccountMessage ?>
            
        <div class="container">
            <div class="row">
                <div class="col-sm-5">
                    <div class = "panel panel-primary">
                        <div class="panel-heading">
                            <h3>Welcome to AttendanceSystem</h3>
                        </div>
                    <div class="panel-body">
                        <form method="POST">
                            <table>
                                <h4>Enter your username and password</h4>
                                <div class="input-group">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
                                    <input id="username" type="text" class="form-control" name="username" placeholder="Username">
                                </div>
                                <div class="row top-buffer2">
                                    <div class="input-group">
                                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>
                                        <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                                    </div>
                                </div>
                            </table>
                            <br>
                            <div class="pull-right">
                                <input type="submit" class="btn btn-success btn-lg" name="ok" value="Log In"/>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>