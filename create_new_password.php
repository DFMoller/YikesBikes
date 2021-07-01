<?php

    session_start();

    $errors = array("pwd1" => "", "pwd2" => "");
    $feedback = array("pwd1" => "", "pwd2" => "");

    if (isset($_POST['submit'])) {

        $selector = $_POST['selector'];
        $validator = $_POST['validator'];
        $pwd1 = $_POST['pwd1'];
        $pwd2 = $_POST['pwd2'];

        // Check Password 1
        if(empty($pwd1)){
            $errors['pwd1'] = "Please enter a new password!";
            $feedback['pwd1'] = "is-invalid";
        }

        // Check Password 1
        if(empty($pwd2)){
            $errors['pwd2'] = "Please confirm your new password!";
            $feedback['pwd2'] = "is-invalid";
        } elseif ($pwd1 !== $pwd2) {
            $errors['pwd2'] = "Passwords do not match!";
            $feedback['pwd2'] = "is-invalid";
        }

        if(!array_filter($errors)) {

            // Check token in db
            $currentDate = Date("U");
            include_once "includes/db_connect.php";
            $sql = "SELECT * FROM pwdreset WHERE pwdResetSelector=? AND pwdResetExpires >= ?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {

                $errors['pwd1'] = "Error in preparing statement. Please try again later";
                $feedback['pwd1'] = "is-invalid";

            } else {

                mysqli_stmt_bind_param($stmt, 'ss', $selector, $currentDate);
                mysqli_stmt_execute($stmt);

                $result = mysqli_stmt_get_result($stmt);
                if (!$row = mysqli_fetch_assoc($result)) {
                    $errors['pwd1'] = "Please re-submit your password reset request. Your reset link has expired";
                    $feedback['pwd1'] = "is-invalid";
                } else {
                    $binToken = hex2bin($validator);
                    if ($row["pwdResetToken"] !== hash('sha512', $binToken)) {
                        $errors['pwd1'] = "Tokens do not match!";
                        $feedback['pwd1'] = "is-invalid";
                    } elseif ($row["pwdResetToken"] === hash('sha512', $binToken)) {
                        
                        // Find user and update his/her password
                        include_once "includes/functions.php";
                        $tokenEmail = $row['pwdResetEmail'];
                        updatePassword($conn, $tokenEmail, hash('sha512', $pwd1));
                        header("Location: home.php");

                    }
                }
            }

        }

    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body class="bg-light">

    <?php include('templates/navigation.php'); ?>

    <main class="changepwd-main">

        <?php 

            if (isset($_GET['selector']) && isset($_GET['validator'])) {
                $selector = $_GET['selector'];
                $validator = $_GET['validator'];
            } elseif (isset($_POST['selector']) && isset($_POST['validator'])) {
                $selector = $_POST['selector'];
                $validator = $_POST['validator'];
            }
            if (empty($selector) || empty($validator)) {
                echo "Could not validate your request!";
            } else {
                if (ctype_xdigit($selector) !== false && ctype_xdigit($validator) !== false) {
                    // If these two are in valid hex format
                    ?>

                    <form class="auth-form" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                        
                        <input type="hidden" name="selector" value="<?php echo $selector; ?>">
                        <input type="hidden" name="validator" value="<?php echo $validator; ?>">
                        
                        <div class="mb-3">
                            <label for="pwd1" class="form-label">New Password</label>
                            <input type="password" name="pwd1" class="form-control <?php echo $feedback['pwd1'] ?>" placeholder="Enter a new Password" id="pwd1" aria-describedby="pwd1Feedback">
                            <div id="pwd1Feedback" class="invalid-feedback"><?php echo $errors['pwd1'] ?></div>
                        </div>
                        <div class="mb-3">
                            <label for="pwd2" class="form-label">New Password</label>
                            <input type="password" name="pwd2" class="form-control <?php echo $feedback['pwd2'] ?>" placeholder="Confirm your new Password" id="pwd" aria-describedby="pwd2Feedback">
                            <div id="pwd2Feedback" class="invalid-feedback"><?php echo $errors['pwd2'] ?></div>
                        </div>
                        <button type="submit" name="submit" class="btn btn-primary">Reset Password</button>
                    </form>

                    <?php
                }
            }

            ?>
        

        
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

    </script>
    
</body>
</html>