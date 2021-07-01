<?php

    //Import PHPMailer classes into the global namespace
    //These must be at the top of your script, not inside a function
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\SMTP;
    use PHPMailer\PHPMailer\Exception;

    function userExists($conn, $email) {

        $sql = "SELECT * FROM users WHERE email = ?;"; // Prepared statements (?)
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=stmtfailed");
            exit();
        } 

        mysqli_stmt_bind_param($stmt, "s", $email); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // User found
            return $row;
        } else {
            // No such user found
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);

    }

    function checkUsername($conn, $name) {
        $sql = "SELECT * FROM users WHERE username = ?;"; // Prepared statements (?)
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=stmtfailed");
            exit();
        } 

        mysqli_stmt_bind_param($stmt, "s", $name); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // User found
            return $row;
        } else {
            // No such user found
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);
    }

    function createUser($conn, $username, $email, $password) {

        $sql = "INSERT INTO users (username, email, pwd) VALUES (?, ?, ?);"; // Prepared statements (?)
        // prepare statement
        $stmt = mysqli_stmt_init($conn);

        // check if this sql is actually possible within the database
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../signup.php?error=stmtfailed");
            exit();
        }

        // $hashedPwd = hash('sha512', $password);

        mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }

    function randomPassword() {
        $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
        $pass = array(); //remember to declare $pass as an array
        $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
        for ($i = 0; $i < 8; $i++) {
            $n = rand(0, $alphaLength);
            $pass[] = $alphabet[$n];
        }
        return implode($pass); //turn the array into a string
    }

    function confirmationEmail($username, $email, $temp_password) {

        //Load Composer's autoloader
        require 'vendor/autoload.php';

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'kouemoue69@gmail.com';                     //SMTP username
            $mail->Password   = 'appelkooskonfeit';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('kouemoue69@gmail.com', 'Webshop');
            $mail->addAddress($email);    //Add a recipient

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            $message = "
            <p>
            Dear " . $username . ", this is your confirmation email from Webshop.<br><br>
            Your temporary password is: " . $temp_password . "<br><br>
            Please log in using this password and change your password within the next hour.<br>
            Failure to do so will cause your account to be deleted.<br><br>
            Thank you.
            </p>";

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Confirmation Email';
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function updatePassword($conn, $email, $password) {
        $sql = "UPDATE users SET pwd = ?, pwdChanged = ? WHERE email = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../account.php?error=stmtfailed");
            exit();
        }
        $bool = 1;
        mysqli_stmt_bind_param($stmt, "sss", $password, $bool, $email); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    function deleteUser($conn, $email) {
        $sql = "DELETE FROM users WHERE email = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: ../login.php?error=stmtfailed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $email); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }

    function sendResetEmail($email, $username, $link) {
        //Load Composer's autoloader
        require 'vendor/autoload.php';

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'kouemoue69@gmail.com';                     //SMTP username
            $mail->Password   = 'appelkooskonfeit';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('kouemoue69@gmail.com', 'Webshop');
            $mail->addAddress($email);    //Add a recipient

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            $message = "
            <p>
            Dear $username, this is your password reset email from Webshop.<br><br>
            You can reset your password here:<br><br>
            <a href='$link'>$link</a><br><br>
            Please reset your password within the next hour.<br>
            After that, this link will become invalid.<br><br>
            Thank you.
            </p>";

            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Confirmation Email';
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    function store_user_data($screen_res, $conn) {
        $sql = "SELECT * FROM user_sessions WHERE user_id=?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: home.php?error=stmtfailed_select");
            exit();
        } 

        mysqli_stmt_bind_param($stmt, "s", $_SESSION["user_id"]); // if there were two strings, would be "ss": 3 => "sss" etc...
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            
            $sql = "UPDATE user_sessions SET login_time=?, screen_res=?, os=? WHERE user_id=?;";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: home.php?error=stmtfailed_update");
                exit();
            }

            mysqli_stmt_bind_param($stmt, "ssss", date('Y-m-d H:i:s'), $screen_res, getOS(), $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);

        } else {
            // No data exists yet
            $sql = "INSERT INTO user_sessions (user_id, login_time, screen_res, os) VALUES (?, ?, ?, ?);";
            $stmt = mysqli_stmt_init($conn);

            if(!mysqli_stmt_prepare($stmt, $sql)){
                header("Location: home.php?error=stmtfailed_insert");
                exit();
            }
            
            mysqli_stmt_bind_param($stmt, "ssss", $_SESSION['user_id'], date('Y-m-d H:i:s'), $screen_res, getOS());
            mysqli_stmt_execute($stmt);
        }

        mysqli_stmt_close($stmt);
    }

    function getOS() { 

        $user_agent = $_SERVER['HTTP_USER_AGENT'];
    
        $os_platform =   "Bilinmeyen İşletim Sistemi";
        $os_array =   array(
            '/windows nt 10/i'      =>  'Windows 10',
            '/windows nt 6.3/i'     =>  'Windows 8.1',
            '/windows nt 6.2/i'     =>  'Windows 8',
            '/windows nt 6.1/i'     =>  'Windows 7',
            '/windows nt 6.0/i'     =>  'Windows Vista',
            '/windows nt 5.2/i'     =>  'Windows Server 2003/XP x64',
            '/windows nt 5.1/i'     =>  'Windows XP',
            '/windows xp/i'         =>  'Windows XP',
            '/windows nt 5.0/i'     =>  'Windows 2000',
            '/windows me/i'         =>  'Windows ME',
            '/win98/i'              =>  'Windows 98',
            '/win95/i'              =>  'Windows 95',
            '/win16/i'              =>  'Windows 3.11',
            '/macintosh|mac os x/i' =>  'Mac OS X',
            '/mac_powerpc/i'        =>  'Mac OS 9',
            '/linux/i'              =>  'Linux',
            '/ubuntu/i'             =>  'Ubuntu',
            '/iphone/i'             =>  'iPhone',
            '/ipod/i'               =>  'iPod',
            '/ipad/i'               =>  'iPad',
            '/android/i'            =>  'Android',
            '/blackberry/i'         =>  'BlackBerry',
            '/webos/i'              =>  'Mobile'
        );
    
        foreach ( $os_array as $regex => $value ) { 
            if ( preg_match($regex, $user_agent ) ) {
                $os_platform = $value;
            }
        }   
        return $os_platform;
    }

    function getBike($conn, $shortname) {

        $sql = "SELECT * FROM bikes WHERE shortname = ?";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: home.php?error=stmtfailed_getBike");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $shortname);
        mysqli_stmt_execute($stmt);

        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // Bike found
            return $row;
        } else {
            // No such Bike found
            $result = false;
            return $result;
        }

        mysqli_stmt_close($stmt);

    }

    function getBikes($conn) {

        $sql = "SELECT * FROM bikes";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
        // output data of each row
            $bikes = array();
            while($row = $result->fetch_assoc()) {
                $bike = array();
                $bike['shortname'] = $row['shortname'];
                $bike['title'] = $row['title'];
                $bike['stock'] = $row['stock'];
                $bike['price'] = $row['price'];
                $bike['display_price'] = $row['display_price'];
                $bike['image_location'] = $row['image_location'];
                $bike['specifications'] = $row['specifications'];
                array_push($bikes, $bike);
            }
            return $bikes;
        } else {
            return false;
        }
        $conn->close();

    }

    function addToCart($conn, $bike, $code) {

        $sql = "SELECT * FROM carts WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);

        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: products.php?error=stmtfailed_addToCart");
            exit();
        }

        mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        
        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // Cart found -> UPDATE CART
            
            if($items = json_decode($row['items'], true)) {
                $exists = false;
                foreach ($items as $key => $item) {
                    if($item['shortname'] == $bike) {
                        $exists = true;
                        $items[$key]['count'] = $item['count'] + 1;
                    }
                }
                if (!$exists) {
                    $item = array("shortname" => $bike, "count" => 1);
                    array_push($items, $item);
                }
                $updated_json = json_encode($items);

            } else {

                // Cart exists, but items are empty
                $bikes = array();
                $bike = array("shortname" => $bike, "count" => 1);
                array_push($bikes, $bike);
                $updated_json = json_encode($bikes);
            }

            $sql = "UPDATE carts SET items = ?, code = ? WHERE user_id = ?;";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: products.php?error=stmtfailed_addToCart_II");
                exit();
            }
            mysqli_stmt_bind_param($stmt, "sss", $updated_json, $code, $_SESSION['user_id']);
            mysqli_stmt_execute($stmt);
            echo mysqli_error($conn);

            return $updated_json;

        } else {
            // User does not have a cart yet -> CREATE CART
            $bikes = array();
            $bike = array("shortname" => $bike, "count" => 1);
            array_push($bikes, $bike);
            $items = json_encode($bikes);

            $sql = "INSERT INTO carts (user_id, code, items) VALUES (?, ?, ?);";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: products.php?error=stmtfailed_addToCart_III");
                exit();
            }
            mysqli_stmt_bind_param($stmt, "sss", $_SESSION['user_id'], $code, $items);
            mysqli_stmt_execute($stmt);
            
            return $items;
        }

        mysqli_stmt_close($stmt);
        
    }

    function getCart($conn) {

        $sql = "SELECT * FROM carts WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: products.php?error=stmtfailed_getCart");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // Cart found
            return $row;
        } else {
            return false;
        }
        mysqli_stmt_close($stmt);

    }

    function getCartCode($conn) {

        $sql = "SELECT * FROM carts WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if (!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: products.php?error=stmtfailed_getCartCode");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $resultData = mysqli_stmt_get_result($stmt);
        if ($row = mysqli_fetch_assoc($resultData)) {
            // Cart found
            return $row['code'];
        } else {
            return false;
        }
        mysqli_stmt_close($stmt);

    }

    function getUrlParameters($extention) {

        $myArray = str_split($extention);
        $return_string = "";

        $andFlag = false;
        $qFlag = false;
        foreach($myArray as $character){
            if ($andFlag) {
                $return_string .= $character;
            }
            if ($character == "&" && !$qFlag){
                $andFlag = true;
                $qFlag = true;
                $return_string .= "?";
            }
        }
        
        return $return_string;

    }

    function paymentConfirmationEmail($username, $email, $checkout_data) {

        //Load Composer's autoloader
        require 'vendor/autoload.php';

        //Instantiation and passing `true` enables exceptions
        $mail = new PHPMailer(true);

        try {
            //Server settings
            // $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                            //Send using SMTP
            $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
            $mail->Username   = 'contact.yikesbikes@gmail.com';                     //SMTP username
            $mail->Password   = 'yikesbikes2021';                               //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;         //Enable TLS encryption; `PHPMailer::ENCRYPTION_SMTPS` encouraged
            $mail->Port       = 587;                                    //TCP port to connect to, use 465 for `PHPMailer::ENCRYPTION_SMTPS` above

            //Recipients
            $mail->setFrom('contact.yikesbikes@gmail.com', 'Webshop');
            $mail->addAddress($email);    //Add a recipient

            //Attachments
            // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            $message = "
            <strong>Dear " . $username . ", this is your payment confirmation email from Webshop.</strong>
            <p>
            Your payment was successful and you have purchased the following items:<br>
            <ul>";

            $rows = $checkout_data['rows'];

            foreach($rows as $row) {
                $count = $row['count'];
                $title = $row['title'];
                $total = $row['total'];
                
                $message .= "<li>$count x $title: R $total</li>";
                // print_r($row);
                // print_r(json_decode($row, true));
            }

            $subtotal = $checkout_data['bigTotal'];
            $deliveryFee = $checkout_data['deliveryFee'];
            $grandTotal = $checkout_data['grandTotal'];

            $message .= "
            </ul>
            <strong>Order Summary</strong><br>
            Sub Total: R $subtotal <br>
            Shipping: R $deliveryFee <br>
            Grand Total: R $grandTotal <br><br>

            Thank you for supporting YikesBikes!<br><br>

            Kind regards,<br><br>

            The YikesBikes Team<br><br>
            <p>";


            //Content
            $mail->isHTML(true);                                  //Set email format to HTML
            $mail->Subject = 'Paymen Confirmation';
            $mail->Body    = $message;
            $mail->AltBody = strip_tags($message);

            $mail->send();
            // echo 'Message has been sent';
        } catch (Exception $e) {
            header("Location: products.php?error=mail_failed&mailerror=$mail->ErrorInfo");
            // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
            exit();
        }
    }

    function subtractBikes($checkout_data) {
        
        include("includes/db_connect.php");
        $rows = $checkout_data['rows'];

        foreach($rows as $row) {

            $sql = "SELECT * FROM bikes WHERE shortname = ?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: redirect.php?destination=products.php&error=updatebikes_statement_failed");
                exit();
            }
            $shortname = $row['shortname'];
            mysqli_stmt_bind_param($stmt, "s", $shortname);
            mysqli_stmt_execute($stmt);

            $result = mysqli_stmt_get_result($stmt);
            if(!$bike = mysqli_fetch_assoc($result)) {
                header("Location: redirect.php?destination=products.php&error=updatebikes_result_failed");
                exit();
            }
            mysqli_stmt_close($stmt);
            $oldStock = $bike['stock'];
            $newStock = $oldStock - $row['count'];

            $sql = "UPDATE bikes SET stock = ? WHERE shortname = ?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: redirect.php?destination=products.php&error=updatebikes_upload_statement_failed");
                exit();
            }
            mysqli_stmt_bind_param($stmt, "ss", $newStock, $shortname);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);

        }
        

    }

    function addHistory($user_id, $checkout_data) {

        include("includes/db_connect.php");

        if ($cart = getCart($conn)) {
            $history = json_decode($cart['history'], true);
        } else {
            header("Location: redirect.php?destination=products.php&error=addHistory_statement_failed");
            exit();
        }

        $timestamp = date('Y-m-d H:i:s');

        $instance = $checkout_data;

        print_r($instance);

        if(!$history) {
            $history = array($timestamp => $instance);
        } else {
            $history[$timestamp] = $instance;
        }

        $newHistory = json_encode($history);

        $sql = "UPDATE carts SET history = ? WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: redirect.php?destination=products.php&error=addHistory_statement_failed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "ss", $newHistory, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }

    function clearCart($user_id) {

        include("includes/db_connect.php");
        $sql = "UPDATE carts SET items = ?, checkout = ? WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: redirect.php?destination=products.php&error=clearCart_statement_failed");
            exit();
        }
        $items = $checkout = null;
        mysqli_stmt_bind_param($stmt, "sss", $items, $checkout, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

    }

    function makePayment($username, $user_id, $email) {

        include_once("includes/db_connect.php");
        $sql = "SELECT * FROM carts WHERE user_id = ?;";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: redirect.php?destination=products.php&error=statement_failed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$cart = mysqli_fetch_assoc($result)) {
            header("Location: products.php?error=payment_statement_failed");
            exit();
        } else {
            $checkout_col = $cart['checkout'];
            $checkout_data = json_decode($checkout_col, true);
        }
        mysqli_stmt_close($stmt);
        paymentConfirmationEmail($username, $email, $checkout_data);
        subtractBikes($checkout_data);
        addHistory($user_id, $checkout_data);
        clearCart($user_id);
        echo "Payment successful!";

    }

    function getPurchaseHistory($user_id) {

        include("includes/db_connect.php");

        $sql = "SELECT * FROM carts WHERE user_id = ?";
        $stmt = mysqli_stmt_init($conn);
        if(!mysqli_stmt_prepare($stmt, $sql)) {
            header("Location: redirect.php?destination=products.php&error=getHistory_statement_failed");
            exit();
        }
        mysqli_stmt_bind_param($stmt, "s", $user_id);
        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);
        if (!$cart = mysqli_fetch_assoc($result)) {
            return false;
        } else {
            $history_col = $cart['history'];
            $history = json_decode($history_col, true);
            return $history;
        }
        mysqli_stmt_close($stmt);

    }

    ?>