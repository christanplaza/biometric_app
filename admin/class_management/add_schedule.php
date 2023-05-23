<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT * FROM users WHERE role = 'faculty' ORDER BY last_name ASC";

    $faculties_res = mysqli_query($conn, $sql);

    if (isset($_POST['submit'])) {
        // Get form data
        $day_of_week = mysqli_real_escape_string($conn, $_POST['day_of_week']);
        $start_time = mysqli_real_escape_string($conn, $_POST['start_time']);
        $end_time = mysqli_real_escape_string($conn, $_POST['end_time']);
        $class_location = mysqli_real_escape_string($conn, $_POST['class_location']);

        // Get class_id from GET variable
        $class_id = $_GET['id'];

        // Basic validation to ensure all form values exist
        if (empty($day_of_week) || empty($start_time) || empty($end_time) || empty($class_location) || empty($class_id)) {
            $_SESSION['msg_type'] = 'danger';
            $_SESSION['flash_message'] = 'Please fill out all fields.';
        }

        // Prepare SQL statement and execute with form data
        $sql = "INSERT INTO schedules (day_of_week, time_start, time_end, location, class_id) VALUES ('$day_of_week', '$start_time', '$end_time', '$class_location', '$class_id')";
        mysqli_query($conn, $sql);

        // Redirect to class schedule page
        $_SESSION['msg_type'] = 'success';
        $_SESSION['flash_message'] = 'Schedule Added';
        header("location: $rootURL/admin/class_management/class.php?id=" . $class_id);
        session_write_close();
    }
} else {
    echo "Couldn't connect to database.";
}
include('../../logout.php');
$days_of_week = array('Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday');

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
                        <div class="card-body">
                            <div class="display-6">Add a Schedule</div>
                            <div class="row mt-4">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="day_of_week" class="form-label">Day of Week</label>
                                        <select name="day_of_week" id="day_of_week" class="form-select" required>
                                            <option label="Select a Day"></option>
                                            <?php foreach ($days_of_week as $day) : ?>
                                                <option value="<?= $day ?>"><?= $day ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="start_time" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" name="start_time" id="start_time" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="end_time" class="form-label">End Time</label>
                                        <input type="time" class="form-control" name="end_time" id="end_time" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_location" class="form-label">Location</label>
                                        <input type="text" class="form-control" name="class_location" id="class_location" required />
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-success float-end">Add Schedule</button>
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