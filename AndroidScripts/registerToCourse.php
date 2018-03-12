<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";

    $dbc = mysqli_connect($hostname, $username, $password, $dbname);
    
    // Get userId from phones' request
    $user_id = $_POST['user_id'];
    $course_id = $_POST['course_id'];
    
    //consoleLog("UserID: ".$user_id." | CourseID: ".$course_id);
    
    $query = "SELECT COUNT(user_id) 
                FROM goesTo
                WHERE user_id = '$user_id'
                AND course_id = '$course_id'";
                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $countGoesTo = $row['COUNT(user_id)'];
    
    $query = "SELECT COUNT(course_id) 
                FROM courses
                WHERE course_id = '$course_id'";
                
    $result = @mysqli_query($dbc, $query) or die(mysqli_error($dbc));
    $row = mysqli_fetch_array($result);
    $countCourses = $row['COUNT(course_id)'];
    
    // Incorrect userID
    if($countGoesTo != 0) {
        echo 1;                 // Alerady registered
        exit();
    }
    
    if($countCourses != 1){
        echo 2;                 // Course doesn't exist
        exit();
    }
    
    $query = "INSERT INTO goesTo (user_id, course_id)
                        VALUES ('$user_id', '$course_id')";
                
    mysqli_query($dbc, $query);
    mysqli_close($dbc);
    
    echo 0;
    
    
    function consoleLog($msg) {
       $msg = str_replace('"', '\\"', $msg); // Escaping double quotes 
        echo "<script>console.log(\"$msg\")</script>";
    }
?>