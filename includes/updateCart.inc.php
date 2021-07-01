<?php

    session_start();

    if (!isset($_SESSION['username'])) {
        // User not signed in
        echo "Not Signed In!";
        exit();
    }

    if (isset($_POST['cart']) && isset($_POST['checkout'])) {

        include_once("db_connect.php");

        $bikes = $_POST['cart'];
        $checkout = $_POST['checkout'];

        // Check if any bikes are 0:
        $decoded_bikes = json_decode($bikes, true);
        foreach ($decoded_bikes as $key => $bike) {
            if ($bike['count'] == 0) {
                unset($decoded_bikes[$key]);
            }
        }
        $bikes = json_encode($decoded_bikes);

        $sql = "UPDATE carts SET items = ? WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo false;
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $bikes, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // **************** SECTION 2 ******************


        
        $sql = "UPDATE carts SET checkout = ? WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            echo "Error when updating checkout!";
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $checkout, $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        echo "Cart and Checkout Updated!";

    } else {
        echo "Parameters not Found!";
    }