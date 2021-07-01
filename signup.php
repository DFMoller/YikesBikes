<?php

    $errors = array('username' => '', 'email' => '');
    $feedback = array('username' => '', 'email' => '');
    $name = $email = '';
    $flag = 0;

    if(isset($_POST['submit']))
    {

        include_once("includes/functions.php");

        // check username
        if(empty($_POST['username'])){
            $errors['username'] = "Please enter a User Name!";
        } else {
            $name = $_POST['username'];
            if(strlen($name) < 2){
                $errors['username'] = 'User Name is too short!';
            } elseif (!preg_match("/^[a-zA-Z\s]*$/", $name)) {
                $errors['username'] = 'Only letters and spaces are accepted!';
            }
        }

        // check email
        if(empty($_POST['email'])){
            $errors['email'] = "Please enter your email!";
        } else {
            $email = $_POST['email'];
            if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
                $errors['email'] = 'Invalid Email!';
            }
        }

        if(!array_filter($errors))
        {
            include('includes/db_connect.php');
            if (userExists($conn, $email)) {
                // User found in db
                $errors['email'] = 'A user with that email already exists!';
            } elseif (checkUsername($conn, $name)) {
                $errors['username'] = 'A user with that Nickname already exists!';
            } else {
                // No user found. Create new user

                // Create temporary password:
                $temp_password = randomPassword();

                createUser($conn, $name, $email, $temp_password);
                confirmationEmail($name, $email, $temp_password);
                $flag = 1;
            }

        }
        
        foreach ($errors as $key => $e) {
            if (strlen($e) > 0) {
                $feedback[$key] = 'is-invalid';
            } else {
                $feedback[$key] = 'is-valid';
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?> 

<body class="auth-body bg-light">

    <section class="auth-section">

        <h4 class="m-3">Sign Up</h4>

        <form class="auth-form" method="POST" action="<?php $_SERVER['PHP_SELF']; ?>">
            <div class="mb-3">
                <label for="inputName" class="form-label">Nickname</label>
                <input type="text" name="username" oninput="ajaxUsername();" class="form-control <?php echo $feedback['username'] ?>" value="<?php echo $name ?>" id="inputName" aria-describedby="nameFeedback">
                <div id="nameFeedback" class="invalid-feedback"><?php echo $errors['username'] ?></div>
            </div>
            <div class="mb-3">
                <label for="inputEmail" class="form-label">Email address</label>
                <input type="email" name="email" oninput="ajaxEmail();" class="form-control <?php echo $feedback['email'] ?>" value="<?php echo $email ?>" id="inputEmail" aria-describedby="emailFeedback">
                <div id="emailFeedback" class="invalid-feedback"><?php echo $errors['email'] ?></div>
            </div>
            <div class="mb-3">
                <a href="login.php">Already have an account? Sign in here</a>
            </div>
            <?php
                if ($flag === 1) {
                    echo '
                        <p>You will receive an email with your temporary password. Use it to <a href="login.php">Log In</a>.</p>
                    ';
                }
            ?>
            <button type="submit" name="submit" class="btn btn-primary">Sign Up</button>
        </form>

    </section>

    <script>
    
        function ajaxUsername() {
            
            var nameInput = document.getElementById("inputName");
            var name = nameInput.value;
            var nameFeedback = document.getElementById("nameFeedback");

            console.log(name);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    console.log(result);

                    if (result == 1) {
                        nameInput.classList.add("is-invalid");
                        nameFeedback.innerHTML = "That username is taken."
                        console.log("added");
                    } else if (result == 0) {
                        nameInput.classList.remove("is-invalid");
                        console.log("removed");
                    }
                }
            };
            xmlhttp.open("GET","includes/checkUsernameAjax.inc.php?name=" + name, true);
            xmlhttp.send();

        }

        function ajaxEmail() {
            
            var emailInput = document.getElementById("inputEmail");
            var email = emailInput.value;
            var emailFeedback = document.getElementById("emailFeedback");

            console.log(email);

            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    // console.log(result);

                    if (result == 1) {
                        emailInput.classList.add("is-invalid");
                        emailFeedback.innerHTML = "There is already an account using that email."
                        console.log("added");
                    } else if (result == 0) {
                        emailInput.classList.remove("is-invalid");
                        console.log("removed");
                    }
                }
            };
            xmlhttp.open("GET","includes/checkEmailAjax.inc.php?email=" + email, true);
            xmlhttp.send();

        }

    </script>
    
</body>
</html>