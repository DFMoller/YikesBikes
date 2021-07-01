<?php

    date_default_timezone_set('Africa/Johannesburg');
    echo time() . "<br>";
    echo "Formatted: " . date('d/m/y G:i:s', time()) . "<br>";