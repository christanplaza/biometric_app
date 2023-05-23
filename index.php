<?php
session_start();
include('../config.php');

if (isset($_POST['submit'])) {
    if (isset($_POST['username']) && isset($_POST['password'])) {
        $conn = mysqli_connect($host, $username, $password, $database);

        if ($conn) {
            $username = $_POST['username'];
            $password = md5($_POST['password']);

            $sql = "SELECT * FROM users WHERE username = '$username'";

            $res = mysqli_query($conn, $sql);

            if (mysqli_num_rows($res) != 0) {
                $user = mysqli_fetch_assoc($res);

                if ($username == $user['username'] && $password == $user['password']) {
                    if (mysqli_query($conn, $sql)) {
                        $role = $user['role'];
                        $name = $user['name'];

                        setcookie("username", $username, time() + (86400), "/"); // 86400 = 1 day
                        setcookie("role", $role, time() + (86400), "/"); // 86400 = 1 day
                        setcookie("name", $name, time() + (86400), "/"); // 86400 = 1 day
                        setcookie("id", $user['id'], time() + (86400), "/"); // 86400 = 1 day
                        setcookie("logged_in", true, time() + (86400), "/"); // 86400 = 1 day

                        if ($role == "admin") {
                            header("location: $rootURL/admin/");
                        } else if ($role == "faculty") {
                            header("location: $rootURL/faculty/");
                        } else if ($role == "parent") {
                            header("location: $rootURL/parent/");
                        } else {
                            header("location: $rootURL/student/");
                        }
                    } else {
                        $result = array("status" => "failed", "message" => "Login failed, try again.");
                    }
                } else {
                    $result = array("status" => "failed", "message" => "User already exists.");
                }
            }
        } else {
            echo "Database connection failed.";
        }
    } else {
        echo "All fields are required";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Biometrics Monitoring | Login</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-4">
                <div class="card">
                    <div class="card-body text-center form-signin">
                        <h1 class="h3 mb-3 fw-normal">User Login</h1>
                        <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                            <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                                <?php echo $_SESSION["flash_message"]; ?>
                            </div>
                        <?php endif; ?>
                        <?php
                        unset($_SESSION['msg_type']);
                        unset($_SESSION['flash_message']);
                        ?>
                        <form method="POST">
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" placeholder="JohnDoe27" required>
                                <label for="floatingInput">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary" type="submit" name="submit">Login</button>
                            <div class="mt-3">
                                Don't have an account? <a href="register.php">Register Here.</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>