<?php

    header("Location: products.php");
    exit();

    // include_once("includes/db_connect.php");

    // // Read JSON file
    // $json = file_get_contents('static/bikes/data.json');
        
    // //Decode JSON
    // $json_data = json_decode($json,true);

    // $title = $price = $display_price = $img_location = '';

    // foreach($json_data as $shortname => $array) {
        
    //     $title = $array['title'];
    //     $price = $array['price'];
    //     $display_price = $array['display_price'];
    //     $img_location = $array['img'];
    //     $specifications = $array['specifications'];
        
    //     $sql = "INSERT INTO bikes (shortname, title, stock, price, display_price, image_location, specifications) VALUES (?, ?, ?, ?, ?, ?, ?);";
    //     $stmt = mysqli_stmt_init($conn);
    //     if (!mysqli_stmt_prepare($stmt, $sql)) {
    //         echo "Error! Statement Failed...";
    //         exit();
    //     }

    //     $specifications_string = json_encode($specifications);
    //     $stock = 3;
        
    //     mysqli_stmt_bind_param($stmt, "sssssss", $shortname, $title, $stock, $price, $display_price, $img_location, $specifications_string);
    //     mysqli_stmt_execute($stmt);
    //     mysqli_stmt_close($stmt);
    // }

    // echo $specifications;
    // echo json_encode($specifications);
    // echo $json;

    // echo $bikename;
    // echo " ";
    // echo $title;
    // echo " ";
    // echo $price;
    // echo " ";
    // echo $display_price;
    // echo " ";
    // echo $img_location;
    // echo " ";
    // echo json_encode($specifications);
    // echo " ";

    