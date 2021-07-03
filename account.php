<?php
    
    session_start();

    if(!isset($_SESSION['username'])) {
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    // Errors from other pages
    if (isset($_GET['error'])) {
        echo "<p class='red-alert alert'>Error: " . $_GET['error'] . "</p>";
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main>
        <!-- <h3>My Account</h3> -->
        <div class="account-wrapper">
            <svg class="pfp mb-3" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"><path d="M12 0c-6.627 0-12 5.373-12 12s5.373 12 12 12 12-5.373 12-12-5.373-12-12-12zm7.753 18.305c-.261-.586-.789-.991-1.871-1.241-2.293-.529-4.428-.993-3.393-2.945 3.145-5.942.833-9.119-2.489-9.119-3.388 0-5.644 3.299-2.489 9.119 1.066 1.964-1.148 2.427-3.393 2.945-1.084.25-1.608.658-1.867 1.246-1.405-1.723-2.251-3.919-2.251-6.31 0-5.514 4.486-10 10-10s10 4.486 10 10c0 2.389-.845 4.583-2.247 6.305z"/></svg>
            <h1>My Account</h1>
            <ul class="account-details">
                <li><strong>Nickname: </strong><?php echo $_SESSION['username']; ?></li>
                <li><strong>Email: </strong><?php echo $_SESSION['email']; ?></li>
            </ul>
            <h4 class="mt-4 mb-4">Useful Links</h4>
            <ul>
                <li class="mb-1"><a href="redirect.php?destination=history.php">Purchase History</a></li>
                <li><a href="redirect.php?destination=reset_password.php">Reset Password</a></li>
            </ul>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>
    
</body>
</html>