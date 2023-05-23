<?php
include('../../config.php');
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Get form data from API request
$first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
$last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
$username = mysqli_real_escape_string($conn, $_POST['username']);
$phone_number = mysqli_real_escape_string($conn, $_POST['phone_number']);
$password = md5(mysqli_real_escape_string($conn, $_POST['password'])); // Hash the password
$role = mysqli_real_escape_string($conn, $_POST['role']);

// Check if username already exists
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    // Return error message if username already exists
    $response = array(
        'status' => 'error',
        'message' => 'Username already exists'
    );
} else {
    // Generate unique code for students
    $unique_code = '';
    if ($role == 'student') {
        $unique_code = substr(strtoupper(md5(uniqid($last_name . $first_name, true))), 0, 6);
    }

    // Insert new user to database
    $sql = "INSERT INTO users (first_name, last_name, username, phone_number, password, role, unique_code) VALUES ('$first_name', '$last_name', '$username', '$phone_number', '$password', '$role', '$unique_code')";
    if (mysqli_query($conn, $sql)) {
        $response = array(
            'status' => 'success',
            'message' => 'Account created successfully'
        );
    } else {
        $response = array(
            'status' => 'error',
            'message' => 'Account creation failed'
        );
    }
}

// Return JSON response
header('Content-Type: application/json');
echo json_encode($response);
