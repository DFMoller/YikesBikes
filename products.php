<?php
    
    session_start();

    if (isset($_GET['error']) && $_GET['error'] == "nobikes") {
        // No bikes found
        echo "<p class='red-alert alert'>No Bikes Found in DB!</p>";
        $bikes = array();
    } else {

        // Errors from other pages
        if (isset($_GET['error'])) {
            echo "<p class='red-alert alert'>Error: " . $_GET['error'] . "</p>";
        }

        // Bikes Found or have not tried yet -> This is to prevent an infinite loop
        include_once("includes/db_connect.php");
        include_once("includes/functions.php");

        if (!$bikes = getBikes($conn)) {
            // No Bikes Found
            header("Location: redirect.php?destination=products.php&error=nobikes");
            exit();
        }

    }


?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main>

        <div class="content">
            <h2>THESE ARE OUR BIKES</h2>
            <div class="search-box">
                <input oninput="filter();" class="bike-search form-control" type="text" name="search" placeholder="Search...">
            </div>
            <div class="items">
            <?php foreach($bikes as $bike):?>
                <a href="redirect.php?destination=details.php?bike=<?php echo $bike['shortname']; ?>" class="item-link">
                    <div class="item">
                        <h4><?php echo $bike['title']; ?></h4>
                        <img src="<?php echo "static/bikes/" . $bike['image_location']; ?>" alt="Bike">
                    </div>
                </a>
            <?php endforeach ?>
            </div>
        </div>

    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

        function filter() {

            var searchTerm = document.querySelector(".bike-search").value;
            var itemsBox = document.querySelector(".items");

            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = this.responseText;
                    // console.log(result);
                    itemsBox.innerHTML = result;
                }
            };
            
            xhttp.open("GET", "includes/filter_bikes.inc.php?search=" + searchTerm, true);
            xhttp.send();

        }
        

    </script>
    
</body>
</html>