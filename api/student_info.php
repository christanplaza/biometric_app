<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get the current date and time
$current_date = date('Y-m-d');
$current_time = date('H:i:s');

// Check if student ID is set
if (isset($_GET['id'])) {
    // Get student ID from GET parameter
    $student_id = mysqli_real_escape_string($conn, $_GET['id']);

    // Query to fetch student information
    $sql = "SELECT first_name, last_name, unique_code FROM users WHERE id = '$student_id' LIMIT 1";
    $result = mysqli_query($conn, $sql);

    // Check if query was successful
    if ($result) {
        // Check if student was found
        if (mysqli_num_rows($result) > 0) {
            // Fetch student information
            $row = mysqli_fetch_assoc($result);

            $student = $row;

            // Query to check if the student has any ongoing classes
            $sql = "SELECT c.id, c.title, ca.text AS announcement
                    FROM classes c
                    JOIN schedules s ON c.id = s.class_id
                    JOIN class_announcements ca ON c.id = ca.class_id
                    WHERE s.day_of_week = DAYNAME('$current_date')
                    AND EXISTS (
                        SELECT 1
                        FROM enrollments e
                        WHERE e.student_id = '$student_id' AND e.class_id = c.id
                    )";

            $class_result = mysqli_query($conn, $sql);
            $announcement_msg = "";

            if ($class_result) {
                while ($row = mysqli_fetch_assoc($class_result)) {
                    $class_id = $row['id'];
                    $class_title = $row['title'];
                    $announcement = $row['announcement'];

                    $msg = "$class_title: \n $announcement\n\n";
                    $announcement_msg .= $msg;
                }

                // Build response object
                $response = array(
                    'status' => 'success',
                    'first_name' => $student['first_name'],
                    'last_name' => $student['last_name'],
                    'unique_code' => $student['unique_code'],
                    'msg' => $announcement_msg
                );
            }
        } else {
            // Student not found
            $response = array(
                'status' => 'error',
                'message' => 'Student not found'
            );
        }
    } else {
        // Query failed
        $response = array(
            'status' => 'error',
            'message' => 'Query failed: ' . mysqli_error($conn)
        );
    }
} else {
    // Student ID not set
    $response = array(
        'status' => 'error',
        'message' => 'Student ID not set'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
