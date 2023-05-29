<?php
session_start();
include('../../../config.php');
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {

    // Check if there is a row in board_mode
    $sql = "SELECT * FROM board_mode LIMIT 1";
    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $mode = $row['mode'];

        if ($mode == "enrollment") {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
                $sql = "SELECT * FROM users WHERE fingerprint_id <> 0 AND id = '$id'";

                $users_res = mysqli_query($conn, $sql);

                if (isset($_POST['submit'])) {
                    $fingerprint_id = $_POST['fingerprint_id'];

                    if ($fingerprint_id < 129 && $fingerprint_id > 0) {
                        $sql = "SELECT * FROM users WHERE fingerprint_id = '$fingerprint_id'";

                        $fingerprint_matches_results = mysqli_query($conn, $sql);

                        if (mysqli_num_rows($fingerprint_matches_results) > 0) {
                            $_SESSION['msg_type'] = 'danger';
                            $_SESSION['flash_message'] = 'This ID is already used. Try another one.';
                            header("Refresh: 0");
                            session_write_close();
                        } else {
                            $sql = "UPDATE users SET fingerprint_id = '$fingerprint_id' WHERE id = '$id'";

                            if (mysqli_query($conn, $sql)) {
                                $sql = "UPDATE fingerprint_enrollment SET selected_id = '$fingerprint_id', step = '1'";

                                if (mysqli_query($conn, $sql)) {
                                    $_SESSION['msg_type'] = 'success';
                                    $_SESSION['flash_message'] = 'Fingerprint ID Assigned to user, restart NodeMCU to begin Enrolling.';
                                    header("location: $rootURL/admin/fingerprint_management/enroll_fingerprint.php");
                                    session_write_close();
                                } else {
                                    $_SESSION['msg_type'] = 'danger';
                                    $_SESSION['flash_message'] = 'Something went wrong';
                                    header("location: $rootURL/admin/fingerprint_management.php");
                                    session_write_close();
                                }
                            } else {
                                $_SESSION['msg_type'] = 'danger';
                                $_SESSION['flash_message'] = 'Something went wrong';
                                header("location: $rootURL/admin/fingerprint_management.php");
                                session_write_close();
                            }
                        }
                    } else {
                        $_SESSION['msg_type'] = 'danger';
                        $_SESSION['flash_message'] = 'This Sensor can only record up to 128 Fingerprints.';
                        header("Refresh: 0");
                        session_write_close();
                    }
                }
            }
        } else {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['flash_message'] = 'Set Board Mode to Enrollment';
            header("location: $rootURL/admin/fingerprint_management.php");
            session_write_close();
        }
    }
} else {
    echo "Couldn't connect to database.";
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
            <div class="row justify-content-center">
                <!-- <div class="col-4">
                    <?php include_once "components/panel.php" ?>
                </div> -->
                <div class="col-6">
                    <div class="card shadow">
                        <div class="card-body">
                            <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                                <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION["flash_message"]; ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            unset($_SESSION['msg_type']);
                            unset($_SESSION['flash_message']);
                            ?>
                            <div class="display-6">Registering a Fingerprint</div>
                            <div class="mt-4">
                                <form method="POST">
                                    <div class="form-floating mb-3">
                                        <input type="number" name="fingerprint_id" class="form-control" required>
                                        <label for="fingerprint_id">Fingerprint ID Registered</label>
                                    </div>
                                    <button class="w-100 btn btn-primary" type="submit" name="submit">Verify FingerprintID</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>