<?php
    
    session_start();

    $errors = array('email' => '');
    $feedback = array('email' => '');
    $returnMessage = '';

    if (isset($_POST['submit'])) {
        
        // Tokens
        $selector = bin2hex(random_bytes(8));
        $token = random_bytes(32);
        
        $url = "localhost/reutlingen/webshop/create_new_password.php?selector=$selector&validator=" . bin2hex($token);

        // Create Expiry Date
        $expires = date("U") + 1800;

        require 'includes/db_connect.php';

        $userEmail = $_POST['email'];

        if (empty($userEmail)) {
            $errors['email'] = "Please Enter an email!";
            $feedback['email'] = "is-invalid";
        } else {
            // Correct email was entered.
            // Delete previous tokens belonging to this specific user
            $sql = "DELETE FROM pwdreset WHERE pwdResetEmail = ?;";
            $stmt = mysqli_stmt_init($conn);
            if (!mysqli_stmt_prepare($stmt, $sql)) {

                $errors['email'] = "Error in preparing statement. Please try again later";
                $feedback['email'] = "is-invalid";

            } else {

                mysqli_stmt_bind_param($stmt, 's', $userEmail);
                mysqli_stmt_execute($stmt);
                
                $sql = "INSERT INTO pwdreset (pwdResetEmail, pwdResetSelector, pwdResetToken, pwdResetExpires) VALUES (?, ?, ?, ?);";
                $stmt = mysqli_stmt_init($conn);
                if (!mysqli_stmt_prepare($stmt, $sql)) {
                    $errors['email'] = "Error in preparing statement. Please try again later";
                    $feedback['email'] = "is-invalid";
                } else {

                    $hashedToken = hash('sha512', $token);

                    mysqli_stmt_bind_param($stmt, 'ssss', $userEmail, $selector, $hashedToken, $expires);
                    mysqli_stmt_execute($stmt);
                }

            }
            mysqli_stmt_close($stmt);
            mysqli_close($conn);

            include_once "includes/functions.php";
            sendResetEmail($userEmail, "user", $url);
            $returnMessage = "Email Sent. Check your inbox.";
        }
        

    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body class="bg-light">

    <?php include('templates/navigation.php'); ?>

    <main class="changepwd-main">
        <h4 class="m-3 mt-5">Reset Your Password</h4>
        <p>You will receive an email containing instructions on how to reset your password.</p>
        <form class="auth-form" method="POST" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
            <div class="mb-3">
                <label for="inputName" class="form-label">Email</label>
                <input type="email" name="email" class="form-control <?php echo $feedback['email'] ?>" placeholder="Enter Your Email..." id="inputName" aria-describedby="nameFeedback">
                <div id="nameFeedback" class="invalid-feedback"><?php echo $errors['email'] ?></div>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Send Email</button>
            <?php if ($returnMessage != '') {
                echo "<p>$returnMessage</p>";
            } ?>
        </form>
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

    </script>
    
</body>
</html>