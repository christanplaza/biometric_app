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
$authState = $payload['authState'];
$fingerprintId = $payload['fingerprint_id'];
$roomName = $payload['room_name'];


// Insert the data into the access_logs table
$sql = "INSERT INTO access_logs (fingerprint_id, state, room_name, datetime) VALUES ('$fingerprintId', '$authState', '$roomName', NOW())";
$result = mysqli_query($conn, $sql);

if ($result) {
    $response = array(
        'status' => 'success',
        'message' => 'Data saved successfully'
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
