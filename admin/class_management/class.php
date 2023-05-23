<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    if ($conn) {
        $sql = "SELECT * FROM classes WHERE id = '$id'";

        $class_res = mysqli_query($conn, $sql);
        $class = $class_res->fetch_assoc();

        $class_id = $class['id'];
        $sql = "SELECT * FROM schedules WHERE class_id = '$class_id'";
        $schedules_res = mysqli_query($conn, $sql);

        if (isset($_POST['delete_schedule'])) {
            $schedule_id = mysqli_real_escape_string($conn, $_POST['schedule_id']);

            $sql = "DELETE FROM schedules WHERE id = '$schedule_id'";
            mysqli_query($conn, $sql);

            // set flash message and type
            $_SESSION['msg_type'] = 'success';
            $_SESSION['flash_message'] = 'Schedule Deleted';
            header("Location: " . $_SERVER['PHP_SELF'] . "?id=$class_id");
            session_write_close();
        }
    } else {
        echo "Couldn't connect to database.";
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
                    <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                        <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                            <?php echo $_SESSION["flash_message"]; ?>
                        </div>
                    <?php endif; ?>
                    <?php
                    unset($_SESSION['msg_type']);
                    unset($_SESSION['flash_message']);
                    ?>
                    <div class="card shadow">
                        <div class="card-body">
                            <h3>Class Details</h3>
                            <div class="row mt-4">
                                <div class="col-12 mb-4">
                                    <h1><?= $class['title']; ?></h1>
                                    <p><?= $class['description']; ?></p>
                                </div>
                                <div class="col-12 mb-4">
                                    <a href="<?= $rootURL; ?>/admin/class_management/add_schedule.php?id=<?= $class['id']; ?>" class="btn btn-success float-end">New Schedule</a>
                                </div>
                                <div class="col-12">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Day of Week</th>
                                                <th>Start Time</th>
                                                <th>End Time</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($schedules_res as $schedule) : ?>
                                                <tr>
                                                    <td><?= $schedule['day_of_week'] ?></td>
                                                    <td><?php echo date('h:i A', strtotime($schedule['time_start'])) ?></td>
                                                    <td><?php echo date('h:i A', strtotime($schedule['time_end'])) ?></td>
                                                    <td>
                                                        <a href="<?= $rootURL; ?>/admin/class_management/edit_schedule.php?id=<?= $schedule['id']; ?>" class="btn btn-warning">Edit</a>
                                                        <form method="POST" style="display: inline-block;">
                                                            <input type="hidden" name="schedule_id" value="<?= $schedule['id']; ?>">
                                                            <button type="submit" name="delete_schedule" class="btn btn-danger">Delete</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>