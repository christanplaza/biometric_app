<?php
session_start();
include('../../../config.php');
$conn = mysqli_connect($host, $username, $password, $database); // Get the root URL
// Echo the value of $rootURL as a JavaScript variable
echo "<script>const rootURL = '$rootURL';</script>";

if ($conn) {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $sql = "SELECT * FROM users WHERE fingerprint_id <> 0 AND id = '$id'";

        $users_res = mysqli_query($conn, $sql);

        if (isset($_POST['submit'])) {
            $fingerprint_id = $_POST['fingerprint_id'];
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
                    $_SESSION['msg_type'] = 'success';
                    $_SESSION['flash_message'] = 'Fingerprint ID Assigned to user';
                    header("location: $rootURL/admin/fingerprint_management/enroll_fingerprint.php");
                    session_write_close();
                } else {
                    $_SESSION['msg_type'] = 'danger';
                    $_SESSION['flash_message'] = 'Something went wrong';
                    header("location: $rootURL/admin/fingerprint_management.php");
                    session_write_close();
                }
            }
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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to update the status badge
        function updateStatusBadge(status) {
            var badge = $('#status-badge');

            // Update the content based on the status
            if (status === 'ready') {
                badge.html('<span class="badge bg-success">Ready</span>');
            } else if (status === 'not ready') {
                badge.html('<span class="badge bg-danger">Not Ready</span>');
            } else {
                badge.html('');
            }
        }

        // Function to make the AJAX request and update the status
        function fetchStatus() {
            $.ajax({
                url: rootURL + "/api/get_board_status.php",
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Extract the status from the API response
                    var status = response.status_value;
                    var message = response.status_message;

                    // Update the status badge
                    updateStatusBadge(status);
                    var badge = $('#status-message');
                    badge.html(message);
                    if (message === "Registered") {
                        // Wait for 2 seconds before redirecting
                        setTimeout(function() {
                            window.location.href = rootURL + "/admin/fingerprint_management.php";
                        }, 2000);
                    }

                },
                error: function() {
                    console.log('Error occurred while fetching status.');
                }
            });
        }

        // Periodically fetch the status
        setInterval(fetchStatus, 1000); // Fetch status every 3 seconds
    </script>
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
                            <div class="display-6">Enrolling Fingerprint</div>
                            <p>Board Status:
                            <div id="status-badge"></div>
                            </p>
                            <div class="mt-4">
                                <p>Last Message from NodeMCU: </p>
                                <p id="status-message"></p>
                                <!-- <form method="POST">
                                    <div class="form-floating mb-3">
                                        <input type="number" name="fingerprint_id" class="form-control" required>
                                        <label for="fingerprint_id">Fingerprint ID Registered</label>
                                    </div>
                                    <button class="w-100 btn btn-primary" type="submit" name="submit">Verify FingerprintID</button>
                                </form> -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>