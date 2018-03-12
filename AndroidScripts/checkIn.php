<?php

    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
     
    $dbc = mysqli_connect($hostname,$username,$password,$dbname);
    
    $username = $_POST['username'];
    $password = $_POST['password'];
    $uniqueCode = $_POST['uniqueCode'];
    $phoneLatitude = $_POST['latitude'];
    $phoneLongitude = $_POST['longitude'];
    $deviceId = $_POST['deviceId'];
   
    // Check UserID
    $query = "SELECT user_id FROM users
	                WHERE username = '$username'";
	                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $user_id = $row['user_id'];
    
    // Incorrect userID
    if($user_id == "") {
        echo 1;             // UserID doesn't exist
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
        echo 2;             // Incorrect password
        exit();
    }
    //echo 'PassCheck = '.$passCheck;
    
    
    // Get lessonID ID
    $query = "SELECT lesson_id, latitude, longitude FROM lessons
	                WHERE unique_code = '$uniqueCode'";
	                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $lesson_id = $row['lesson_id'];
    $dbLatitude = $row['latitude'];
    $dbLongitude = $row['longitude'];
    

    
    $query = "SELECT COUNT(entry_id) FROM attended
                WHERE lesson_id = '$lesson_id'
                AND user_id = '$user_id'";
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $duplicateEntry = $row['COUNT(entry_id)'];
    
    $query2 = "SELECT COUNT(lesson_id) FROM attended
                WHERE lesson_id = '$lesson_id'
                AND device_id = '$deviceId'";
    $result2 = @mysqli_query($dbc, $query2) or die(mysqli_error($dbc));
    $row2 = mysqli_fetch_array($result2);
    $entriesPerDevice = $row2['COUNT(lesson_id)'];
    
    if($entriesPerDevice > 0){
        echo "Entries per device: ".$entriesPerDevice." LessonID: ".$lesson_id." DeviceFP: ".$deviceId;
        mysqli_close($dbc);
        exit();
    }
    
    if($duplicateEntry == 0){
        
        // TODO FINE TUNING NEEDED
        $tolerance = 0.05;
        // TODO FINE TUNING NEEDED
        $latDiff = abs($phoneLatitude-$dbLatitude);
        $longDiff = abs($phoneLongitude-$dbLongitude);
        
        if(($latDiff < $tolerance && $longDiff < $tolerance) || ($dbLatitude == 0 && $dbLongitude == 0)){
            $query = "INSERT INTO attended (user_id, lesson_id, device_id) VALUES ('$user_id', '$lesson_id', '$deviceId')";
        
            if(mysqli_query($dbc, $query)){
                echo 0;                     // Success
            } else {    
                echo 5;                     // Query Exec error
            }
        
            
        } else {
            echo 3;             // Your location doesn't match the lecutre location
        }
    } else {
        echo 4;                 // Duplicate entry
    }
    
    
    
    mysqli_close($dbc);
?>