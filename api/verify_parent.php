<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get student code from API request
$student_code = mysqli_real_escape_string($conn, $_POST['student_code']);

$sql = "SELECT id, first_name, last_name FROM users WHERE unique_code = '$student_code' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Get student_id from result
    $row = mysqli_fetch_assoc($result);
    $student_id = $row['id'];

    $sql = "SELECT COUNT(*) as cnt FROM users WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $sql);
    $student_count = mysqli_fetch_assoc($result);

    if ($student_count['cnt'] > 0) {
        // Student is already paired with a parent, return error message
        $response = array(
            'status' => 'warning',
            'message' => 'Student is already paired with a parent',
            'student' => $row
        );
    } else {
        // Return success message
        $response = array(
            'status' => 'success',
            'message' => 'Valid Student Code',
            'student' => $row
        );
    }
} else {
    // Return error message if no student was found
    $response = array(
        'status' => 'error',
        'message' => 'Invalid student code'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
