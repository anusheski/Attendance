<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";

    
    $dbc = mysqli_connect($hostname, $username, $password, $dbname);
    
    // Get userId from phones' request
    $user_id = $_POST['user_id'];
    
    $query = "SELECT c.course_name, c.course_id
                FROM courses c, goesTo g
                WHERE g.user_id = '$user_id'
                AND g.course_id = c.course_id
                ORDER BY c.course_id ASC";
                
    $r = mysqli_query($dbc, $query);
    $result = array();
    
    while($row = mysqli_fetch_array($r)){
        array_push($result, array(
            'course_id'=>$row['course_id'],
            'course_name'=>$row['course_name']
        ));
    }
    
    //echo $result;
    echo json_encode(array('result'=>$result));
    mysqli_close($dbc);
    
    
    /*
    $query = "SELECT * FROM testTable";
    
    $r = mysqli_query($con, $query);
    
    $result = array();
    
    while($row = mysqli_fetch_array($r)){
        array_push($result,array(
            'id'=>$row['id'],
            'name'=>$row['name'],
            'email'=>$row['email'],
            'website'=>$row['website'],
            'reg_date'=>$row['reg_date']
        ));
    }
    
    echo json_encode(array('result'=>$result));
    
    mysqli_close($con);
    */

?>