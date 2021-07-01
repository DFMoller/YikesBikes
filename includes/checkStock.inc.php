<?php

    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    $shortname = $_POST['shortname'];
    $count = $_POST['count'];

    include_once("db_connect.php");
    include_once("functions.php");

    if(checkStock($conn, $shortname, $count)) {
        echo "true";
    } else {
        echo "false";
    }
    