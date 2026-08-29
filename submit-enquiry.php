<?php
header('Content-Type: application/json');

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
    exit;
}

require_once __DIR__ . '/db.php';

// Retrieve and sanitize inputs
$name = isset($_POST['name']) ? trim(strip_tags($_POST['name'])) : '';
$phone = isset($_POST['phone']) ? trim(strip_tags($_POST['phone'])) : '';
$email = isset($_POST['email']) ? trim(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)) : '';
$service = isset($_POST['service']) ? trim(strip_tags($_POST['service'])) : 'General Enquiry';
$message = isset($_POST['message']) ? trim(strip_tags($_POST['message'])) : '';

// Validation
if (empty($name) || empty($phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Full Name and Phone Number are required fields.']);
    exit;
}

if (!preg_match('/^[0-9+() -]{10,18}$/', $phone)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid 10-digit phone number.']);
    exit;
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['status' => 'error', 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Insert into database
    $stmt = $pdo->prepare("INSERT INTO enquiries (name, phone, email, service, message, status) VALUES (?, ?, ?, ?, ?, 'Pending')");
    $stmt->execute([$name, $phone, $email ?: null, $service, $message ?: null]);
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Thank you! Your enquiry has been received. Our team will contact you shortly.'
    ]);
} catch (PDOException $e) {
    // Log error in production, show simple message to user
    echo json_encode([
        'status' => 'error',
        'message' => 'Database error: Unable to submit your enquiry at this time. Please try again later.'
    ]);
}
