<?php
    
    session_start();

    // Read JSON file
    $json = file_get_contents('static/bikes/data.json');
        
    //Decode JSON
    $json_data = json_decode($json,true);

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main>
        <div class="introduction_box">
            <div class="intro-left">
                <div class="intro-text">
                    <h1>WELCOME TO<br>YIKES BIKES</h1>
                    <p class="p3">Have a look at our range <a class="blue" href="redirect.php?destination=products.php">HERE</a></p>
                </div>
                <div class="user-counter-box">
                    <h4>Users Currently Online</h4>
                    <span class="users-online" id="num-users"></span>
                </div>
            </div>
            <div class="intro-right">
                <div id="carouselExampleSlidesOnly" class="carousel slide" data-bs-ride="carousel">
                    <div class="carousel-inner">
                            <div class="carousel-item active">
                                <img src="static/bikes/cropped/topfuel9.7.jpg" class="d-block w-100" alt="<?php $shortname; ?>">
                            </div>
                        <?php foreach($json_data as $shortname => $array):?>
                            <div class="carousel-item">
                                <img src="static/bikes/cropped/<?php echo $array['img']; ?>" class="d-block w-100" alt="<?php $shortname; ?>">
                            </div>
                        <?php endforeach ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>

    <script type="text/javascript">

        my_delay = 10000;

        function getUsers() {
            var xmlhttp = new XMLHttpRequest();
            xmlhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    document.getElementById("num-users").innerHTML = this.responseText;
                    setTimeout(getUsers(), my_delay);
                }
            };
            xmlhttp.open("GET","includes/get_num_users.inc.php", true);
            xmlhttp.send();
        }

        getUsers();

    </script>
    
</body>
</html>