<?php

    // DB Info
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
     
    // Database connection
    $dbc = mysqli_connect($hostname,$username,$password,$dbname);
    
    // Get info from Android Request
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if username exists
    $query = "SELECT user_id FROM users
	                WHERE username = '$username'";
	                
	// Store result to user_id
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $user_id = $row['user_id'];
    
    // Incorrect username
    if($user_id == "") {
        echo "UserID doesn't exist!";
        exit();
    }
    
    // Check password
    $pw = hash_hmac('sha512', 'salt' . $password, $_SERVER['site_key']);
    $query = "SELECT COUNT(user_id) FROM users
	                WHERE password = '$pw'
	                AND user_id = '$user_id'";
	                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $passCheck = $row['COUNT(user_id)'];
    
    // Incorrect password
    if($passCheck == 0){
        echo 'Your password is incorrect';
        exit();
    }
    
    
    echo $user_id;
    
    mysqli_close($dbc);
?>