<?php
    
    session_start();

    if (!isset($_SESSION['username'])) {
        // Not signed in
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    include_once("includes/functions.php");

    if (!$history = getPurchaseHistory($_SESSION['user_id'])) {
        header("Location: redirect.php?destination=account.php&error=could_not_fetch_history");
        exit();
    }

    // print_r($history);

?>

<!DOCTYPE html>
<html lang="en">

    <?php include('templates/head.php'); ?>

<body>

    <?php include('templates/navigation.php'); ?>

    <main>
        <!-- <h3>My Account</h3> -->
        <div class="history-box">
        <h1 class="hist-section-title">Purchase History</h1>
            <?php foreach($history as $date => $data): ?>
                <h5 class="hist-title">Date / Time: <span class="hist-timestamp"><?php echo $date; ?></span></h5>
                <table class="hist-table">
                    <tbody>
                        <tr class="hist-title-row">
                            <th>Bike(s)</th>
                            <th class="hist-narrow">Count</th>
                            <th class="hist-narrow">Unit Price</th>
                            <th class="hist-narrow">Discount</th>
                            <th class="hist-narrow">Total</th>
                        </tr>
                    <?php foreach($data['rows'] as $key => $row): ?>
                        <tr class="hist-tr">
                            <td class="hist-td"><?php echo $row['title']; ?></td>
                            <td class="hist-td hist-narrow"><?php echo $row['count']; ?></td>
                            <td class="hist-td hist-narrow"><?php echo $row['price']; ?></td>
                            <td class="hist-td hist-narrow"><?php echo $row['discount']; ?></td>
                            <td class="hist-td hist-narrow"><?php echo $row['total']; ?></td>
                        </tr>
                    <?php endforeach ?>
                    <tr class="hist-tr">
                        <td class="hist-td hist-narrow" colspan="5"></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Sub Total</strong></td>
                        <td class="hist-td hist-narrow"><?php echo $data['bigTotal']; ?></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Delivery Fee</strong><span class="hist-delivery-method">(<?php echo $data['delivery']; ?>)</span></td>
                        <td class="hist-td hist-narrow"><?php echo $data['deliveryFee']; ?></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Sub Total</strong></td>
                        <td class="hist-td hist-narrow"><?php echo $data['grandTotal']; ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php endforeach ?>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>
    
</body>
</html>