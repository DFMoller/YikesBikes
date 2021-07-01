<?php

    $username = "yikesbikes";
    $password = "yikesbikes";

    $hashed_username = hash('sha512', $username);
    $hashed_password = hash('sha512', $password);

    include("../includes/db_connect.php");

    $sql = "INSERT INTO admin_users (username, password) VALUES (?, ?);";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Statement Failed";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $hashed_username, $hashed_password);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo "Success";