<?php

    if (!isset($_GET['search'])) {
        echo "Parameter not found!";
        exit();
    }

    include("db_connect.php");
    include("functions.php");

    $search = strtolower($_GET['search']);

    if (!$bikes = getBikes($conn)) {
        echo "No bikes found";
        exit();
    }

    $filtered_bikes = array();
    // print_r($bikes);

    foreach($bikes as $key => $bike){
        $shortname = $bike['shortname'];
        $title = $bike['title'];
        $condition1 = strpos($shortname, $search) !== false;
        $condition2 = strpos($title, $search) !== false;
        $condition3 = strpos($shortname, str_replace(' ', '', $search)) !== false;
        if ($condition1 || $condition2 || $condition3) {
            array_push($filtered_bikes, $bike);
        }
    }

    $return_string = "";

    foreach($filtered_bikes as $bike) {
        $return_string .= "<a href='redirect.php?destination=details.php?bike={$bike['shortname']}' class='item-link'>";
        $return_string .= "<div class='item'>";
        $return_string .= "<h4>{$bike['title']}</h4><img src='static/bikes/{$bike['image_location']}' alt='Bike'></div></a>";
    }

    echo $return_string;

    

