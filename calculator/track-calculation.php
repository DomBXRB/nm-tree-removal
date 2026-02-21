<?php
// Track when someone calculates a price (AJAX endpoint)

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get JSON data from AJAX request
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Extract data
    $price = isset($data['price']) ? $data['price'] : 'Not calculated';
    $city = isset($data['city']) ? $data['city'] : 'Unknown';
    $height = isset($data['height']) ? $data['height'] : 'Unknown';
    $diameter = isset($data['diameter']) ? $data['diameter'] : 'Unknown';
    $serviceType = isset($data['serviceType']) ? $data['serviceType'] : 'Unknown';
    $treeCondition = isset($data['treeCondition']) ? $data['treeCondition'] : 'Unknown';
    $proximity = isset($data['proximity']) ? $data['proximity'] : 'Unknown';
    $timeline = isset($data['timeline']) ? $data['timeline'] : 'Unknown';
    
    // Format displays
    $city_display = ucwords(str_replace('-', ' ', $city));
    $serviceTypeDisplay = ucwords(str_replace('-', ' ', $serviceType));
    $treeConditionDisplay = ucwords(str_replace('-', ' ', $treeCondition));
    $proximityDisplay = ucwords(str_replace('-', ' ', $proximity));
    $timelineDisplay = ucwords(str_replace('-', ' ', $timeline));
    
    // Log to file
    $calc_log = date('Y-m-d H:i:s') . " | ";
    $calc_log .= "$city_display | $price | $serviceTypeDisplay | $timelineDisplay\n";
    @file_put_contents('calculations-log.txt', $calc_log, FILE_APPEND);
    
    // Send notification email
    $to = "dominicmadridseo@gmail.com";
    $subject = "🔔 Price Calculated: $price - $city_display";
    
    $message = "
=============================================
    PRICE CALCULATION (NO CONTACT YET)
=============================================

Someone just calculated a price but hasn't 
submitted contact info yet.

ESTIMATED PRICE: $price

---------------------------------------------
CALCULATION DETAILS
---------------------------------------------
Service Type:   $serviceTypeDisplay
Location:       $city_display
Timeline:       $timelineDisplay

Tree Height:    $height
Tree Diameter:  $diameter
Tree Condition: $treeConditionDisplay
Proximity:      $proximityDisplay

---------------------------------------------
TIMESTAMP
---------------------------------------------
Date/Time: " . date('F j, Y') . " at " . date('g:i a') . "

=============================================

NOTE: This is just a calculation - they haven't
submitted contact info yet. Watch for follow-up
quote request!

From: nmtreeremoval.com/calculator/
";
    
    $headers = "From: noreply@nmtreeremoval.com\r\n";
    
    $mail_sent = mail($to, $subject, $message, $headers);
    
    // Return JSON response
    echo json_encode([
        'success' => $mail_sent,
        'message' => $mail_sent ? 'Calculation tracked' : 'Tracking failed'
    ]);
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method'
    ]);
}
?>