<?php 

    $json_string = '[{"name":"marlin8","count":0},{"name":"topfuel9.9","count":1},{"name":"topfuel9.8gx","count":2}]';
    $bikes = json_decode($json_string, true);
    
    foreach ($bikes as $key => $bike) {
        if ($bike['count'] == 0) {
            unset($bikes[$key]);
        }
    }

    echo json_encode($bikes);
    // foreach($bikes as $key => $val) {
    //     echo $val["name"];
    // }