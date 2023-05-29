<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if there is a row in fingerprint_enrollment
$sql = "SELECT * FROM fingerprint_enrollment LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $selected_id = $row['selected_id'];

    $response = array(
        'status' => 'success',
        'message' => 'Row found in fingerprint_enrollment',
        'selected_id' => $selected_id
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'No row found in fingerprint_enrollment'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
