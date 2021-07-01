<?php
    
    session_start();

    $errors = array('pwd1' => '', 'pwd2' => '');
    $feedback = array('pwd1' => '', 'pwd2' => '');
    $pwd1 = $pwd2 = '';

    if (isset($_POST['submit'])) {
        // Check Password
        if(empty($_POST['pwd1'])){
            $errors['pwd1'] = "Please enter a new password!";
            $feedback['pwd1'] = "is-invalid";
        } elseif (empty($_POST['pwd2'])) {
            $errors['pwd2'] = "Please enter a new password!";
            $feedback['pwd2'] = "is-invalid";
        } else {

            $pwd1 = $_POST['pwd1'];
            $pwd2 = $_POST['pwd2'];

            if (strlen($pwd1) < 8) {
                $errors['pwd1'] = 'Password needs to be at least 8 characters!';
                $feedback['pwd2'] = 'is-invalid';
            } elseif ($pwd1 !== $pwd2) {
                $errors['newPwd'] = 'Passwords do not match!';
                $feedback['newPwd'] = 'is-invalid';
            }

            if (!array_filter($errors)) {
                include('includes/db_connect.php');
                include('includes/functions.php');
                $user = userExists($conn, $_SESSION['email']);
                if (!$user) {
                    // User not found
                    $errors['pwd1'] = "User not found in the database. Please try again later.";
                    $feedback['pwd1'] = "is-invalid";
                } elseif ($user) {

                    // Create hashed password:
                    $hashedPwd = hash('sha512', $pwd1);

                    // Update user password
                    updatePassword($conn, $_SESSION['email'], $hashedPwd);
                    header("Location: redirect.php?destination=home.php");
                    exit();

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

    <main>
        <div class="changepwd-wrapper">
            <h4 class="m-3">Create a New Password</h4>
            <form class="auth-form" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
                <p>Every new user must create a personal password on his/her first login.</p>
                <div class="mb-3">
                    <label for="pwd1" class="form-label">New Password</label>
                    <input type="password" name="pwd1" class="form-control <?php echo $feedback["pwd1"]; ?>" value="<?php echo $pwd1; ?>" id="pwd1" aria-describedby="pwd1Feedback">
                    <div id="pwd1Feedback" class="invalid-feedback"><?php echo $errors['pwd2']; ?></div>
                </div>
                <div class="mb-3">
                    <label for="pwd2" class="form-label">Confirm New Password</label>
                    <input type="password" name="pwd2" class="form-control <?php echo $feedback["pwd2"]; ?>" value="<?php echo $pwd2; ?>" id="pwd2" aria-describedby="pwd2Feedback">
                    <div id="pwd2Feedback" class="invalid-feedback"><?php echo $errors['pwd2']; ?></div>
                </div>
                <button type="submit" name="submit" class="btn btn-primary">Confirm</button>
            </form>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

    </script>
    
</body>
</html>