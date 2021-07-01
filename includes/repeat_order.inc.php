<?php

    session_start();

    if(!isset($_SESSION['username'])) {
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    if(!isset($_GET["timestamp"])) {
        header("Location: history.php?error=parameters_not_found");
        exit();
    }

    $timestamp = $_GET['timestamp'];

    // Load History into checkout data:
    include("db_connect.php");
    include("functions.php");
    
    $cart = getCart($conn);

    $history = json_decode($cart['history'], true);
    $checkout_string = json_encode($history[$timestamp]);

    $sql = "UPDATE carts SET checkout = ? WHERE user_id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: redirect.php?destination=history.php&error=statement_failed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "ss", $checkout_string, $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);

    // Go to checkout page
    header("Location: ../checkout.php");
    exit();
