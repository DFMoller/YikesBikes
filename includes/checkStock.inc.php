<?php

    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    $shortname = $_POST['shortname'];
    $count = $_POST['count'];

    include_once("db_connect.php");

    $sql = "SELECT * FROM bikes WHERE shortname = ?";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: redirect.php?destination=products.php&error=checkStock_statement_failed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $shortname);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if (!$bike = mysqli_fetch_assoc($result)) {
        header("Location: redirect.php?destination=products.php&error=checkStock_result_statement_failed");
        exit();
    }
    mysqli_stmt_close($stmt);

    if ($bike['stock'] < $count) {
        echo "false";
    } else {
        echo "true";
    }