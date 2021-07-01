<?php

    session_start();

    if(!isset($_SESSION["username"])) {
        // User not logged in
        header("Location: products.php?error=not_signed_in");
        exit();
    } else {

        include_once("includes/db_connect.php");
        include_once("includes/functions.php");

        if (!$cart = getCart($conn)) {
            // Cart does not exist
            header("Location: products.php?error=cart_does_not_exist");
            exit();
        } else {
            //Cart Exists

            if(!$checkout_text = $cart['checkout']) {
                // Checkout is empty
                header("Location: products.php?error=checkout_empty");
                exit();
            }

            $checkout = json_decode($checkout_text, true);
            $bigTotal = $checkout["bigTotal"];

            $rows = array();
            foreach($checkout["rows"] as $key => $val) {
                array_push($rows, $val);
            }
            // print_r($rows); // This was correct on 27 June 2021
            $storage_rows = json_encode($rows);
            // print_r($storage_rows);

        }

    }
?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main class="cart-main">

        <section class="cart-side-col">
           
        </section>

        <section class="cart-center-col">
            <h1>Checkout</h1>
            <table class="cart-table">
                <tbody>
                    <tr>
                        <th class="cart-col1">Bike</th>
                        <th class="td-narrow">Count</th>
                        <th class="td-narrow">Unit Price</th>
                        <th class="td-narrow">Discount</th>
                        <th class="td-narrow">Total</th>
                    </tr>
                    <?php if ($rows != null): ?>
                        <?php foreach($rows as $row): ?>
                            <tr class="data-row">
                                <td class="data-title"><?php echo $row['title']; ?></td>
                                <td class="td-narrow"><?php echo $row['count']; ?></td>
                                <td class="unit-price td-narrow"><?php echo $row['price']; ?></td>
                                <td class="item-discount td-narrow"><?php echo $row['discount']; ?></td>
                                <td class="td-narrow item-sum-price"><?php echo $row['total']; ?></td>
                            </tr>
                        <?php endforeach ?>
                    <?php else: ?>
                        <tr>
                            <td>Your Cart is empty</td>
                            <td class="td-narrow"></td>
                            <td class="td-narrow"></td>
                            <td class="td-narrow"></td>
                        </tr>
                    <?php endif ?>
                </tbody>
            </table>
            <form class="delivery-form" action="payment.php" method="POST">
                <div class="delivery-options">
                    <h5 class="delivery-title">Please select a delivery method</h5>
                    <label for="DPD-Radio"><input class="delivery-radio" onchange="updateDelivery();" id="DPD-Radio" value="DPD" type="radio" name="delivery-method" checked>DPD<span>Description</span></label>
                    <label for="DHL-Radio"><input class="delivery-radio" onchange="updateDelivery();" id="DHL-Radio" value="DHL" type="radio" name="delivery-method">DHL<span>Description</span></label>
                    <label for="DHLX-Radio"><input class="delivery-radio" onchange="updateDelivery();" id="DHLX-Radio" value="DHLX" type="radio" name="delivery-method">DHL Express<span>Description</span></label>
                </div>
                
                <h4 class="summary-title">Order Summary</h4>
                <table class="checkout-summary-table">
                    <tbody>
                        <tr>
                            <th>Sub-Total</th>
                            <td class="checkout-total"><?php echo $bigTotal; ?></td>
                        </tr>
                        <tr>
                            <th>Delivery Fee</th>
                            <td class="checkout-delivery-total">0</td>
                        </tr>
                        <tr>
                            <th>Grand Total</th>
                            <td class="checkout-grand-total"><?php echo $bigTotal; ?></td>
                        </tr>                  
                    </tbody>
                </table>
                <label for="consent-checkbox"><input id="consent-checkbox" name="consent" type="checkbox" required>I accept the privacy policy.<span class="red-star">*</span></label><br>
                <input class="checkout-btn" type="submit" value="Proceed to Payment">
            </form>
            
        </section>

        <section class="cart-side-col">
            
        </section>
       
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

        function updateDelivery() {

            const deliveryForm = document.querySelector(".delivery-form");
            var subTotal = parseInt(document.querySelector(".checkout-total").innerHTML);
            const deliveryValBox = document.querySelector(".checkout-delivery-total");
            const grandTotalBox = document.querySelector(".checkout-grand-total");
            var deliverySelection = deliveryForm.elements.namedItem("delivery-method").value;
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

            console.log("Choice: " + deliverySelection + " = " + deliveryFee);
            deliveryValBox.innerHTML = deliveryFee;
            var grandTotal = subTotal + deliveryFee;
            grandTotalBox.innerHTML = grandTotal;

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    console.log(result);
                }
            };

            var parameters = "delivery=" + deliverySelection + "&grandTotal=" + grandTotal + "&deliveryFee=" + deliveryFee;
            
            xhttp.open("POST", "includes/addDeliveryCheckout.inc.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(parameters);

        }

        document.addEventListener("DOMContentLoaded", function () {

            updateDelivery();

        })

    </script>
    
</body>
</html>