<?php 

    session_start();

    if (!isset($_SESSION['admin'])) {
        echo "Not an admin User";
        exit();
    }

    if (!isset($_POST['shortname']) || !isset($_POST['count'])) {
        echo "Parameters not found";
        exit();
    }

    $shortname = $_POST['shortname'];
    $count = $_POST['count'];

    include("db_connect.php");

    $sql = "UPDATE bikes SET stock = ? WHERE shortname = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        echo "Statement Failed!";
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $count, $shortname);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    echo true;