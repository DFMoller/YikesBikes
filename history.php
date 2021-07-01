<?php
    
    session_start();

    if (!isset($_SESSION['username'])) {
        // Not signed in
        header("Location: products.php?error=not_signed_in");
        exit();
    }

    // Errors from other pages
    if (isset($_GET['error'])) {
        echo "<p class='red-alert alert'>Error: " . $_GET['error'] . "</p>";
    }

    include_once("includes/functions.php");

    if (!$history = getPurchaseHistory($_SESSION['user_id'])) {

        // History is empty
        $history = null;
    }

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
            <?php if($history): ?>
            <?php foreach($history as $date => $data): ?>
                <div class="hist-title-box">
                    <h5 class="hist-title">Date / Time: <span class="hist-timestamp"><?php echo $date; ?></span></h5>
                    <a href="redirect.php?destination=includes/repeat_order.inc.php&timestamp=<?php echo $date; ?>" class="re-order-btn">Make this order Again</a>
                </div>
                <table class="hist-table">
                    <tbody>
                        <tr class="hist-title-row">
                            <th>Bike(s)</th>
                            <th class="hist-narrow hist-center">Count</th>
                            <th class="hist-narrow hist-center">Unit Price</th>
                            <th class="hist-narrow hist-center">Discount</th>
                            <th class="hist-narrow hist-center">Total</th>
                        </tr>
                    <?php foreach($data['rows'] as $key => $row): ?>
                        <tr class="hist-tr">
                            <td class="hist-td"><?php echo $row['title']; ?></td>
                            <td class="hist-td hist-narrow hist-center"><?php echo $row['count']; ?></td>
                            <td class="hist-td hist-narrow hist-center"><?php echo $row['price']; ?></td>
                            <td class="hist-td hist-narrow hist-center"><?php echo $row['discount']; ?></td>
                            <td class="hist-td hist-narrow hist-center"><?php echo $row['total']; ?></td>
                        </tr>
                    <?php endforeach ?>
                    <tr class="hist-tr">
                        <td class="hist-td hist-narrow" colspan="5"></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Sub Total</strong></td>
                        <td class="hist-td hist-narrow hist-center"><?php echo $data['bigTotal']; ?></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Delivery Fee</strong><span class="hist-delivery-method">(<?php echo $data['delivery']; ?>)</span></td>
                        <td class="hist-td hist-narrow hist-center"><?php echo $data['deliveryFee']; ?></td>
                    </tr>
                    <tr class="hist-tr">
                        <td class="hist-td hist-totals-title" colspan="4"><strong>Grand Total</strong></td>
                        <td class="hist-td hist-narrow hist-center"><?php echo $data['grandTotal']; ?></td>
                    </tr>
                    </tbody>
                </table>
            <?php endforeach ?>
            <?php else : ?>
                <table class="hist-table">
                    <tbody>
                        <tr class="hist-title-row">
                            <th>Bike(s)</th>
                            <th class="hist-narrow">Count</th>
                            <th class="hist-narrow">Unit Price</th>
                            <th class="hist-narrow">Discount</th>
                            <th class="hist-narrow">Total</th>
                        </tr>
                        <tr class="hist-tr">
                            <td class="hist-td">You Have no Purchase History</td>
                            <td class="hist-td" colspan="4"></td>
                        </tr>
                    </tbody>
                </table>
            <?php endif ?>
        </div>
    </main>

    <?php include('templates/footer.php'); ?>
    
</body>
</html>