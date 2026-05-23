<?php
// This is a simple API endpoint to demonstrate AJAX
header('Content-Type: application/json');

// Read JSON input from the fetch request
$data = json_decode(file_get_contents('php://input'), true);
$email_to_check = isset($data['email']) ? trim($data['email']) : '';

// Dummy database of already registered emails
$dummy_database = [
    'admin@test.com',
    'pratik@test.com',
    'hello@world.com',
    'test@example.com'
];

$response = ['exists' => false];

// Check if the provided email exists in our dummy database (case-insensitive)
foreach ($dummy_database as $existing_email) {
    if (strtolower($existing_email) === strtolower($email_to_check)) {
        $response['exists'] = true;
        break;
    }
}

// Return the result as JSON
echo json_encode($response);
?>