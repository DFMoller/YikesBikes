<?php
    
    session_start();

    if (!isset($_SESSION['username'])){
        // User Not signed in
        header("Location: home.php?msg=error_not_signed_in");
    } else {

        include_once("includes/db_connect.php");
        include_once("includes/functions.php");

        if (!isset($_GET['bike'])) {
            // No bike specified
            header("Location: products.php?error=no_bike_specified");
            exit();
        }

        $bikename = $_GET['bike'];
        if ($bike = getBike($conn, $bikename)) {
            $title = $bike['title'];
            $price = $bike['price'];
            $display_price = $bike['display_price'];
            $img_location = $bike['image_location'];
            $specifications = json_decode($bike['specifications']);
        } else {
            // Bike not found
            header("Location: products.php?error=bike_not_found");
            exit();
        }
        $code = randomPassword();
    }

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body class="details-body">

    <?php include('templates/navigation.php'); ?>

    <main>
        <div class="details-content">
            <div class="bike-card">
                <img class="bike-img" src="<?php echo "static/bikes/cropped/" . $img_location; ?>" alt="Bike">
                <div class="right-info">
                    <div class="info-card">
                        <h4><?php echo "TREK " . $title; ?></h4>
                        <p class="p2"><?php echo $display_price; ?></p>
                        <p class="stock">Bikes in Stock: <span class="static_count"><?php echo $bike['stock']; ?></span></p>
                        <?php if(isset($_SESSION['admin'])): ?>
                            <label for="addStock">Admin: Update Bike Stock</label>
                            <input class="add-stock mb-3" onchange="updateStock('<?php echo $bikename; ?>')" type="number" value="<?php echo $bike['stock']; ?>" id="addStock">
                        <?php endif ?>
                        <div class="bike-action">
                            <div class="add-to-cart-button" onclick="<?php echo "window.location.href = 'redirect.php?destination=cart.php&bike=$bikename&code=$code';"; ?>">
                                <svg class="add-to-cart-icon" width="71" height="65" viewBox="0 0 71 65" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path class="add-to-cart-wheel" d="M52.6851 57.5C52.6851 55.4592 54.3688 54.4388 56.0231 54.4388C57.6774 54.4388 59.3611 55.4592 59.3611 57.5C59.3611 59.5408 57.7749 60.5612 56.0231 60.5612C54.2713 60.5612 52.6851 59.5408 52.6851 57.5Z" fill="black"/>
                                    <path class="add-to-cart-wheel" d="M24.2907 57.5C24.2907 55.4592 25.9745 54.4388 27.6287 54.4388C29.283 54.4388 30.9667 55.4592 30.9667 57.5C30.9667 59.5408 29.3805 60.5612 27.6287 60.5612C25.877 60.5612 24.2907 59.5408 24.2907 57.5Z" fill="black"/>
                                    <path d="M4 12H13L24.2907 43.0017H59.3611L66.3611 22M56.0231 54.4388C54.3688 54.4388 52.6851 55.4592 52.6851 57.5C52.6851 59.5408 54.2713 60.5612 56.0231 60.5612C57.7749 60.5612 59.3611 59.5408 59.3611 57.5C59.3611 55.4592 57.6774 54.4388 56.0231 54.4388ZM27.6287 54.4388C25.9745 54.4388 24.2907 55.4592 24.2907 57.5C24.2907 59.5408 25.877 60.5612 27.6287 60.5612C29.3805 60.5612 30.9667 59.5408 30.9667 57.5C30.9667 55.4592 29.283 54.4388 27.6287 54.4388Z" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M41.6831 2V34M41.6831 34L50 27.5M41.6831 34L33 27.5" stroke="black" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                                <span>Add to Cart</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="specifications">
                <h1 class="spec-section-title">SPECIFICATIONS</h1>
                <?php foreach($specifications as $title => $subdata): ?>
                    <h5 class="spec-title"><?php echo $title; ?></h5>
                    <table class="spec-table">
                        <tbody>
                        <?php foreach($subdata as $key => $description): ?>
                            <?php foreach($description as $num => $line): ?>
                                <?php if ($num == 0): ?>
                                    <tr class="spec-tr">
                                        <th class="spec-th" rowspan="<?php echo count($description);?>"><?php echo $key; ?></th>
                                        <td class="spec-td"><?php echo $line; ?></td>
                                    </tr>
                                <?php else: ?>
                                    <tr class="spec-tr">
                                        <td class="spec-td"><?php echo $line; ?></td>
                                    </tr>
                                <?php endif ?>
                            <?php endforeach ?>
                        <?php endforeach ?>
                        </tbody>
                    </table>
                <?php endforeach ?>
            </div>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

        function updateStock(shortname) {

            var count = document.getElementById("addStock").value;
            var display_count = document.querySelector(".static_count");

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    console.log(result);
                    display_count.innerHTML = count;
                }
            };

            var parameters = "shortname=" + shortname + "&count=" + count;
            
            xhttp.open("POST", "includes/updateStock.inc.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send(parameters);

        }

    </script>
    
</body>
</html>