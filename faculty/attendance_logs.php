<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

$teacher_id = $_COOKIE['id'];

$search_query = "";
if (isset($_GET['search'])) {
    $search_query = $_GET['search'];
    if (!empty($search_query)) {
        $search_query = mysqli_real_escape_string($conn, $_GET['search']);
        $search_query = "AND (u.last_name LIKE '%$search_query%' OR u.first_name LIKE '%$search_query%')";
    }
}

// Check if sort query is submitted
$sort_query = "";
$sort_field = "";
if (isset($_GET['sort'])) {
    $sort_field = $_GET['sort'];
    if (!empty($sort_field)) {
        $sort_field = mysqli_real_escape_string($conn, $_GET['sort']);
        $valid_sort_fields = ['name', 'class'];
        if (in_array($sort_field, $valid_sort_fields)) {
            $sort_query = "ORDER BY $sort_field";
        }
    }
}

$sql = "SELECT
        CONCAT_WS(', ', u.last_name, u.first_name) AS student_name,
        c.title AS class_title,
        c.access_limit AS class_limit,
        COUNT(a.id) AS absences,
        (c.access_limit - COUNT(a.id)) AS absences_remaining
        FROM
        users u
        INNER JOIN enrollments e ON u.id = e.student_id
        INNER JOIN classes c ON e.class_id = c.id
        LEFT JOIN attendance a ON e.student_id = a.student_id AND e.class_id = a.class_id
        WHERE
        u.role = 'student'
        AND c.teacher_id = '$teacher_id'
        $search_query
        GROUP BY
        u.id, c.id
        $sort_query;";
$result = mysqli_query($conn, $sql);

// Create an array to store the sorted data
$sortedResults = array();

// Loop through the results and store them in the sorted array
while ($row = mysqli_fetch_assoc($result)) {
    $sortedResults[] = $row;
}

if ($sort_field) {
    // Split the sort field and direction
    $parts = explode(' ', $sort_field);
    $sortBy = $parts[0];  // Field to sort by
    $sortDirection = $parts[1];  // Sort direction (ASC or DESC)

    // Perform the sorting based on the sort field and direction
    usort($sortedResults, function ($a, $b) use ($sortBy, $sortDirection) {
        if ($sortBy === 'absences') {
            return ($sortDirection === 'ASC') ? $a['absences'] - $b['absences'] : $b['absences'] - $a['absences'];
        } elseif ($sortBy === 'absences_remaining') {
            return ($sortDirection === 'ASC') ? $a['absences_remaining'] - $b['absences_remaining'] : $b['absences_remaining'] - $a['absences_remaining'];
        }
        // Add more conditions for other fields if needed

        // Default: no sorting
        return 0;
    });
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
                    <div class="card shadow">
                        <div class="card-body">
                            <div class="display-6">Attendance Logs</div>
                            <div class="row mt-4">
                                <form method="GET" class="mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2">
                                            <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or class" value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
                                        </div>
                                        <div class="col-md-6">
                                            <select name="sort" id="sort" class="form-select">
                                                <option value="">Sort by</option>
                                                <option value="name ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'name ASC' ? 'selected' : '' ?>>Name (Ascending)</option>
                                                <option value="name DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'name DESC' ? 'selected' : '' ?>>Name (Descending)</option>
                                                <option value="class ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'class ASC' ? 'selected' : '' ?>>Class (Ascending)</option>
                                                <option value="class DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'class DESC' ? 'selected' : '' ?>>Class (Descending)</option>
                                                <option value="absences ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'absences ASC' ? 'selected' : '' ?>>Absences (Ascending)</option>
                                                <option value="absences DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'absences DESC' ? 'selected' : '' ?>>Absences (Descending)</option>
                                                <option value="absences_remaining ASC" <?= isset($_GET['sort']) && $_GET['sort'] == 'absences_remaining ASC' ? 'selected' : '' ?>>Absences Remaining (Ascending)</option>
                                                <option value="absences_remaining DESC" <?= isset($_GET['sort']) && $_GET['sort'] == 'absences_remaining DESC' ? 'selected' : '' ?>>Absences Remaining (Descending)</option>
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
                                            <th scope="col">Student Name</th>
                                            <th scope="col">Class Title</th>
                                            <th scope="col">Absences</th>
                                            <th scope="col">Absences Remaining</th>
                                            <th scope="col">Class Absence Limit</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Fetch the results and print them in a table row
                                        foreach ($sortedResults as $row) {
                                            $student_name = $row["student_name"];
                                            $class_title = $row["class_title"];
                                            $absences = $row["absences"];
                                            $absences_remaining = $row["absences_remaining"];
                                            $access_limit = $row["class_limit"];
                                        ?>
                                            <tr>
                                                <td><?= $student_name ?></td>
                                                <td><?= $class_title ?></td>
                                                <td><?= $absences ?></td>
                                                <td><?= $absences_remaining ?></td>
                                                <td><?= $access_limit ?></td>
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