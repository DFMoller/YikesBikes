<?php

    $errors = array('username' => '', 'pwd' => '');
    $feedback = array('username' => '', 'pwd' => '');
    $username = $pwd = '';

    if(isset($_POST['submit']))
    {

        include_once("includes/functions.php");

        // check username
        if(empty($_POST['username'])){
            $errors['username'] = "Please enter your username!";
            $feedback['username'] = "is-invalid";
        } else {
            $username = $_POST['username'];
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

            // Log in the admin user:
            include('includes/db_connect.php');

            $hashed_username = hash('sha512', $username);

            $sql = "SELECT * FROM admin_users WHERE username = ?";
            $stmt = mysqli_stmt_init($conn);
            if(!mysqli_stmt_prepare($stmt, $sql)) {
                header("Location: admin_login.php?error=statement_failed");
                exit();
            }
            mysqli_stmt_bind_param($stmt, "s", $hashed_username);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            if(!$row = mysqli_fetch_assoc($result)) {
                $errors['username'] = "Incorrect Username";
                $feedback['username'] ="is-invalid";
            } else {

                $hashed_pwd = hash("sha512", $pwd);

                if ($hashed_pwd != $row['password']) {
                    $errors['pwd'] = "Incorrect Password";
                    $feedback['pwd'] = "is-invalid";
                } else if ($hashed_pwd == $row['password']) {
                    
                    // Sign in the user
                    session_start();
                    $_SESSION['username'] = "admin";
                    $_SESSION['email'] = "admin";
                    $_SESSION['user_id'] = "admin";
                    $_SESSION['admin'] = true;
                    header("Location: redirect.php?destination=home.php");
                    exit();

                }

            }




            // $userExists = userExists($conn, $email);
            // if ($userExists === false) {
            //     $errors['email'] = "No user with that email!";
            //     $feedback['email'] = "is-invalid";
            // } else {

            //     // $feedback['email'] = "is-valid";
            //     $dbPwd = $userExists['pwd'];
            //     if ($userExists['pwdChanged'] == true) {
            //         // The user has already changed his/her password
            //         $verify = hash('sha512', $_POST['pwd']);
            //     } elseif ($userExists['pwdChanged'] == false) {
            //         // This is the user's default (temporary) password
            //         $verify = $_POST['pwd'];
            //     } else {
            //         $errors['pwd'] = "Password matching error in DB";
            //         $feedback['pwd'] = "is-invalid";
            //     }
            //     $result = ($dbPwd === $verify);
            //     if ($result === false) {
            //         $errors['pwd'] = 'Incorrect Password!';
            //         $feedback['pwd'] = "is-invalid";
            //     } elseif ($result === true) {

            //         // If user is using default password after 24 hours, delete account
            //         $joinTime = strtotime($userExists['joined']);
            //         // echo date('d/m/y G:i:s', time());
            //         if ($userExists['pwdChanged'] == false && (time() - $joinTime) > 3600) {
            //             // Delete User
            //             $errors['pwd'] = "You have used your temporary password for more than 1 hour! Therefore, your account has been deleted. Please create a <a href='signup.php'>new account</a>.";
            //             $feedback['pwd'] = "is-invalid";
            //             deleteUser($conn, $userExists['email']);
            //         } elseif ($userExists['pwdChanged'] == false && (time() - $joinTime) <= 3600) {
            //             // Log in the user with temp password
            //             session_start();
            //             $_SESSION['username'] = $userExists['username'];
            //             $_SESSION['email'] = $userExists['email'];
            //             $_SESSION['user_id'] = $userExists['id'];
            //             $screen_res = $_POST['screen-width'] . "x" . $_POST['screen-height'];
            //             store_user_data($screen_res, $conn);
            //             header("Location: redirect.php?destination=changepwd.php");
            //             exit();
            //         } else {
            //             // Log in the user with correct password
            //             session_start();
            //             $_SESSION['username'] = $userExists['username'];
            //             $_SESSION['email'] = $userExists['email'];
            //             $_SESSION['user_id'] = $userExists['id'];
            //             $screen_res = $_POST['screen-width'] . "x" . $_POST['screen-height'];
            //             store_user_data($screen_res, $conn);
            //             header("Location: redirect.php?destination=home.php");
            //             exit();
            //         }
            //     }
            // }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?> 

<body class="auth-body">

    <section class="auth-section">

        <h4>Are You an Admin User?</h4>

        <form class="auth-form" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            
            <div class="mb-3">
                <label for="inputusername" class="form-label">Username</label>
                <input type="text" name="username" class="form-control <?php echo $feedback['username'] ?>" value="<?php echo $username ?>" id="inputusername" aria-describedby="usernameFeedback">
                <div id="usernameFeedback" class="invalid-feedback"><?php echo $errors['username'] ?></div>
            </div>
            <div class="mb-3">
                <label for="pwd" class="form-label">Password</label>
                <input type="password" name="pwd" class="form-control <?php echo $feedback['pwd'] ?>" value="<?php echo $pwd ?>" id="pwd" aria-describedby="pwdFeedback">
                <div id="pwdFeedback" class="invalid-feedback"><?php echo $errors['pwd'] ?></div>
            </div>
            
            <div class="mb-3">
                <a href="login.php">Not an Admin User? Go Back to Login</a>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">Log In as Admin</button>
            
        </form>

        <script>

        </script>

    </section>
    
</body>
</html>