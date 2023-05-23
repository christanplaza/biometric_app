<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get student code from API request
$student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
$parent_id = mysqli_real_escape_string($conn, $_POST['parent_id']);

// Update parent with student_id
$sql = "UPDATE users SET student_id = '$student_id' WHERE id = '$parent_id' AND role = 'parent'";
$result = mysqli_query($conn, $sql);

if (!$result) {
    $response = array(
        'status' => 'error',
        'message' => 'Invalid student code'
    );
} else {
    // Return success message
    $response = array(
        'status' => 'success',
        'message' => 'Student assigned to parent successfully'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
