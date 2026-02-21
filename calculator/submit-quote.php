<?php
// FIXED VERSION - Better email headers to ensure delivery

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Get form data - BASIC INFO
    $name = isset($_POST['name']) ? htmlspecialchars($_POST['name']) : '';
    $email = isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '';
    $phone = isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '';
    $price = isset($_POST['estimated_price']) ? htmlspecialchars($_POST['estimated_price']) : 'Not calculated';
    $city = isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '';
    $height = isset($_POST['tree_height']) ? htmlspecialchars($_POST['tree_height']) : 'Not specified';
    $diameter = isset($_POST['tree_diameter']) ? htmlspecialchars($_POST['tree_diameter']) : 'Not specified';
    
    // Get NEW form data - SERVICE DETAILS
    $serviceType = isset($_POST['service_type']) ? htmlspecialchars($_POST['service_type']) : 'Not specified';
    $treeCondition = isset($_POST['tree_condition']) ? htmlspecialchars($_POST['tree_condition']) : 'Not specified';
    $proximity = isset($_POST['proximity']) ? htmlspecialchars($_POST['proximity']) : 'Not specified';
    $timeline = isset($_POST['timeline']) ? htmlspecialchars($_POST['timeline']) : 'Not specified';
    
    // Format displays
    if (!empty($city)) {
        $city_display = ucwords(str_replace('-', ' ', $city));
    } else {
        $city_display = 'Not selected';
    }
    
    // Format service type display
    $serviceTypeDisplay = ucwords(str_replace('-', ' ', $serviceType));
    $treeConditionDisplay = ucwords(str_replace('-', ' ', $treeCondition));
    $proximityDisplay = ucwords(str_replace('-', ' ', $proximity));
    $timelineDisplay = ucwords(str_replace('-', ' ', $timeline));
    
    // Log what we received (for debugging)
    $debug_log = "\n=== FORM SUBMISSION " . date('Y-m-d H:i:s') . " ===\n";
    $debug_log .= "Name: $name\n";
    $debug_log .= "Email: $email\n";
    $debug_log .= "Phone: $phone\n";
    $debug_log .= "Price: $price\n";
    $debug_log .= "City: $city_display\n";
    $debug_log .= "Height: $height\n";
    $debug_log .= "Diameter: $diameter\n";
    $debug_log .= "Service Type: $serviceTypeDisplay\n";
    $debug_log .= "Tree Condition: $treeConditionDisplay\n";
    $debug_log .= "Proximity: $proximityDisplay\n";
    $debug_log .= "Timeline: $timelineDisplay\n";
    $debug_log .= "=====================================\n";
    @file_put_contents('form-debug.txt', $debug_log, FILE_APPEND);
    
    // Email setup
    $to = "dominicmadridseo@gmail.com";
    $subject = "💰 NEW LEAD: $price - $city_display - $timelineDisplay";
    
    // Email message (plain text, no fancy formatting)
    $message = "=============================================
NEW TREE REMOVAL QUOTE REQUEST
=============================================

ESTIMATED PRICE: $price

---------------------------------------------
CUSTOMER INFORMATION
---------------------------------------------
Name:     $name
Email:    $email
Phone:    $phone

---------------------------------------------
SERVICE DETAILS
---------------------------------------------
Service Type:   $serviceTypeDisplay
Location:       $city_display
Timeline:       $timelineDisplay

Tree Height:    $height
Tree Diameter:  $diameter
Tree Condition: $treeConditionDisplay
Proximity:      $proximityDisplay

---------------------------------------------
SUBMITTED
---------------------------------------------
Date/Time: " . date('F j, Y \a\t g:i a') . "

=============================================

ACTION REQUIRED: Contact within 2 hours

This customer is ready for contractor quotes.
Reply to this email to respond to customer.

Lead from: nmtreeremoval.com/calculator/
";
    
    // SAME HEADERS AS TRACK-CALCULATION (which works!)
    $headers = "From: noreply@nmtreeremoval.com\r\n";
    
    // Send email
    $mail_sent = mail($to, $subject, $message, $headers);
    
    // Log the result WITH MORE DETAIL
    $log_entry = date('Y-m-d H:i:s') . " | ";
    $log_entry .= $mail_sent ? "EMAIL-SUCCESS" : "EMAIL-FAILED";
    $log_entry .= " | $name | $email | $phone | $city_display | $price | $serviceTypeDisplay | $timelineDisplay\n";
    @file_put_contents('leads-log.txt', $log_entry, FILE_APPEND);
    
    // Also log if email FAILED
    if (!$mail_sent) {
        $error_log = date('Y-m-d H:i:s') . " | EMAIL SEND FAILED | To: $to | Subject: $subject\n";
        @file_put_contents('email-errors.txt', $error_log, FILE_APPEND);
    }
    
    // Redirect to thank you page
    header("Location: /thank-you.html");
    exit;
}
?>