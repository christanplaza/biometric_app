<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if the connection to the database is successful
if (!$conn) {
    $response = array(
        'status' => 'error',
        'message' => 'Failed to connect to the database'
    );
    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Get the teacher_id from the POST request
$teacher_id = $_POST['teacher_id'];

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Query to check if the teacher has any class currently
$sql = "SELECT c.id, c.title, s.id AS schedule_id, s.day_of_week, s.time_start, s.time_end
        FROM classes c
        INNER JOIN schedules s ON c.id = s.class_id
        WHERE c.teacher_id = '$teacher_id'
        AND s.day_of_week = DAYNAME('$current_date')
        AND '$current_time' BETWEEN s.time_start AND s.time_end";

$result = mysqli_query($conn, $sql);

if ($result) {
    // Check if the teacher has any class currently
    if (mysqli_num_rows($result) > 0) {
        // Fetch the class details from the result
        $class = mysqli_fetch_assoc($result);

        // Prepare the response
        $response = array(
            'status' => 'success',
            'id' => $class['id'],
            'title' => $class['title'],
            'schedule_id' => $class['schedule_id'],
            'day_of_week' => $class['day_of_week'],
            'time_start' => $class['time_start'],
            'time_end' => $class['time_end']
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Teacher does not have any class currently'
        );
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Error executing SQL query'
    );
}

// Return the JSON response
header('Content-Type: application/json');
echo json_encode($response);
