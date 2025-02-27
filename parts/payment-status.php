<?php
require_once '../helper/root.php'; 
require_once '../helper/db_connection.php'; 

$payment_id = !empty($_GET['checkout_ref_id'])?$_GET['checkout_ref_id']:''; 
?>
<!DOCTYPE html>
<html lang="en-US">
<head>
    <title>Payment Status</title>
</head>
<body>
    <div class="container">
        <h2>Status</h2>
        <div class="">
            <?php 
                if(!empty($payment_id)){ 

                    $sql = "SELECT payment_status, transaction_id, item_name, item_price, item_price_currency FROM transactions WHERE transaction_id = '".$payment_id."'";
                    $result = $conn->query($sql);

                    // Check if there are results and output data
                    if ($result->num_rows > 0) {
                            $paymentRow = $result->fetch_assoc(); 
                            $payment_status = $paymentRow['payment_status']; 
                            $transaction_id = $paymentRow['transaction_id']; 
                            $itemName = $paymentRow['item_name']; 
                            $itemPrice = $paymentRow['item_price']; 
                            $currency = $paymentRow['item_price_currency'];

                            if(!empty($payment_status)){
                                ?>
                                <div class="status">
                                    <?php 
                                        if($payment_status == 'COMPLETED'){
                                            ?>
                                                <h1 class="success">Success</h1>
                                                <p>Thank you for your payment.</p>
                                                <p>Transaction ID: <?php echo $transaction_id; ?></p>
                                                <p>Item Name: <?php echo $itemName; ?></p>
                                                <p>Price: <?php echo $itemPrice.' '.$currency; ?></p>
                                            <?php 
                                        }
                                        else
                                        { 
                                            ?>
                                                <h1 class="error">Your Payment has Failed</h1>
                                            <?php 
                                        } 
                                    ?>
                                </div>
                            <?php 
                            }
                            else
                            {
                                ?>
                                    <h1 class="error">Payment Failed</h1>
                                    <p>Transaction ID: <?php echo $payment_id; ?></p>
                                    <p>Something went wrong, please try again.</p>
                                <?php
                            }
                    } else {
                        echo "0 results";
                    }
                    $conn->close();
                } 
            ?>
        </div>
    </div>
</body>
</html>