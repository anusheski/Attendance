<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";

    $dbc = mysqli_connect($hostname, $username, $password, $dbname);
    
    // Get userId from phones' request
    $user_id = $_POST['user_id'];
    $course_id = $_POST['course_id'];
    
    $query = "SELECT l.lesson_id, l.lesson_title, l.lesson_description, c.required_attendance
                FROM lessons l, attended a, courses c
                WHERE l.course_id = '$course_id'
                AND l.course_id = c.course_id
                AND l.lesson_id = a.lesson_id
                AND a.user_id = '$user_id'";
                
    $r = mysqli_query($dbc, $query);
    $result = array();
    
    while($row = mysqli_fetch_array($r)){
        array_push($result, array(
            'lesson_id'=>$row['lesson_id'],
            'lesson_title'=>$row['lesson_title'],
            'lesson_description'=>$row['lesson_description'],
            'required_attendance'=>$row['required_attendance']
        ));
    }
    
    echo json_encode(array('result'=>$result));
    mysqli_close($dbc);
?>