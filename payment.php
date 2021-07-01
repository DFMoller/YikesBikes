<?php

    session_start();

    if (!isset($_SESSION['username'])) {
        header("Location: redirect.php?destination=products.php&error=not_signed_in");
        exit();
    }

    $errors = array("firstname" => "", "lastname" => "", "email" => "", "address" => "", "city" => "", "zip" => "", "cardname" => "", "cardnumber" => "", "cvv" => "");
    $feedback = array("firstname" => "", "lastname" => "", "email" => "", "address" => "", "city" => "", "zip" => "", "cardname" => "", "cardnumber" => "", "cvv" => "");
    $errorBikes = array();

    $firstname = $lastname = $email = $address = $city = $country_code = $zipcode = $cardname = $cardnumber = $expireyear = $expiremonth = $cvv = $stockError = "";

    if (isset($_POST['submit'])) {

        // Form submitted
        
        $firstname = $_POST['firstname'];
        $lastname = $_POST['lastname'];
        $email = $_POST['email']; // checked in form
        $address = $_POST['address'];
        $country_code = $_POST['country']; // checked in form
        $zipcode = $_POST['zip'];
        $cardname = $_POST['cardname'];
        $cardnumber = $_POST['cardnumber'];
        $expireyear = $_POST['expireyear']; // handled in form
        $expiremonth = $_POST['expiremonth']; // handled in form
        $cvv = $_POST['cvv'];
        $city = $_POST['city'];

        if (strlen($firstname) < 2) {
            $errors['firstname'] = "Must be more than 1 Character";
            $feedback['firstname'] = "is-invalid";
        }

        if (strlen($lastname) < 2) {
            $errors['lastname'] = "Must be more than 1 Character";
            $feedback['lastname'] = "is-invalid";
        }

        if ($email != $_SESSION['email']) {
            $errors['email'] = "Please enter the email you used to sign up with YikesBikes";
            $feedback['email'] = "is-invalid";
        }

        if (strlen($address) < 5) {
            $errors['address'] = "Must be more than 4 Characters";
            $feedback['address'] = "is-invalid";
        }

        if (strlen($city) < 2) {
            $errors['city'] = "Must be more than 1 Character";
            $feedback['city'] = "is-invalid";
        }

        if(strlen($zipcode) != 4) {
            $errors['zip'] = "Must be 4 Characters!";
            $feedback['zip'] = "is-invalid";
        }

        if(strlen($cardname) < 2) {
            $errors['cardname'] = "Must be more than 1 Character";
            $feedback['cardname'] = "is-invalid";
        }

        if(strlen($cardnumber) < 16) {
            $errors['cardnumber'] = "Must be 16 Numbers!";
            $feedback['cardnumber'] = "is-invalid";
            // echo "short";
        }

        if(strlen($cvv) != 3) {
            $errors['cvv'] = "Must be 3 Characters";
            $feedback['cvv'] = "is-invalid";
        }

        if(!array_filter($errors)) {
            // If there are no Errors
            
            include_once("includes/functions.php");
            include_once("includes/db_connect.php");

            // Check stock one last time
            $cart = getCart($conn);
            $checkout_data = json_decode($cart['checkout'], true);
            $bikes = $checkout_data['rows'];

            $inStock = true;
            foreach($bikes as $bike) {
                $shortname = $bike['shortname'];
                $count = $bike['count'];
                if(!checkStock($conn, $shortname, $count)){
                    $inStock = false;
                    array_push($errorBikes, $bike['title']);
                }
            }

            if ($inStock) {
                makePayment($_SESSION['username'], $_SESSION['user_id'], $email);
                header("Location: redirect.php?destination=payment_success.php");
                exit();
            } else {
                $stockError = "You are exceeding the available stock for the following bikes:";
            }

        }


    } elseif (!isset($_POST['delivery-method']) && !isset($_POST['Make Payment'])) {
        header("Location: redirect.php?destination=products.php&error=access_denied");
        exit();
    }   
            
    // Page was accessed correctly
    include_once("includes/db_connect.php");
    $sql = "SELECT * FROM carts WHERE user_id = ?;";
    $stmt = mysqli_stmt_init($conn);
    if(!mysqli_stmt_prepare($stmt, $sql)) {
        header("Location: redirect.php?destination=products.php&error=statement_failed");
        exit();
    }
    mysqli_stmt_bind_param($stmt, "s", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    if (!$cart = mysqli_fetch_assoc($result)) {
        header("Location: products.php?error=statement_failed");
        exit();
    } else {
        $checkout_col = $cart['checkout'];
        $checkout_data = json_decode($checkout_col, true);
    }

    mysqli_stmt_close($stmt);

    

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main class="cart-main">

        <section class="payment-side-col">
           
        </section>

        <section class="payment-center-col">
            
            <div class="horizontal-box">
                <form action="<?php $_SERVER['PHP_SELF']; ?>" class="payment-form" method="POST">
                    <h5>Billing Address</h5>
                    <div class="flnamebox">
                        <div class="fnamebox">
                            <label for="name">Name</label>
                            <input value="<?php echo $firstname; ?>" name="firstname" class="form-control <?php echo $feedback['firstname']; ?>" id="name" type="text" aria-describedby="nameFeedback" required>
                            <div id="nameFeedback" class="invalid-feedback"><?php echo $errors['firstname']; ?></div>
                        </div>
                        <div class="lnamebox">
                            <label for="surname">Surname</label>
                            <input value="<?php echo $lastname; ?>" name="lastname" class="form-control <?php echo $feedback['lastname']; ?>" id="surname" type="text" aria-describedby="lastnameFeedback" required>
                            <div id="lastnameFeedback" class="invalid-feedback"><?php echo $errors['lastname']; ?></div>
                        </div>
                    </div>
                    <label for="email">Email</label>
                    <input value="<?php echo $email; ?>" class="form-control <?php echo $feedback['email']; ?>" name="email" type="email" id="email" aria-describedby="emailFeedback" required>
                    <div id="emailFeedback" class="invalid-feedback"><?php echo $errors['email']; ?></div>
                    <label for="address">Delivery Address</label>
                    <input value="<?php echo $address; ?>" class="form-control <?php echo $feedback['address']; ?>" type="text" id="address" name="address" aria-describedby="addressFeedback" required>
                    <div id="addressFeedback" class="invalid-feedback"><?php echo $errors['address']; ?></div>
                    <label for="city">City</label>
                    <input value="<?php echo $city; ?>" class="form-control <?php echo $feedback['city']; ?>" type="text" id="city" name="city" aria-describedby="cityFeedback" required>
                    <div id="cityFeedback" class="invalid-feedback"><?php echo $errors['city']; ?></div>
                    <div class="countryzipbox">
                        <div class="countrybox">
                            <label for="country">Country</label>
                            <select class="form-control" id="country" name="country" required>
                                <option value="" selected>Choose...</option>
                                <option value="GE">Germany</option>
                                <option value="ZA">South Africa</option>
                                <option value="US">USA</option>
                            </select>
                        </div>
                        <div class="zipbox">
                            <label for="zip">Zip Code</label>
                            <input value="<?php echo $zipcode; ?>" name="zip" class="form-control <?php echo $feedback['zip']; ?>" id="zip" type="text" aria-describedby="zipFeedback" required>
                            <div id="zipFeedback" class="invalid-feedback"><?php echo $errors['zip']; ?></div>
                        </div>
                    </div>
                    <h5 class="payment-title">Credit/Debit Card Payment</h5>
                    <div class="cardnamenumberbox">
                        <div class="cardnamebox">
                            <label for="cardname">Name on Card</label>
                            <input value="<?php echo $cardname; ?>" name="cardname" class="form-control <?php echo $feedback['cardname']; ?>" type="text" id="cardname" aria-describedby="cardnameFeedback" required>
                            <div id="cardnameFeedback" class="invalid-feedback"><?php echo $errors['cardname']; ?></div>
                        </div>
                        <div class="cardnumberbox">
                            <label for="cardnumber">Card Number</label>
                            <input value="<?php echo $cardnumber; ?>" name="cardnumber" class="form-control <?php echo $feedback['cardnumber']; ?>" class="form-control <?php echo $feedback['cardnumber']; ?>" id="cardnumber" type="text" aria-describedby="cardnumberFeedback" required>
                            <div id="cardnumberFeedback" class="invalid-feedback"><?php echo $errors['cardnumber']; ?></div>
                        </div>
                    </div>
                    <div class="expirecvvbox">
                        
                        <div class="expirebox">
                            <label for="innerexpirebox">Exiration Date</label>
                            <!-- <input name="expire" class="form-control" type="text" id="expire" required> -->
                            <div class="innerexpirebox" id="innerexpirebox">
                                <select name="expiremonth" id="expiremonth" class="expiremonth form-control" required>
                                    <option value = "" selected>M</option>
                                    <option value="01">01</option>
                                    <option value="02">02</option>
                                    <option value="03">03</option>
                                    <option value="04">04</option>
                                    <option value="05">05</option>
                                    <option value="06">06</option>
                                    <option value="07">07</option>
                                    <option value="08">08</option>
                                    <option value="09">09</option>
                                    <option value="10">10</option>
                                    <option value="11">11</option>
                                    <option value="12">12</option>
                                </select>
                                <select name="expireyear" id="expireyear" class="expireyear form-control" required>
                                    <option value = "" selected>Y</option>
                                    <option value="21">21</option>
                                    <option value="22">22</option>
                                    <option value="23">23</option>
                                    <option value="24">24</option>
                                    <option value="25">25</option>
                                    <option value="26">26</option>
                                    <option value="27">27</option>
                                    <option value="28">28</option>
                                    <option value="29">29</option>
                                    <option value="30">30</option>
                                    <option value="30">31</option>
                                    <option value="31">32</option>
                                    <option value="32">33</option>
                                    <option value="33">34</option>
                                    <option value="34">35</option>
                                    <option value="35">36</option>
                                </select>
                            </div>
                            
                        </div>
                        <div class="cvvbox">
                            <label for="cvv">CVV</label>
                            <input value="<?php echo $cvv; ?>" name="cvv" class="form-control <?php echo $feedback['cvv']; ?>" class="form-control" id="cvv" type="text" aria-describedby="cvvFeedback" required>
                            <div id="cvvFeedback" class="invalid-feedback"><?php echo $errors['cvv']; ?></div>
                        </div>
                    </div>
                    <input class="make-payment" name="submit" type="submit" value="Make Payment">
                    
                </form>
                <div class="payment-summary">
                    <h1>Payment</h1>
                    <h5 class="payment-summary-title">Order Summary</h5>
                    <table class="payment-summary-table">
                        <tbody>
                            <?php foreach($checkout_data['rows'] as $row): ?>
                            <tr>
                                <th><?php echo $row['count']; ?> x <?php echo $row['title']; ?></th>
                                <td><?php echo $row['total']; ?></td>
                            </tr>
                            <?php endforeach ?>
                            <tr>
                                <input type="hidden" id="store-delivery-method" value="<?php echo $checkout_data['delivery']; ?>">
                                <th>Delivery Fee</th>
                                <td class="checkout-delivery-total">0</td>
                            </tr>
                            <tr class="grandTotal-row">
                                <th class="grandTotal-row">Grand Total</th>
                                <td class="checkout-grand-total"><?php echo $checkout_data['grandTotal']; ?></td>
                            </tr>                  
                        </tbody>
                    </table>
                    <?php if($stockError != ""): ?>
                    <div class="payment-error-box ml-4 mr-4">
                        <p class="mb-1 mt-3"><?php echo $stockError; ?></p>
                        <ul>
                            <?php foreach($errorBikes as $errBike): ?>
                                <li><?php echo $errBike; ?></li>
                            <?php endforeach ?>
                        </ul>
                    </div>
                    <?php endif ?>
                </div>
                
            </div>
            
        </section>

        <section class="payment-side-col">
            
        </section>
       
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

        function updateDelivery() {

            const deliveryValBox = document.querySelector(".checkout-delivery-total");
            var deliverySelection = document.getElementById("store-delivery-method").value;
            var deliveryFee = 0;

            switch(deliverySelection) {
                case "DPD":
                    deliveryFee = 15;
                    break;
                
                case "DHL":
                    deliveryFee = 25;
                    break;
                
                case "DHLX":
                    deliveryFee = 40;
                    break;

                default:
                    deliveryFee = 0;
                    break;
            }

            deliveryValBox.innerHTML = deliveryFee;

        }

        document.addEventListener("DOMContentLoaded", function() {
            updateDelivery();
        })

    </script>
    
</body>
</html>