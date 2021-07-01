<?php

    

    session_start();

    if (!isset($_GET['destination']) || !isset($_SESSION['email'])) {
        header("Location: products.php");
    } elseif (isset($_GET['destination']) && !isset($_SESSION['email'])) {
        // user not logged in
        // Redirect
        header("Location: " . $destination);
    } else {
        // User logged in and destination specified
        $timestamp = date('Y-m-d H:i:s');
        $email = $_SESSION['email'];

        // Log user activity
        include_once("includes/db_connect.php");
        $sql = "UPDATE users SET last_online=? WHERE email=?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: home.php?error=stmtfailed_activity");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $timestamp, $email); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        $parameters = $_GET;
        $location = $parameters['destination'];

        if (count($parameters) > 1) {
            $location .= "?";
        }

        $first = true;
        foreach($parameters as $key => $val) {
            if ($key != "destination") {
                if ($first) {
                    $location .= "$key=$val";
                    $first = false;
                } else {
                    $location .= "&$key=$val";
                }
            }
        }

        // Redirect
        header("Location: " . $location);

        

    }