<?php
include('../helper/db_connection.php');

$log_file = 'webhook.log';
$payload = file_get_contents('php://input');
file_put_contents($log_file, "Received Webhook: $payload\n", FILE_APPEND);
$event = json_decode($payload, true);
if ($event) {
    $event_type = $event['event_type'] ?? 'Unknown';
    $resource = $event['resource'] ?? [];

    // Handle different event types
    if ($event_type === 'PAYMENT.CAPTURE.COMPLETED') {
        $capture_id = $resource['id'] ?? 'Unknown';
        $amount = $resource['amount']['value'] ?? 'Unknown';
        $currency = $resource['amount']['currency_code'] ?? 'Unknown';
        $payee_email = $resource['payee']['email_address'] ?? 'Unknown';
        
        // Log the event
        file_put_contents($log_file, "Payment Capture Completed: $capture_id\n", FILE_APPEND);

        // Prepare and execute the database insertion
        $stmt = $conn->prepare("INSERT INTO payment_captures (capture_id, amount, currency, payee_email, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $capture_id, $amount, $currency, $payee_email, $event_type);

        if ($stmt->execute()) {
            file_put_contents($log_file, "Database entry created for Capture ID: $capture_id\n", FILE_APPEND);
        } else {
            file_put_contents($log_file, "Database entry failed for Capture ID: $capture_id\n", FILE_APPEND);
        }

        $stmt->close();
    } else if ($event_type === 'CHECKOUT.ORDER.APPROVED') {
        $order_id = $resource['id'] ?? 'Unknown';
        $amount = $resource['purchase_units'][0]['amount']['value'] ?? 'Unknown';
        $currency = $resource['purchase_units'][0]['amount']['currency_code'] ?? 'Unknown';
        $payer_email = $resource['payment_source']['paypal']['email_address'] ?? 'Unknown';

        // Log the event
        file_put_contents($log_file, "Checkout Order Approved: $order_id\n", FILE_APPEND);

        // Prepare and execute the database insertion
        $stmt = $conn->prepare("INSERT INTO checkout_orders (order_id, amount, currency, payer_email, status) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $order_id, $amount, $currency, $payer_email, $event_type);

        if ($stmt->execute()) {
            file_put_contents($log_file, "Database entry created for Order ID: $order_id\n", FILE_APPEND);
        } else {
            file_put_contents($log_file, "Database entry failed for Order ID: $order_id\n", FILE_APPEND);
        }

        $stmt->close();
    } else {
        file_put_contents($log_file, "Unhandled Event Type: $event_type\n", FILE_APPEND);
    }
} else {
    file_put_contents($log_file, "Empty or invalid event data\n", FILE_APPEND);
}

$conn->close();

http_response_code(200);
?>