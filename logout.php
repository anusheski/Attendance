<?php
    session_start();
    session_unset();
    session_destroy();
    ob_start();
    header("Location: https://attendance-system-js5898.c9users.io/login.php");
    ob_end_flush(); 
    exit();
?>