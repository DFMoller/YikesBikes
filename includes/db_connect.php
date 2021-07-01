<?php

    $server_username = "reutlingen";
    $servername = "localhost";
    $password = "egPvvV[e05GvYRR3";
    $dbName = "reutlingen";

    $conn = mysqli_connect($servername, $server_username, $password, $dbName);

    if (!$conn)
    {
        // Error
        die("Connection failed: " . mysqli_connect_error()); // end whatever we are doing

    }

