<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];
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

        $sql = "SELECT DATE_FORMAT(a.created_at, '%m/%d/%Y') AS created_at_short, c.title, CONCAT_WS(', ', t.last_name, t.first_name) AS teacher_name, CONCAT_WS(' ', DAYNAME(sc.time_start), TIME_FORMAT(sc.time_start, '%h:%i%p'), '-', TIME_FORMAT(sc.time_end, '%h:%i%p')) AS schedule_time 
        FROM attendance a 
        JOIN users s ON a.student_id = s.id 
        JOIN classes c ON a.class_id = c.id 
        JOIN users t ON c.teacher_id = t.id 
        JOIN schedules sc ON a.schedule_id = sc.id 
        WHERE 1 $search_query $sort_query AND a.student_id = '$id'";
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
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="card shadow">
                                <div class="card-body">
                                    <div class="display-6">Attendance Logs</div>
                                    <div class="row mt-4">
                                        <form method="GET" class="mb-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-2">
                                                    <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or class" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" />
                                                </div>
                                                <div class="col-md-6">
                                                    <select name="sort" id="sort" class="form-select">
                                                        <option value="">Sort by</option>
                                                        <option value="created_at ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'created_at ASC' ? 'selected' : '' ?>>Date (Ascending)</option>
                                                        <option value="created_at DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'created_at DESC' ? 'selected' : '' ?>>Date (Descending)</option>
                                                        <option value="teacher_name ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'teacher_name ASC' ? 'selected' : '' ?>>Teacher Name (Ascending)</option>
                                                        <option value="teacher_name DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'teacher_name DESC' ? 'selected' : '' ?>>Teacher Name (Descending)</option>
                                                        <option value="title ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'title ASC' ? 'selected' : '' ?>>Class Name (Ascending)</option>
                                                        <option value="title DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'title DESC' ? 'selected' : '' ?>>Class Name (Descending)</option>
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
                                                    <th scope="col">Date Created</th>
                                                    <th scope="col">Teacher Name</th>
                                                    <th scope="col">Class Name</th>
                                                    <th scope="col">Schedule Time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                // Fetch the results and print them in a table row
                                                while ($row = $result->fetch_assoc()) {
                                                    $title = $row["title"];
                                                    $teacher_name = $row["teacher_name"];
                                                    $schedule_time = $row["schedule_time"];
                                                ?>
                                                    <tr>
                                                        <td><?= $row["created_at_short"] ?></td>
                                                        <td><?= $teacher_name ?></td>
                                                        <td><?= $title ?></td>
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