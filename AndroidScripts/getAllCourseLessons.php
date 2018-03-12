<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";

    
    $dbc = mysqli_connect($hostname, $username, $password, $dbname);
    
    // Get userId from phones' request
    $course_id = $_POST['course_id'];
    
    $query = "SELECT l.lesson_id, l.lesson_title, l.lesson_timestamp
                FROM lessons l
                WHERE l.course_id = '$course_id'";
                
    $r = mysqli_query($dbc, $query);
    $result = array();
    
    while($row = mysqli_fetch_array($r)){
        array_push($result, array(
            'lesson_id'=>$row['lesson_id'],
            'lesson_title'=>$row['lesson_title'],
            'lesson_description'=>$row['lesson_timestamp']
        ));
    }
    
    echo json_encode(array('result'=>$result));
    mysqli_close($dbc);
    
?>