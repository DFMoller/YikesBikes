<?php

    $errors = array('email' => '', 'pwd' => '');
    $feedback = array('email' => '', 'pwd' => '');
    $email = $pwd = '';

    if(isset($_POST['submit']))
    {

        include_once("includes/functions.php");

        // check email
        if(empty($_POST['email'])){
            $errors['email'] = "Please enter your email!";
        } else {
            $email = $_POST['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors['email'] = 'Invalid Email!';
            }
        }

        // Check Password
        if(empty($_POST['pwd'])){
            $errors['pwd'] = "Please enter a password!";
            $feedback['pwd'] = "is-invalid";
        } else {
            $pwd = $_POST['pwd'];  
        }

        if(!array_filter($errors))
        {

            // Log in the user:
            include('includes/db_connect.php');
            $userExists = userExists($conn, $email);
            if ($userExists === false) {
                $errors['email'] = "No user with that email!";
                $feedback['email'] = "is-invalid";
            } else {

                // $feedback['email'] = "is-valid";
                $dbPwd = $userExists['pwd'];
                if ($userExists['pwdChanged'] == true) {
                    // The user has already changed his/her password
                    $verify = hash('sha512', $_POST['pwd']);
                } elseif ($userExists['pwdChanged'] == false) {
                    // This is the user's default (temporary) password
                    $verify = $_POST['pwd'];
                } else {
                    $errors['pwd'] = "Password matching error in DB";
                    $feedback['pwd'] = "is-invalid";
                }
                $result = ($dbPwd === $verify);
                if ($result === false) {
                    $errors['pwd'] = 'Incorrect Password!';
                    $feedback['pwd'] = "is-invalid";
                } elseif ($result === true) {

                    // If user is using default password after 24 hours, delete account
                    $joinTime = strtotime($userExists['joined']);
                    // echo date('d/m/y G:i:s', time());
                    if ($userExists['pwdChanged'] == false && (time() - $joinTime) > 3600) {
                        // Delete User
                        $errors['pwd'] = "You have used your temporary password for more than 1 hour! Therefore, your account has been deleted. Please create a <a href='signup.php'>new account</a>.";
                        $feedback['pwd'] = "is-invalid";
                        deleteUser($conn, $userExists['email']);
                    } elseif ($userExists['pwdChanged'] == false && (time() - $joinTime) <= 3600) {
                        // Log in the user with temp password
                        session_start();
                        $_SESSION['username'] = $userExists['username'];
                        $_SESSION['email'] = $userExists['email'];
                        $_SESSION['user_id'] = $userExists['id'];
                        $screen_res = $_POST['screen-width'] . "x" . $_POST['screen-height'];
                        store_user_data($screen_res, $conn);
                        header("Location: redirect.php?destination=changepwd.php");
                        exit();
                    } else {
                        // Log in the user with correct password
                        session_start();
                        $_SESSION['username'] = $userExists['username'];
                        $_SESSION['email'] = $userExists['email'];
                        $_SESSION['user_id'] = $userExists['id'];
                        $_SESSION['last_online'] = getLastOnline($conn, $_SESSION['user_id']);
                        $screen_res = $_POST['screen-width'] . "x" . $_POST['screen-height'];
                        store_user_data($screen_res, $conn);
                        header("Location: redirect.php?destination=home.php&welcome=true");
                        exit();
                    }
                }
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?> 

<body class="auth-body">

    <section class="auth-section">

        <h4>Log In</h4>

        <form class="auth-form" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            
            <div class="mb-3">
                <label for="inputEmail" class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?php echo $feedback['email'] ?>" value="<?php echo $email ?>" id="inputEmail" aria-describedby="emailFeedback">
                <div id="emailFeedback" class="invalid-feedback"><?php echo $errors['email'] ?></div>
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password</label>
                <input type="password" name="pwd" class="form-control <?php echo $feedback['pwd'] ?>" value="<?php echo $pwd ?>" id="pwd" aria-describedby="pwdFeedback">
                <div id="pwdFeedback" class="invalid-feedback"><?php echo $errors['pwd'] ?></div>
            </div>

            <input type="hidden" name="screen-width" id="screen-width" value="screen.width">
            <input type="hidden" name="screen-height" id="screen-height" value="window.height">
            
            <div class="mb-1">
                <a href="signup.php">New User? Create an account</a>
            </div>

            <div class="mb-1">
                <a href="reset_password.php">Forgot Password</a>
            </div>

            <div class="mb-3">
                <a href="admin_login.php">Admin Login</a>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Log In</button>
            
        </form>

        <script>
        
            document.addEventListener("DOMContentLoaded", () => {
                document.getElementById("screen-width").value = screen.width;
                document.getElementById("screen-height").value = screen.height;
            })

        </script>

    </section>
    
</body>
</html>