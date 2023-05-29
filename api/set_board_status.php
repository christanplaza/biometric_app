<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
header('Content-Type: application/json');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if the request is a POST request
// Extract the values from $_POST
// Retrieve the raw request body
$requestBody = file_get_contents('php://input');

// Parse the JSON payload
$payload = json_decode($requestBody, true);

// Access the values from the payload
$status = $payload['status'];
$message = $payload['message'];

// Insert the data into the access_logs table
$sql = "UPDATE board_status SET status = '$status', message = '$message' WHERE id = 1";
$result = mysqli_query($conn, $sql);

if ($result) {
    $response = array(
        'status' => 'success',
        'message' => 'Status Updated'
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Failed to save data'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
