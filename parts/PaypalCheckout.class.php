<?php  
include_once '../helper/root.php'; 

class PaypalCheckout{  
    public $paypalAuthAPI = PAYPAL_SANDBOX?'https://api-m.sandbox.paypal.com/v1/oauth2/token':'https://api-m.paypal.com/v1/oauth2/token'; 
    public $paypalAPI = PAYPAL_SANDBOX?'https://api-m.sandbox.paypal.com/v2/checkout':'https://api-m.paypal.com/v2/checkout'; 
    public $paypalClientID = PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_ID:PAYPAL_PROD_CLIENT_ID;  
    private $paypalSecret = PAYPAL_SANDBOX?PAYPAL_SANDBOX_CLIENT_SECRET:PAYPAL_PROD_CLIENT_SECRET;  
    
   public function validate($order_id) { 
    // Get access token
    $ch = curl_init();  
    curl_setopt($ch, CURLOPT_URL, $this->paypalAuthAPI);  
    curl_setopt($ch, CURLOPT_HEADER, false);  
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($ch, CURLOPT_POST, true);  
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);  
    curl_setopt($ch, CURLOPT_USERPWD, $this->paypalClientID.":".$this->paypalSecret);  
    curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");  
    $auth_response = curl_exec($ch); 
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE); 
    curl_close($ch);

    // Decode response and check for errors
    $auth_response = json_decode($auth_response);
    if ($http_code != 200 || empty($auth_response->access_token)) {
        $error_message = !empty($auth_response->error) ? 'Error '.$auth_response->error.': '.$auth_response->error_description : 'Unknown error';
        throw new Exception('Authentication failed: '.$error_message);  
    } 
    
    // Fetch order details
    $ch = curl_init(); 
    curl_setopt($ch, CURLOPT_URL, $this->paypalAPI.'/orders/'.$order_id); 
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);  
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer '. $auth_response->access_token));  
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET'); 
    $api_response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);  
    curl_close($ch); 

    // Decode response and check for errors
    $api_response = json_decode($api_response, true); 
    if ($http_code != 200 || empty($api_response)) {
        $error_message = !empty($api_response['error']) ? 'Error '.$api_response['error'].': '.$api_response['error_description'] : 'Unknown error';
        throw new Exception('API request failed: '.$error_message);  
    } 

    return $api_response; 
} 
}
?>
