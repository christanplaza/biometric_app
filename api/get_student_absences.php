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

// Get the student_id from the POST request
$student_id = $_GET['student_id'];

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Query to check if the student has any ongoing classes
$sql = "SELECT c.id, c.title, s.id AS schedule_id, s.day_of_week, s.time_start, s.time_end, c.access_limit
        FROM classes c
        INNER JOIN schedules s ON c.id = s.class_id
        WHERE EXISTS (
            SELECT 1
            FROM enrollments e
            WHERE e.student_id = '$student_id'
            AND e.class_id = c.id
        )
        AND s.day_of_week = DAYNAME('$current_date')
        AND '$current_time' BETWEEN s.time_start AND s.time_end";

$result = mysqli_query($conn, $sql);

if ($result) {
    // Check if the student has any ongoing classes
    if (mysqli_num_rows($result) > 0) {
        // Fetch the class details from the result
        $class = mysqli_fetch_assoc($result);
        $class_id = $class['id'];

        // Query to count the number of 'absent' attendance records for the student in the class
        $attendanceSql = "SELECT COUNT(*) AS absent_count
                          FROM attendance
                          WHERE student_id = '$student_id'
                          AND class_id = '$class_id'
                          AND status = 'absent'";

        $attendanceResult = mysqli_query($conn, $attendanceSql);
        $attendanceData = mysqli_fetch_assoc($attendanceResult);
        $absentCount = $attendanceData['absent_count'];

        // Calculate the remaining absences before reaching the absence limit
        $remainingAbsences = $class['access_limit'] - $absentCount;

        // Prepare the response
        $response = array(
            'status' => 'success',
            'id' => $class['id'],
            'title' => $class['title'],
            'schedule_id' => $class['schedule_id'],
            'day_of_week' => $class['day_of_week'],
            'time_start' => $class['time_start'],
            'time_end' => $class['time_end'],
            'access_limit' => $class['access_limit'],
            'absent_count' => $absentCount,
            'remaining_absences' => $remainingAbsences
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Student does not have any ongoing classes'
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
