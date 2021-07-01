<?php

    session_start();

    if (!isset($_SESSION['username'])) {
        echo "Not Signed In!";
        exit();
    } else {
        
        if (!isset($_POST['delivery']) || !isset($_POST['grandTotal'])) {
            echo "Parameters not found!";
            exit();
        } else {

            include_once("db_connect.php");

            $delivery_method = $_POST['delivery'];
            $grandTotal = $_POST['grandTotal'];
            $deliveryFee = $_POST['deliveryFee'];

            $sql = "SELECT * FROM carts WHERE user_id = ?";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                echo "Statement Failed!";
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            
            $result = mysqli_stmt_get_result($stmt);
            if (!$row = mysqli_fetch_assoc($result)) {
                echo "No result found!";
                exit();
            } else {

                $checkoutCol = $row["checkout"];
                $checkoutData = json_decode($checkoutCol, true);

                // Update delivery method and grand total
                $checkoutData['delivery'] = $delivery_method;
                $checkoutData['grandTotal'] = $grandTotal;
                $checkoutData['deliveryFee'] = $deliveryFee;

                //Now upload this updated json to DB
                $encoded_checkout = json_encode($checkoutData);
                $sql = "UPDATE carts SET checkout = ? WHERE user_id = ?;";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    echo "Statement Failed!";
                    exit();
                }
                mysqli_stmt_bind_param($stmt, "ss", $encoded_checkout, $_SESSION['user_id']);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);

                echo "Delivery Method and Grand Total uploaded to DB!";
                

            }

        }

    }