<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get parent_id and excuse_date from API request
$parent_id = mysqli_real_escape_string($conn, $_GET['parent_id']);
$excuse_date = mysqli_real_escape_string($conn, $_GET['excuse_date']);

// Find user with parent_id
$sql = "SELECT * FROM users WHERE id = '$parent_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Get student_id from result
    $row = mysqli_fetch_assoc($result);
    $student_id = $row['student_id'];

    // Get student's schedule on excuse_date
    $sql = "SELECT c.title, c.id
        FROM schedules s 
        JOIN classes c ON s.class_id = c.id 
        JOIN enrollments sc ON s.class_id = sc.class_id 
        WHERE s.day_of_week = DAYNAME('$excuse_date') 
        AND sc.student_id = '$student_id'";
    $result = mysqli_query($conn, $sql);

    $classes = array();
    while ($row = mysqli_fetch_assoc($result)) {
        $classes[] = array('title' => $row['title'], 'id' => $row['id']);
    }

    $response = array(
        'status' => 'success',
        'classes' => $classes,
    );
} else {
    // Return error message if no parent was found
    $response = array(
        'status' => 'error',
        'message' => 'Invalid Parent ID'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
