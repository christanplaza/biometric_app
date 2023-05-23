<?php
include("../config.php");
session_start();

if (isset($_POST['submit'])) {
    if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['password_confirm'])) {
        if ($_POST['password'] === $_POST['password_confirm']) {
            $conn = mysqli_connect($host, $username, $password, $database);

            $first_name = $_POST['first_name'];
            $last_name = $_POST['last_name'];
            $username = $_POST['username'];
            $password = md5($_POST['password']);
            $role = 'student';

            if ($conn) {
                $sql = "SELECT * FROM users WHERE username = '$username'";

                $res = mysqli_query($conn, $sql);

                if (mysqli_num_rows($res) != 0) {
                    $result = array("status" => "danger", "message" => "Username already Exists");
                } else {
                    $sql = "INSERT into users (first_name, last_name, role, username, password) values ('$first_name', '$last_name', '$role', '$username', '$password')";

                    if (mysqli_query($conn, $sql)) {
                        $user_id = mysqli_insert_id($conn);

                        // concatenate user ID and name
                        $name = $user_id . $last_name . $first_name;

                        // Generate a unique code using md5 and the user info
                        $unique_code = substr(md5($name), 0, 6);

                        // Update the user row with the unique code
                        $sql = "UPDATE users SET unique_code = '$unique_code' WHERE id = $user_id";

                        mysqli_query($conn, $sql);

                        $result = array("status" => "success", "message" => "Account Created");
                    } else {
                        $result = array("status" => "danger", "message" => "Registration Failed");
                    }
                }
            } else {
                $result = array("status" => "danger", "message" => "Database connection failed.");
            }
        } else {
            $result = array("status" => "danger", "message" => "Password is not the same");
        }
    } else {
        $result = array("status" => "danger", "message" => "All fields are required");
    }

    $_SESSION['msg_type'] = $result['status'];
    $_SESSION['flash_message'] = $result['message'];

    if ($result['status'] == "danger") {
        header("location: $rootURL/register.php");
    } else {
        header("location: $rootURL/index.php");
    }
    session_write_close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courier App | Login</title>
    <!-- CSS only -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
</head>

<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-4">
                <div class="card">
                    <div class="card-body text-center form-signin">
                        <h1 class="h3 mb-3 fw-normal">Create Account</h1>
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
                                <input type="text" name="first_name" class="form-control" placeholder="Juan" required>
                                <label for="floatingInput">First </label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="last_name" class="form-control" placeholder="Dela Cruz" required>
                                <label for="floatingInput">Last Name</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="text" name="username" class="form-control" placeholder="johndoe27" required>
                                <label for="floatingInput">Username</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" name="password_confirm" class="form-control" placeholder="password_confirm" required>
                                <label for="password">Confirm Password</label>
                            </div>
                            <button class="w-100 btn btn-lg btn-primary" type="submit" name="submit">Create Account</button>
                            <div class="mb-3">
                                <p>Already have an account? <a href="index.php">Login here.</a></p>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>