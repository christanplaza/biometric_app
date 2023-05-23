<?php
session_start();
include('../../../config.php');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if form was submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name']);
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm_password']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Validate inputs
    if (empty($first_name) || empty($last_name) || empty($username) || empty($password) || empty($confirm_password) || empty($email)) {
        $error_message = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } elseif ($password != $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Hash password
        $hashed_password = md5($password);

        // Insert new user into database
        $sql = "INSERT INTO users (first_name, last_name, username, password, email, role)
                VALUES ('$first_name', '$last_name', '$username', '$hashed_password', '$email', 'faculty')";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_type'] = 'success';
            $_SESSION['flash_message'] = 'Faculty Member added successfully!';
            header("location: $rootURL/admin/faculty_management.php");
            session_write_close();
        } else {
            $error_message = "Error: " . mysqli_error($conn);
        }
    }
}

include('../../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../../components/header.php"; ?>
    <title>Biometrics Monitoring | Admin</title>
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary shadow-lg">
        <div class="container">
            <a class="navbar-brand" href="#">Biometrics Monitoring</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                    </li>
                </ul>
                <div class="dropdown">
                    <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Menu
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#">About</a></li>
                        <li><a class="dropdown-item" href="#">Help</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li>
                            <form method="POST"><button type="submit" name="logout" class="dropdown-item" href="#">Logout</button></form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container">
        <div class="mt-5">
            <div class="row">
                <div class="col-4">
                    <?php include_once "../components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-header bg-secondary-subtle">Add Faculty</div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="firstNameInput" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="firstNameInput" name="first_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="lastNameInput" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="lastNameInput" name="last_name" required>
                                </div>
                                <div class="mb-3">
                                    <label for="usernameInput" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="usernameInput" name="username" required>
                                </div>
                                <div class="mb-3">
                                    <label for="passwordInput" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="passwordInput" name="password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirmPasswordInput" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirmPasswordInput" name="confirm_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="emailInput" class="form-label">Email</label>
                                    <input type="email" class="form-control" id="emailInput" name="email" required>
                                </div>
                                <!-- <div class="mb-3">
                                    <label for="profilePictureInput" class="form-label">Profile Picture</label>
                                    <input type="file" class="form-control" id="profilePictureInput" name="profile_picture">
                                </div> -->
                                <div class="text-end">
                                    <button type="submit" class="btn btn-success">Add Faculty</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>