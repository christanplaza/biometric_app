<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get parent_id, excuse_date, note, and class_ids from API request
$parent_id = mysqli_real_escape_string($conn, $_POST['parent_id']);
$excuse_date = mysqli_real_escape_string($conn, $_POST['date']);
$note = mysqli_real_escape_string($conn, $_POST['note']);
$class_ids = explode(",", $_POST['class_ids']);
$image = mysqli_real_escape_string($conn, $_POST['image']);

// Get student_id from user table
$sql = "SELECT student_id FROM users WHERE id = '$parent_id' LIMIT 1";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $student_id = $row['student_id'];

    // Insert an excuse letter for each class
    foreach ($class_ids as $class_id) {
        $sql = "INSERT INTO excuse_letters (parent_id, student_id, class_id, excuse_date, note, image_path) VALUES ('$parent_id', '$student_id', '$class_id', '$excuse_date', '$note', '$image')";
        if (mysqli_query($conn, $sql)) {
            $response = array(
                'status' => 'success',
                'message' => 'Excuse letter submitted successfully',
                'sql' => $sql,
                'ids' => $_POST['class_ids'],
                'image' => $_POST['image'],
            );
        } else {
            $response = array(
                'status' => 'error',
                'message' => 'Error submitting excuse letter'
            );
        }
    }
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
