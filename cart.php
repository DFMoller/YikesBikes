<?php
    
    session_start();

    if(!isset($_SESSION['username'])) {
        // Not Signed in
        header("Location: redirect.php?destination=products.php$&error=not_signed_in");
        exit();
    } else {

        // User is signed in

        // if (isset($_GET[''])) {
        //     echo "<p class='red-alert alert'>Error: " . $_GET['error'] . "</p>";
        // }

        include_once("includes/db_connect.php");
        include_once("includes/functions.php");

        $code = getCartCode($conn);

        $bikes = array();

        if (isset($_GET['bike']) && isset($_GET['code']) && ($code != $_GET['code'])) {
            // Add bike to cart
            $items = json_decode(addToCart($conn, $_GET['bike'], $_GET['code']), true); // This one returns an array of arrays
            foreach($items as $item) {
                $bike = getBike($conn, $item["shortname"]);
                $bike["count"] = $item["count"];
                array_push($bikes, $bike);
            }

        } else {
            // Simply display the cart
            if (!$cart = getCart($conn)) {
                // Cart is empty
                $bikes = null;
            } else {
                // Cart is not empty
                if($items = json_decode($cart['items'], true)) { // This one returns an array of objects
                    foreach($items as $item => $key) {
                        $bike = getBike($conn, $key['shortname']);
                        $bike['count'] = $key['count'];
                        array_push($bikes, $bike);
                    }
                } else {
                    // items is empty
                    $bikes = null;
                }
                
            }
        }

        $errors = array("submit" => "");
        $feedback = array("submit" => "");

        if (isset($_POST['submit'])) {

            $checkoutFlag = true;
            foreach($bikes as $bike) {
                $shortname = $bike['shortname'];
                $countFlag = $_POST[$shortname . "CountFlag"];
                if ($countFlag == "false") {
                    $checkoutFlag = false;
                }
            }

            if ($checkoutFlag) {
                // Go to checkout
                header("Location: checkout.php");
                exit();
            } else {
                $errors['submit'] = "You are trying to buy more items than we have in stock!";
                $feedback['submit'] = "is-invalid";
            }

        }

        
        if (!$all_bikes = getBikes($conn)) {
            // Could not fetch Bikes
            $all_bikes = null;
        }
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body class="cart-body">

    <?php include('templates/navigation.php'); ?>
    
    <main class="cart-main">

        <section class="cart-side-col">
            <h4>Our Bikes</h4>
            <ul class="bike-links">
                <?php foreach($all_bikes as $bike) : ?>
                    <a href="redirect.php?destination=details.php&bike=<?php echo $bike['shortname'] ?>">
                        <li>
                            <?php echo $bike['title']; ?>
                        </li>
                    </a>
                <?php endforeach ?>
            </ul>
        </section>

        <section class="cart-center-col">
            <h1>Your Cart</h1>
            <form class="cart-form" action="#" method="POST">
                <table class="cart-table">
                    <tbody>
                        <tr>
                            <th class="cart-col1">Bike</th>
                            <th class="td-narrow">Count</th>
                            <th class="td-narrow">Unit Price</th>
                            <th class="td-narrow">Discount</th>
                            <th class="td-narrow">Total</th>
                        </tr>
                        <?php if ($bikes != null): ?>
                            <?php foreach($bikes as $bike): ?>
                                <tr class="data-row">
                                    <td class="data-title">
                                        <?php echo $bike['title']; ?>
                                    </td>
                                    <td class="td-narrow">
                                        <input class="data-count form-control" type="number" onchange="updateCart();" min="0" value="<?php echo $bike['count']; ?>" aria-describedby="<?php echo $bike['shortname']; ?>CountFeedback">
                                        <div id="<?php echo $bike['shortname']; ?>CountFeedback" class="invalid-feedback"></div>
                                        <input class="data-shortname" type="hidden" value="<?php echo $bike['shortname']; ?>">
                                        <input class="count-flag" type="hidden" name="<?php echo $bike['shortname']; ?>CountFlag" value="true">
                                    </td>
                                    <td class="unit-price td-narrow">
                                        <?php echo $bike['price']; ?>
                                    </td>
                                    <td class="td-narrow item-discount"></td>
                                    <td class="td-narrow item-sum-price"></td>
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
                <?php if ($bikes != null) : ?>
                    <input type="hidden" name="validation" value="1">
                    <input class="cart-checkout-btn <?php echo $feedback['submit']; ?>" name="submit" type="submit" aria-describedby="submitFeedback" value="Checkout">
                    <div id="submitFeedback" class="invalid-feedback submitFeedback"><?php echo $errors['submit']; ?></div>
                <?php endif ?>
            </form>
        </section>

        <section class="cart-side-col">
            <h4>Order Summary</h4>
            <table class="cart-summary-table">
                <tbody>
                    <tr>
                        <th>Total</th>
                        <td class="cart-total">0</td>
                    </tr>
                    <tr>
                        <th>Delivery Fee</th>
                        <td>0</td>
                    </tr>                    
                </tbody>
            </table>
            <div class="discount-notice">
                <p>If you purchase 10 of the same items, you are eligible for a 15% discount on each.</p>
            </div>
        </section>
       
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">
        
        function updateCart() {

            updateTotals();
            
            const item_counts = document.querySelectorAll(".data-count");
            const rows = document.querySelectorAll(".data-row");
            const bigTotal = parseInt(document.querySelector(".cart-total").innerHTML);

            let bikes = [];
            let checkout_items = {bigTotal: bigTotal, delivery: "", grandTotal: 0, rows: []};
            rows.forEach((row) => {

                var shortname = row.querySelector('.data-shortname').value;
                var count = parseInt(row.querySelector('.data-count').value);
                var price =  parseInt(row.querySelector('.unit-price').innerHTML);
                var discount =  parseInt(row.querySelector('.item-discount').innerHTML);
                var total = parseInt(row.querySelector(".item-sum-price").innerHTML);
                var title = row.querySelector(".data-title").innerHTML;
                var bike_instance = {title: title,shortname: shortname, count: count, price: price, discount: discount, total: total};
                bikes.push({shortname: shortname, count: parseInt(count)});
                checkout_items["rows"].push(bike_instance);

            })

            cart_data = JSON.stringify(bikes);
            checkout_data = JSON.stringify(checkout_items);

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    // console.log(result);
                }
            };

            var parameters = "cart=" + cart_data + "&checkout=" + checkout_data;
            
            xhttp.open("POST", "includes/updateCart.inc.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(parameters);

        }

        function updateTotals() {

            const rows = document.querySelectorAll(".data-row");
            const bigTotalBox = document.querySelector(".cart-total");
            var bigTotal = 0;

            rows.forEach((row) => {
                var price = row.querySelector(".unit-price").innerHTML;
                var count = row.querySelector(".data-count").value;
                var shortname = row.querySelector(".data-shortname").value;
                var total_box = row.querySelector(".item-sum-price");
                var discount_box = row.querySelector(".item-discount");
                var total = 0;
                var discount = 0;
                
                checkStock(row, shortname, count);

                if(count > 9) {
                    // eligible for discount
                    discount = 10 * price * 0.15;
                    total = price * count - discount;
                    discount_box.innerHTML = discount;
                } else {
                    total = price * count;
                    discount_box.innerHTML = "";
                }
                total_box.innerHTML = total;
                bigTotal += total;
            })

            bigTotalBox.innerHTML = bigTotal;
            let submit_btn = document.querySelector(".cart-checkout-btn");

            if (bigTotal == 0) {
                submit_btn.style.display = "none";
            } else {
                submit_btn.style.display = "block";
            }
        }

        function checkStock(row, shortname, count) {

            // console.log("LastFlag: " + lastFlag);

            var countInput = row.querySelector(".data-count");
            var countFeedback = row.querySelector(".invalid-feedback");
            var countFlag = row.querySelector(".count-flag");

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    // console.log("Result: " + result);
                    if (result == "false") {
                        countInput.classList.add("is-invalid");
                        countFeedback.innerHTML = "Not enough in stock!";
                        countFlag.value = "false";
                        return false;
                    } else if(result == "true") {
                        countInput.classList.remove("is-invalid");
                        countFlag.value = "true";
                        return true;
                    } else {
                        console.log("Unexpected...");
                    }

                }
            };

            var parameters = "shortname=" + shortname + "&count=" + count;
            
            xhttp.open("POST", "includes/checkStock.inc.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(parameters);

        }

        // function setSubmitButton() {

        //     let submit_btn = document.querySelector(".cart-checkout-btn");
        //     var rows = document.querySelectorAll(".data-row");
        //     var show = true;

        //     rows.forEach((row) => {
        //         let input = row.querySelector(".data-count");
        //         if(input.classList.contains("is-invalid")) {
        //             show = false;
        //         }
        //     })
            
        //     if (show) {
        //         submit_btn.style.display = "block";
        //         console.log("show");
        //     } else {
        //         submit_btn.style.display = "none";
        //         console.log("Dont show");
        //     }

        // }

        document.addEventListener("DOMContentLoaded", function() {
            updateTotals();
            updateCart();
        })

    </script>
    
</body>
</html>