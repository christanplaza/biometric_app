<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

$sql = "SELECT al.*, u.* FROM access_logs al INNER JOIN users u ON u.fingerprint_id = al.fingerprint_id";

$results = mysqli_query($conn, $sql);

include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
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
                    <?php include_once "components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">Attendance Logs</div>
                            <div class="row mt-4">
                                <table class="table table-striped mt-5">
                                    <thead>
                                        <tr>
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Room</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Datetime</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch the results and print them in a table row
                                        while ($row = $results->fetch_assoc()) {
                                            $student_name = $row["first_name"] . " " . $row["last_name"];
                                            $room = $row["room_name"];
                                            $state = $row["state"];
                                            $datetime = $row["datetime"];
                                            $date = new DateTime($datetime);
                                        ?>
                                            <tr>
                                                <td><?= $student_name ?></td>
                                                <td><?= $room ?></td>
                                                <td><?= $state ?></td>
                                                <td><?= $date->format('Y-m-d h:iA') ?></td>
                                            </tr>
                                        <?php
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>