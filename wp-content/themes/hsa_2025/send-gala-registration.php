<?php
/**
 * Gala Registration Form Email Handler
 * Sends registration details to admin@hispanicsociety.org
 */

// SMTP Configuration
add_action('phpmailer_init', 'configure_smtp');
function configure_smtp($phpmailer) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = 'server239.web-hosting.com';  // SMTP host
    $phpmailer->SMTPAuth   = true;
    $phpmailer->Port       = 465;                          // Port (465 for SSL)
    $phpmailer->Username   = 'admin@hispanicsociaty.org';  // SMTP username
    $phpmailer->Password   = 'admin@hispanicsociaty.org';  // SMTP password
    $phpmailer->SMTPSecure = 'ssl';                        // Encryption: 'ssl' for port 465
    $phpmailer->From       = 'admin@hispanicsociaty.org';
    $phpmailer->FromName   = 'Hispanic Society Website';
}

// Set headers for JSON response
header('Content-Type: application/json');

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data from request
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate required fields
$required_fields = ['full_name', 'email', 'phone', 'ticket_type'];
foreach ($required_fields as $field) {
    if (empty($data[$field])) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required field: ' . $field]);
        exit;
    }
}

// Sanitize inputs
$full_name = sanitize_text_field($data['full_name']);
$email = sanitize_email($data['email']);
$phone = sanitize_text_field($data['phone']);
$organization = sanitize_text_field($data['organization'] ?? 'N/A');
$ticket_type = sanitize_text_field($data['ticket_type']);
$quantity = sanitize_text_field($data['quantity']);
$additional_donation = sanitize_text_field($data['additional_donation'] ?? '$0.00');
$total_amount = sanitize_text_field($data['total_amount']);
$attendee_names = sanitize_textarea_field($data['attendee_names'] ?? 'N/A');
$special_requests = sanitize_textarea_field($data['special_requests'] ?? 'N/A');

// Email configuration
$to = 'admin@hispanicsociety.org';
$subject = '2025 Gala Registration - ' . $full_name;

// Email body
$message = "New Gala Registration Submission\n";
$message .= "================================\n\n";
$message .= "Contact Information:\n";
$message .= "-------------------\n";
$message .= "Name: " . $full_name . "\n";
$message .= "Email: " . $email . "\n";
$message .= "Phone: " . $phone . "\n";
$message .= "Organization: " . $organization . "\n\n";
$message .= "Ticket Selection:\n";
$message .= "----------------\n";
$message .= "Ticket Type: " . $ticket_type . "\n";
$message .= "Quantity: " . $quantity . "\n";
$message .= "Additional Donation: $" . $additional_donation . "\n";
$message .= "Total Amount: " . $total_amount . "\n\n";
$message .= "Attendee Information:\n";
$message .= "--------------------\n";
$message .= $attendee_names . "\n\n";
$message .= "Special Requests/Dietary Restrictions:\n";
$message .= "-------------------------------------\n";
$message .= $special_requests . "\n\n";
$message .= "-------------------\n";
$message .= "Submitted: " . date('F j, Y, g:i a') . "\n";
$message .= "IP Address: " . $_SERVER['REMOTE_ADDR'] . "\n";

// Email headers
$headers = array(
    'From: Hispanic Society Website <noreply@hispanicsociety.org>',
    'Reply-To: ' . $email,
    'Content-Type: text/plain; charset=UTF-8'
);

// Send email
$sent = wp_mail($to, $subject, $message, $headers);

if ($sent) {
    // Also send confirmation email to registrant
    $confirmation_subject = '2025 Gala Registration Confirmation';
    $confirmation_message = "Dear " . $full_name . ",\n\n";
    $confirmation_message .= "Thank you for your interest in the 2025 Hispanic Society Gala!\n\n";
    $confirmation_message .= "We have received your registration request with the following details:\n\n";
    $confirmation_message .= "Ticket Type: " . $ticket_type . "\n";
    $confirmation_message .= "Quantity: " . $quantity . "\n";
    $confirmation_message .= "Total Amount: " . $total_amount . "\n\n";
    $confirmation_message .= "A member of our team will contact you shortly with payment instructions and additional event details.\n\n";
    $confirmation_message .= "Event Details:\n";
    $confirmation_message .= "Date: Thursday, 30 October 2025\n";
    $confirmation_message .= "Cocktails: 7:00pm | Dinner: 8:00pm\n";
    $confirmation_message .= "Location: The Metropolitan Club, One East 60th Street, New York City\n";
    $confirmation_message .= "Dress Code: Black Tie\n\n";
    $confirmation_message .= "If you have any questions, please don't hesitate to contact us at admin@hispanicsociety.org\n\n";
    $confirmation_message .= "Best regards,\n";
    $confirmation_message .= "The Hispanic Society Team\n";

    $confirmation_headers = array(
        'From: Hispanic Society <admin@hispanicsociety.org>',
        'Content-Type: text/plain; charset=UTF-8'
    );

    wp_mail($email, $confirmation_subject, $confirmation_message, $confirmation_headers);

    echo json_encode([
        'success' => true,
        'message' => 'Registration submitted successfully'
    ]);
} else {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Failed to send email'
    ]);
}

