<?php

    include_once("db_connect.php");
    $sql = "SELECT * FROM users";
    $result = mysqli_query($conn, $sql);
    $users_online = 0;
    if (mysqli_num_rows($result) > 0) {
        // output data of each row
        $users_online = 0;
        while ($row = mysqli_fetch_assoc($result)) {
            $last_online = $row['last_online'];
            $difference = strtotime(date('Y-m-d H:i:s')) - strtotime($last_online);
            // 5 minutes = 300 sec
            if ($difference < 300) {
                $users_online += 1;
            }
        }
    }
    mysqli_close($conn);

    echo $users_online;