<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "SELECT DATE_FORMAT(el.created_at, '%m/%d/%Y') AS created_at_short, el.*, CONCAT_WS(', ', s.last_name, s.first_name) AS student_name FROM excuse_letters el JOIN users s ON el.student_id = s.id WHERE el.class_id = '$id'";
    $excuse_letters_res = mysqli_query($conn, $sql);


    $search_query = "";
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        if (!empty($search_query)) {
            $search_query = mysqli_real_escape_string($conn, $_GET['search']);
            $search_query = "AND (s.last_name LIKE '%$search_query%' OR s.first_name LIKE '%$search_query%')";
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
            $sort_query = "ORDER BY $sort_field;";
        }
    }

    $sql = "SELECT DATE_FORMAT(a.created_at, '%m/%d/%Y %h:%i %p') AS created_at_short, CONCAT_WS(', ', s.last_name, s.first_name) AS student_name, s.id as student_id, c.title, CONCAT_WS(', ', t.last_name, t.first_name) AS teacher_name, CONCAT_WS(' ', DAYNAME(sc.time_start), TIME_FORMAT(sc.time_start, '%h:%i%p'), '-', TIME_FORMAT(sc.time_end, '%h:%i%p')) AS schedule_time, a.status as status 
        FROM attendance a 
        JOIN users s ON a.student_id = s.id 
        JOIN classes c ON a.class_id = c.id 
        JOIN users t ON c.teacher_id = t.id 
        JOIN schedules sc ON a.schedule_id = sc.id 
        WHERE c.id = $id AND 1 $search_query $sort_query";
    $result = mysqli_query($conn, $sql);


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
                    <?php include_once "../components/panel.php" ?>
                </div>
                <div class="col-8">
                    <div class="row">
                        <div class="col-12 mb-4">
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
                                            <a href="<?= $rootURL; ?>/faculty/class_management/add_schedule.php?id=<?= $class['id']; ?>" class="btn btn-success float-end">New Schedule</a>
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
                                                                <a href="<?= $rootURL; ?>/faculty/class_management/edit_schedule.php?id=<?= $schedule['id']; ?>" class="btn btn-warning">Edit</a>
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
                        <div class="col-12">
                            <ul class="nav nav-tabs" id="myTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="excuse-tab" data-bs-toggle="tab" href="#excuse" role="tab" aria-controls="excuse" aria-selected="true">Excuse Letters</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance" role="tab" aria-controls="attendance" aria-selected="false">Attendance</a>
                                </li>
                            </ul>
                            <div class="tab-content" id="myTabContent">
                                <div class="tab-pane fade show active" id="excuse" role="tabpanel" aria-labelledby="excuse-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <h3>Excuse Letters</h3>
                                            <table class="table table-striped">
                                                <thead>
                                                    <tr>
                                                        <th scope="col">Date Created</th>
                                                        <th scope="col">Student Name</th>
                                                        <th scope="col">Note</th>
                                                        <th scope="col">Actions</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    // Fetch the results and print them in a table row
                                                    while ($row = $excuse_letters_res->fetch_assoc()) {
                                                        $student_name = $row["student_name"];
                                                    ?>
                                                        <tr>
                                                            <td><?= $row["created_at_short"] ?></td>
                                                            <td><?= $student_name ?></td>
                                                            <td><?= $row['note'] ?></td>
                                                            <td>
                                                                <a href="<?= $rootURL; ?>/faculty/class_management/excuse_letter.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="attendance" role="tabpanel" aria-labelledby="attendance-tab">
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="display-6">Attendance Logs</div>
                                            <div class="row mt-4">
                                                <form method="GET" class="mb-3">
                                                    <input type="hidden" name="id" value="<?= $id ?>">
                                                    <div class="row">
                                                        <div class="col-md-6 mb-2">
                                                            <input type="text" name="search" id="search" class="form-control" placeholder="Search by name" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" />
                                                        </div>
                                                        <div class="col-md-6">
                                                            <select name="sort" id="sort" class="form-select">
                                                                <option value="">Sort by</option>
                                                                <option value="created_at ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'created_at ASC' ? 'selected' : '' ?>>Date (Ascending)</option>
                                                                <option value="created_at DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'created_at DESC' ? 'selected' : '' ?>>Date (Descending)</option>
                                                                <option value="student_name ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'student_name ASC' ? 'selected' : '' ?>>Student Name (Ascending)</option>
                                                                <option value="student_name DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'student_name DESC' ? 'selected' : '' ?>>Student Name (Descending)</option>
                                                            </select>
                                                        </div>
                                                        <div class="col-md-12 mt-2">
                                                            <button type="submit" class="btn btn-primary">Search and Sort</button>
                                                        </div>
                                                    </div>
                                                </form>
                                                <table class="table table-striped mt-5">
                                                    <thead>
                                                        <tr>
                                                            <th scope="col">Schedule Time</th>
                                                            <th scope="col">Date Created</th>
                                                            <th scope="col">Student Name</th>
                                                            <th scope="col">Status</th>
                                                            <th scope="col">Present</th>
                                                            <th scope="col">Absent</th>
                                                            <td></td>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php
                                                        // Fetch the results and print them in a table row
                                                        while ($row = $result->fetch_assoc()) {
                                                            $student_name = $row["student_name"];
                                                            $student_id = $row['student_id'];
                                                            $title = $row["title"];
                                                            $teacher_name = $row["teacher_name"];
                                                            $status = $row["status"];
                                                            $schedule_time = $row["schedule_time"];

                                                            $sql = "SELECT 
                                                                    SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) AS absent_count,
                                                                    SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) AS present_count
                                                                FROM 
                                                                    attendance a
                                                                JOIN 
                                                                    classes c ON a.class_id = c.id
                                                                WHERE 
                                                                    a.student_id = '$student_id' 
                                                                    AND c.id = '$class_id'";

                                                            $attendance_status_count_res = mysqli_query($conn, $sql);
                                                            $attendance_status_count = mysqli_fetch_assoc($attendance_status_count_res);
                                                        ?>
                                                            <tr>
                                                                <td><?= $schedule_time ?></td>
                                                                <td><?= $row["created_at_short"] ?></td>
                                                                <td><a href="<?= $rootURL ?>/faculty/attendance_status.php?id=<?= $row['student_id'] ?>&class_id=<?= $id ?>"><?= $student_name ?></a></td>
                                                                <td>
                                                                    <?php if ($status === 'present') : ?>
                                                                        <span class="badge bg-success">Present</span>
                                                                    <?php elseif ($status === 'absent') : ?>
                                                                        <span class="badge bg-danger">Absent</span>
                                                                    <?php elseif ($status === 'excused') : ?>
                                                                        <span class="badge bg-warning text-dark">Excused</span>
                                                                    <?php endif; ?>
                                                                </td>
                                                                <td>
                                                                    <?= $attendance_status_count['present_count']; ?>
                                                                </td>
                                                                <td>
                                                                    <?= $attendance_status_count['absent_count']; ?>
                                                                </td>
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
        </div>
    </div>
</body>

</html>