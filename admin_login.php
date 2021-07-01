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
            mysqli_stmt_close($stmt);
            
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