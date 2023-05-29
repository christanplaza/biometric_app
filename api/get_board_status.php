<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if there is a row in board_status
$sql = "SELECT status, message FROM board_status LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $status = $row['status'];
    $message = $row['message'];

    $response = array(
        'status' => 'success',
        'message' => 'Status retrieved successfully',
        'status_value' => $status,
        'status_message' => $message
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'No rows found in board_status'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
