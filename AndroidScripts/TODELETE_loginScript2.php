<?php 
    
    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
    
    $con = mysqli_connect($hostname, $username, $password, $dbname);
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

?>