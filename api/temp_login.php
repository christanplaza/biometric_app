<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$response = array(
    "success" => false,
    "message" => "Invalid request"
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        $hashed_password = md5($password);

        $conn = connect();
        $stmt = $conn->prepare('SELECT id, role FROM users WHERE username = ?');
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();
        return 'hit';

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            if ($hashed_password == $user['password']) {
                $response['success'] = true;
                $response['id'] = $id;
                $response['userRole'] = $role;
                $response['message'] = "Login successful";
            } else {
                $response['message'] = "Invalid password";
            }
        } else {
            $response['message'] = "User not found";
        }

        $stmt->close();
        $conn->close();
    } else {
        $response['message'] = "Missing username or password";
    }
}

echo json_encode($response);
