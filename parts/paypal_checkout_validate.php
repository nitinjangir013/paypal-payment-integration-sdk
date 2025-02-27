<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start output buffering to capture any accidental output (like HTML)
ob_start();

require_once '../helper/root.php';
require_once 'PaypalCheckout.class.php';
require_once '../helper/db_connection.php'; 

$response = array('status' => 0, 'msg' => 'Invalid request');

if (isset($_POST['paypal_order_check']) && !empty($_POST['order_id'])) {
    $paypalOrderID = $_POST['order_id'];

    try {
        // Create an instance of PaypalCheckout
        $paypalCheckout = new PaypalCheckout();
        $paypalInfo = $paypalCheckout->validate($paypalOrderID);

        if (empty($paypalInfo)) {
            throw new Exception('Invalid PayPal response');
        }

        // Extract necessary data
        $orderID = isset($paypalInfo['id']) ? $paypalInfo['id'] : '';
        $status = isset($paypalInfo['status']) ? $paypalInfo['status'] : '';
        $payerInfo = isset($paypalInfo['payer']) ? $paypalInfo['payer'] : array();
        $purchaseItem = isset($paypalInfo['purchase_units'][0]) ? $paypalInfo['purchase_units'][0] : array();
        $paymentSource = isset($purchaseItem['payments']['captures'][0]) ? $purchaseItem['payments']['captures'][0] : array();

        // Check if the payment was successful
        if (!empty($paypalInfo) && $status == 'COMPLETED') {
            $transactionID = isset($paymentSource['id']) ? $paymentSource['id'] : '';
            $paidAmount = isset($paymentSource['amount']['value']) ? $paymentSource['amount']['value'] : '';
            $paidCurrency = isset($paymentSource['amount']['currency_code']) ? $paymentSource['amount']['currency_code'] : '';
            $user_uid = isset($purchaseItem['custom_id']) ? $purchaseItem['custom_id'] : '';
            $itemName = isset($purchaseItem['items'][0]['name']) ? $purchaseItem['items'][0]['name'] : '';

            // Check if the transaction already exists
            $prevPayment = $conn->query("SELECT id FROM transactions WHERE transaction_id = '".$conn->real_escape_string($transactionID)."'");
            if ($prevPayment->num_rows > 0) {
                $response['status'] = 1;
                $response['ref_id'] = $transactionID;
            } else {
                // Insert transaction record
                $insert = $conn->query("
                    INSERT INTO transactions(item_number, item_name, item_price, item_price_currency, payer_id, payer_name, payer_email, payer_country, merchant_id, merchant_email, order_id, transaction_id, paid_amount, paid_amount_currency, payment_source, payment_status, created, modified)
                    VALUES (
                        '".$conn->real_escape_string($user_uid)."',
                        '".$conn->real_escape_string($itemName)."',
                        '".$conn->real_escape_string($paidAmount)."',
                        '".$conn->real_escape_string($paidCurrency)."',
                        '".$conn->real_escape_string($payerInfo['payer_id'])."',
                        '".$conn->real_escape_string($payerInfo['name']['given_name'].' '.$payerInfo['name']['surname'])."',
                        '".$conn->real_escape_string($payerInfo['email_address'])."',
                        '".$conn->real_escape_string($payerInfo['address']['country_code'])."',
                        '".$conn->real_escape_string($paypalInfo['payee']['merchant_id'])."',
                        '".$conn->real_escape_string($paypalInfo['payee']['email_address'])."',
                        '".$conn->real_escape_string($orderID)."',
                        '".$conn->real_escape_string($transactionID)."',
                        '".$conn->real_escape_string($paidAmount)."',
                        '".$conn->real_escape_string($paidCurrency)."',
                        '".$conn->real_escape_string($paymentSource['status'])."',
                        '".$conn->real_escape_string($status)."',
                        '".$conn->real_escape_string($currentDateTime)."',
                        NOW()
                    )
                ");

                if ($insert) {
                    echo "Payment Done!";
                } else {
                    throw new Exception('Failed to insert transaction: ' . $conn->error);
                }
            }
        } else {
            $response['status'] = 0;
            $response['msg'] = 'Transaction has been failed!';
        }
    } catch (Exception $e) {
        $response['status'] = 0;
        $response['msg'] = 'Something went wrong! ' . $e->getMessage();
    }
} else {
    $response['msg'] = 'Required fields are missing';
}

// Clean buffer to avoid accidental HTML output
ob_end_clean();

echo json_encode($response);
die;
?>