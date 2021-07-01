<?php

    include_once("db_connect.php");

    $name = $_GET['name'];

    $sql = "SELECT * FROM users WHERE username LIKE ?";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Failed to Check Database...";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $name);
    mysqli_stmt_execute($stmt);
    $resultData = mysqli_stmt_get_result($stmt);
    if ($row = mysqli_fetch_assoc($resultData)) {
        // User found
        echo true;
    } else {
        // No such user found
        echo false;
    }

    mysqli_stmt_close($stmt);
