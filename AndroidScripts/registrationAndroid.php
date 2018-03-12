<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";

    $dbc = mysqli_connect($hostname, $username, $password, $dbname);
    
    // Get userId from phones' request
    $fn = $_POST['firstname'];
    $ln = $_POST['lastname'];
    $em = $_POST['email'];
    $un = $_POST['username'];
    $pw = $_POST['password'];
    
    $query = "SELECT COUNT(user_id) 
                FROM users
                WHERE username = '$un'";
                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $countUsername = $row['COUNT(user_id)'];
    
    if($countUsername != 0){
        echo 1;
        exit();
    }
    
    // Insert basic data into users table
    $pw = hash_hmac('sha512', 'salt' . $pw, $_SERVER['site_key']);
    $query = "INSERT INTO users (firstname, lastname, email, username, password) 
                VALUES ('" . $fn . "', '" . $ln ." ', '" . $em . "', '" . $un ." ', '" . $pw . "')";
    mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    
    // Get newly generated userID from users table
    $query = "SELECT user_id FROM users
                    WHERE username = '$un'";
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $user_id = $row["user_id"]; 
    
    $query = "INSERT INTO role (user_id, role)
                VALUES('$user_id', 'student')";
    mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    
    mysqli_close($dbc);
    echo 0;
?>