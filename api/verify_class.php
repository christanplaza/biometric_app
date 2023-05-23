<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get student ID from API request
$student_id = $_POST['student_id'];

// Get current day and time
$current_day_of_week = date('l');
$current_time = date('H:i:s');

// Get the class that the student is currently enrolled in and has a schedule that matches the current day and time
$sql = "SELECT c.id, c.title, c.description, u.first_name, u.last_name
        FROM enrollments e
        JOIN classes c ON e.class_id = c.id
        JOIN schedules s ON c.id = s.class_id
        JOIN users u ON c.teacher_id = u.id
        WHERE e.student_id = '$student_id'
        AND e.enrollment_end >= CURDATE()
        AND e.status = 'active'
        AND s.day_of_week = '$current_day_of_week'
        AND s.time_start <= '$current_time'
        AND s.time_end >= '$current_time'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result) {
    $class = mysqli_fetch_assoc($result);
    if ($class) {
        $date = date('Y-m-d');
        $unique_string = base64_encode($student_id . '|' . $class['id'] . '|' . $date);
        
        // Student is currently enrolled in a class that matches the current day and time
        // Return class details as JSON response
        $response = array(
            'status' => 'success',
            'message' => 'Student is currently enrolled in a class.',
            'class' => $class,
            'verify' => $unique_string
        );
    } else {
        // Student is not currently enrolled in any classes that match the current day and time
        $response = array(
            'status' => 'error',
            'message' => 'Student is not currently enrolled in any classes that match the current day and time.',
        );
    }
} else {
    // Error executing SQL query
    $response = array(
        'status' => 'error',
        'message' => 'Error executing SQL query.'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
