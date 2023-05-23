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

// Get student ID from API request
$teacher_id = $_POST['teacher_id'];

// Get current day and time
$current_day_of_week = date('l');
$current_time = date('H:i:s');

$sql = "SELECT c.id AS class_id, c.title, s.id AS schedule_id
        FROM classes c
        JOIN schedules s ON c.id = s.class_id
        WHERE c.teacher_id = '$teacher_id'
        AND s.day_of_week = '$current_day_of_week'
        AND TIME('$current_time') BETWEEN s.time_start AND s.time_end LIMIT 1;";

$result = mysqli_query($conn, $sql);

if ($result) {
    $class = $result->fetch_assoc();
    $class_id = $class['class_id'];
    $schedule_id = $class['schedule_id'];
    $current_date = date('Y-m-d');

    $sql = "SELECT u.id, u.first_name, u.last_name
            FROM users u
            WHERE u.role = 'student'
            AND u.id NOT IN (
                SELECT a.student_id
                FROM attendance a
                WHERE a.class_id = '$class_id'
                AND a.schedule_id = '$schedule_id'
                AND DATE(a.created_at) >= DATE(DATE_SUB('$current_date', INTERVAL WEEKDAY('$current_date') DAY))
                AND DATE(a.created_at) <= DATE(DATE_ADD('$current_date', INTERVAL (6 - WEEKDAY('$current_date')) DAY))
            )";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $students = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $students[] = $row;
        }

        foreach ($students as $student) {
            $student_id = $student['id'];
            $sql = "INSERT INTO attendance (student_id, class_id, schedule_id, status) VALUES ($student_id, $class_id, $schedule_id, 'absent')";

            $result = mysqli_query($conn, $sql);

            if (!$result) {
                $response = array(
                    'status' => 'error',
                    'message' => 'Error executing SQL query.'
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
                    $msg .= "$first_name has been marked absent for his/her $class_title class at $date_time.\n";
                    $msg .= "Thank you! This is a generated text; there is no need to reply.";

                    $postData = array(
                        'apikey' => $apiKey,
                        'number' => $parent['phone_number'],
                        'message' => $msg
                    );

                    sendPostRequest($url, $postData);
                }
                $response = array(
                    'status' => 'success',
                    'message' => 'Unscanned students are marked absent.'
                );
            }
        }
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'No one was absent.'
        );
    }
} else {
    $response = array(
        'status' => 'error',
        'message' => 'Error executing SQL query.'
    );
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
