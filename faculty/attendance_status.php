<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];
        $class_id = $_GET['class_id'];
        $sql = "SELECT * FROM users WHERE id = '$id' AND role = 'student'";

        $user_res = mysqli_query($conn, $sql);
        $user = mysqli_fetch_assoc($user_res);

        $search_query = "";
        if (isset($_GET['search'])) {
            $search_query = $_GET['search'];
            if (!empty($search_query)) {
                $search_query = mysqli_real_escape_string($conn, $_GET['search']);
                $search_query = "AND (t.last_name LIKE '%$search_query%' OR t.first_name LIKE '%$search_query%' OR c.title LIKE '%$search_query%')";
            }
        }

        // Check if sort query is submitted
        $sort_query = "";
        $sort_field = "";
        if (isset($_GET['sort'])) {
            $sort_field = $_GET['sort'];
            if (!empty($sort_field)) {
                if ($sort_field == 'created_at_short') {
                    $sort_field = 'a.created_at';
                }
                $sort_query = "ORDER BY $sort_field ASC;";
            }
        }

        $sql = "SELECT title, access_limit FROM classes WHERE id = '$class_id' LIMIT 1";
        $class_result = mysqli_query($conn, $sql);
        $class = mysqli_fetch_assoc($class_result);

        $sql = "SELECT COUNT(*) AS absent_count
        FROM attendance a
        JOIN classes c ON a.class_id = c.id
        WHERE a.student_id = '$id' AND c.id = '$class_id' AND a.status = 'absent'";

        $absences_result = mysqli_query($conn, $sql);
        $absences = mysqli_fetch_assoc($absences_result);

        $sql = "SELECT DATE_FORMAT(a.created_at, '%m/%d/%Y') AS created_at_short, c.title, CONCAT_WS(' ', DAYNAME(sc.time_start), TIME_FORMAT(sc.time_start, '%h:%i%p'), '-', TIME_FORMAT(sc.time_end, '%h:%i%p')) AS schedule_time,
        a.status
        FROM attendance a 
        JOIN users s ON a.student_id = s.id 
        JOIN classes c ON a.class_id = c.id 
        JOIN schedules sc ON a.schedule_id = sc.id 
        WHERE 1 $search_query $sort_query AND a.student_id = '$id' AND c.id = '$class_id'";
        $result = mysqli_query($conn, $sql);
    } else {
        echo "Couldn't connect to database.";
    }
} else if (isset($_POST['id'])) {
    if ($conn) {
        $id = $_POST['id'];
        $sql = "DELETE FROM users WHERE id = '$id'";

        mysqli_query($conn, $sql);
        header("location: $rootURL/faculty/student_management.php");
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/faculty/student_management.php");
}
include('../logout.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <title>Biometrics Monitoring | Faculty</title>
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
                    <div class="row">
                        <div class="col-12 mb-4">
                            <div class="card shadow">
                                <div class="card-header bg-secondary-subtle">Student Profile</div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td>First Name</td>
                                            <td><?php echo $user['first_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Last Name</td>
                                            <td><?php echo $user['last_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Username</td>
                                            <td><?php echo $user['username']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Number of Absences</td>
                                            <td><?php echo $absences['absent_count']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Absence limit for <?= $class['title']; ?></td>
                                            <td><?php echo $class['access_limit']; ?></td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="display-6">Attendance Logs for <?= $user['first_name'] ?> <?= $user['last_name']; ?> for <?= $class['title']; ?></div>
                                    <div class="row mt-4">
                                        <table class="table table-striped mt-5">
                                            <thead>
                                                <tr>
                                                    <th scope="col">Date Created</th>
                                                    <th scope="col">Status</th>
                                                    <th scope="col">Schedule Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch the results and print them in a table row
                                                while ($row = $result->fetch_assoc()) {
                                                    $title = $row["title"];
                                                    $status = $row["status"];
                                                    $schedule_time = $row["schedule_time"];
                                                ?>
                                                    <tr>
                                                        <td><?= $row["created_at_short"] ?></td>
                                                        <td>
                                                            <?php if ($status === 'present') : ?>
                                                                <span class="badge bg-success">Present</span>
                                                            <?php elseif ($status === 'absent') : ?>
                                                                <span class="badge bg-danger">Absent</span>
                                                            <?php elseif ($status === 'excused') : ?>
                                                                <span class="badge bg-warning text-dark">Excused</span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= $schedule_time ?></td>
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
        </div>
    </div>
</body>

</html>