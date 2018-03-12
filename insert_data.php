<?php

    $hostname = "localhost";
    $username = "js5898";
    $password = "";
    $dbname = "attendanceDB";
     
    $con = mysqli_connect($hostname,$username,$password,$dbname);
    
    $name = $_POST['name'];
    $email = $_POST['email'];
    $website = $_POST['website'];
    
    
    
    $query = "INSERT INTO testTable (name,email,website) VALUES ('$name','$email','$website')";
    
    if(mysqli_query($con, $query)){
        echo 'Data Inserted Successfully';
    } else {
        echo 'Try Again';
        
    }
    
    mysqli_close($con);
?>