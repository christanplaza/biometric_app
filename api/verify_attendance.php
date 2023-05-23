<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Usage example
$url = "https://api.semaphore.co/api/v4/messages";

function sendPostRequest($url, $postData)
{
    // Create a new cURL resource
    $curl = curl_init();

    // Set the URL and options
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    // Execute the request
    $response = curl_exec($curl);

    // Close cURL resource
    curl_close($curl);

    // Handle the response
    if ($response === false) {
        // Request failed
        echo "Error: " . curl_error($curl);
    } else {
        // Request succeeded
        echo "Response: " . $response;
    }
}

// Get unique string from API request
$unique_string = $_POST['unique_string'];

// Decode base64-encoded unique string
$decoded_string = base64_decode($unique_string);

// Parse student ID, class ID, and date from decoded string
$decoded_array = explode('|', $decoded_string);
$student_id = $decoded_array[0];
$class_id = $decoded_array[1];
$date = $decoded_array[2];

// Get current server time
$current_time = date('H:i:s');
// Get the current day
$current_day = date("l");

// Check if there is a class with the given ID that matches the current day and time
$sql = "SELECT c.id, c.title, c.description, u.first_name, u.last_name
        FROM classes c
        JOIN schedules s ON c.id = s.class_id
        JOIN users u ON c.teacher_id = u.id
        WHERE c.id = '$class_id'
        AND s.day_of_week = DAYNAME(CURRENT_DATE())
        AND s.time_start <= '$current_time'
        AND s.time_end >= '$current_time'
        LIMIT 1";

$result = mysqli_query($conn, $sql);

if ($result) {
    $class = mysqli_fetch_assoc($result);
    if ($class) {
        // Check if student is enrolled in the class with the given ID
        $sql = "SELECT *
                FROM enrollments
                WHERE student_id = '$student_id'
                AND class_id = '$class_id'
                AND enrollment_end >= CURDATE()
                AND status = 'active'";

        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            // Student is enrolled in the class with the given ID and the class schedule matches the current day and time

            // Get the Schedule
            // Fetch the schedule that matches the current time and date
            $sql = "SELECT * FROM schedules WHERE class_id = $class_id AND day_of_week = DAYNAME(CURRENT_DATE()) AND '$current_time' BETWEEN time_start AND time_end";
            $result = mysqli_query($conn, $sql);

            if (!$result) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Schedule not found.',
                );
            } else {
                // Check if a schedule was found
                if ($result->num_rows > 0) {
                    $schedule = $result->fetch_assoc();
                    $schedule_id = $schedule["id"];

                    $sql = "SELECT * FROM attendance WHERE student_id = $student_id AND class_id = $class_id AND schedule_id = $schedule_id AND DAYNAME(created_at) = '$current_day'";
                    $result = mysqli_query($conn, $sql);

                    // Check for errors
                    if (!$result) {
                        $response = array(
                            'status' => 'error',
                            'message' => 'Error verifying attendance.'
                        );
                    } else {
                        // Check if an attendance record already exists
                        if ($result->num_rows > 0) {
                            $response = array(
                                'status' => 'success',
                                'message' => 'QR already used.'
                            );
                        } else {
                            $sql = "INSERT INTO attendance (student_id, class_id, schedule_id, status) VALUES ($student_id, $class_id, $schedule_id, 'present')";
                            $result = mysqli_query($conn, $sql);

                            // Check for errors
                            if (!$result) {
                                $response = array(
                                    'status' => 'error',
                                    'message' => 'Error verifying attendance.'
                                );
                            } else {
                                $sql = "SELECT * FROM users WHERE student_id = '$student_id' LIMIT 1";
                                $result = mysqli_query($conn, $sql);
                                if (mysqli_num_rows($result) > 0) {
                                    $parent = $result->fetch_assoc();
                                    $last_name = $parent['last_name'];
                                    $sql = "SELECT * FROM users WHERE id = '$student_id' LIMIT 1";
                                    $student_res = mysqli_query($conn, $sql);
                                    $student = $student_res->fetch_assoc();
                                    $first_name = $student['first_name'];
                                    $class_title = $class['title'];
                                    $date_time = date('m-d-Y h:i A');

                                    $msg = "Good day Mrs/Mr. $last_name, \n";
                                    $msg .= "$first_name has logged-in for his/her $class_title class at $date_time.\n";
                                    $msg .= "Thank you! This is a generated text; there is no need to reply.";


                                    $postData = array(
                                        'apikey' => $apiKey,
                                        'number' => $parent['phone_number'],
                                        'message' => $msg
                                    );

                                    $response = array(
                                        'postData' => $postData,
                                        'parent' => $parent,
                                        'student' => $student
                                    );

                                    sendPostRequest($url, $postData);
                                }

                                $response = array(
                                    'status' => 'success',
                                    'message' => 'QR Valid.',
                                    'class' => $class
                                );
                            }
                        }
                    }
                } else {
                    $response = array(
                        'status' => 'error',
                        'message' => 'Schedule not found.',
                    );
                }
            }
        } else {
            // Student is not enrolled in the class with the given ID or the enrollment has ended or is inactive
            $response = array(
                'status' => 'error',
                'message' => 'Student is not enrolled in the class with the given ID or the enrollment has ended or is inactive.'
            );
        }
    } else {
        // No class with the given ID has a schedule that matches the current day and time
        $response = array(
            'status' => 'error',
            'message' => 'No class with the given ID has a schedule that matches the current day and time.'
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
