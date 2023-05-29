<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if there is a row in board_mode
$sql = "SELECT * FROM board_mode LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $mode = $row['mode'];

    $response = array(
        'status' => 'success',
        'message' => 'Row found in board_mode',
        'mode' => $mode
    );
} else {
    $response = array(
        'status' => 'error',
        'message' => 'No row found in board_mode'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
