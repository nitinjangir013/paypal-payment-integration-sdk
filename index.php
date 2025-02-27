<?php include "./helper/root.php"; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>PayPal Payment Gateway</title>
</head>
<body>
    <div id="paypal-button-container"></div>
    <div id="paymentResponse" class="hidden"></div>

    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- PayPal SDK -->
    <script src="https://www.paypal.com/sdk/js?client-id=YOUR_PAYPAL_CLIENT_ID&currency=USD"></script>

    <script>
        let uniqueTimestamp = Date.now();
        let itemName = "Test";
        let itemPrice = 1;
        let currency = "USD";

        function setProcessing(status) {
            console.log("Processing: " + status);
        }

        paypal.Buttons({
            createOrder: (data, actions) => {
                return actions.order.create({
                    purchase_units: [{
                        custom_id: uniqueTimestamp.toString(),
                        description: itemName,
                        amount: {
                            currency_code: currency,
                            value: itemPrice.toFixed(2),
                            breakdown: {
                                item_total: {
                                    currency_code: currency,
                                    value: itemPrice.toFixed(2)
                                }
                            }
                        },
                        items: [{
                            name: itemName,
                            description: itemName,
                            unit_amount: {
                                currency_code: currency,
                                value: itemPrice.toFixed(2)
                            },
                            quantity: "1",
                            category: "DIGITAL_GOODS"
                        }]
                    }]
                });
            },
            onApprove: (data, actions) => {
                return actions.order.capture().then(function(orderData) {
                    setProcessing(true);

                    var postData = {
                        paypal_order_check: 1,
                        order_id: orderData.id
                    };

                    $.ajax({
                        url: './parts/paypal_checkout_validate.php',
                        method: 'POST',
                        data: postData,
                        dataType: 'json',
                        success: function(result) {
                            if (result.status == 1) {
                                window.location.href = "./parts/payment-status.php?checkout_ref_id=" + result.ref_id;
                            } else {
                                console.log(result);
                            }
                            setProcessing(false);
                        },
                        error: function(xhr, status, error) {
                            console.log('Error in ajax:', error);
                            setProcessing(false);
                        }
                    });
                });
            }
        }).render('#paypal-button-container');
    </script>
</body>
</html>
