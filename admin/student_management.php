<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $search_query = '';
    $sort_query = '';

    // Check if search query is submitted
    if (isset($_GET['search'])) {
        $search_query = $_GET['search'];
        if (!empty($search_query)) {
            $search_query = mysqli_real_escape_string($conn, $_GET['search']);
            $search_query = "AND (username LIKE '%$search_query%' OR (first_name = '$search_query') OR (last_name = '$search_query'))";
        }
    }


    // Check if sort query is submitted
    if (isset($_GET['sort'])) {
        $sort_query = $_GET['sort'];
        if (!empty($sort_query)) {
            $sort_query = "ORDER BY " . $_GET['sort'] . " ASC;";
        }
    }

    $sql = "SELECT * FROM users WHERE role = 'student' $search_query $sort_query";

    $student_res = mysqli_query($conn, $sql);
} else {
    echo "Couldn't connect to database.";
}
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
                            <div class="display-6">Student Management</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <form method="GET" class="mb-3">
                                        <div class="row">
                                            <div class="col-md-6 mb-2">
                                                <input type="text" name="search" id="search" class="form-control" placeholder="Search by name or username" value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>" />
                                            </div>
                                            <div class="col-md-6">
                                                <select name="sort" id="sort" class="form-select">
                                                    <option value="">Sort by</option>
                                                    <option value="last_name" <?= isset($_GET['sort']) && $_GET['sort'] == 'last_name' ? 'selected' : '' ?>>Last Name</option>
                                                    <option value="first_name" <?= isset($_GET['sort']) && $_GET['sort'] == 'first_name' ? 'selected' : '' ?>>First Name</option>
                                                    <option value="username" <?= isset($_GET['sort']) && $_GET['sort'] == 'username' ? 'selected' : '' ?>>Username</option>
                                                </select>
                                            </div>
                                            <div class="col-md-12 mt-2">
                                                <button type="submit" class="btn btn-primary">Search and Sort</button>
                                            </div>
                                        </div>
                                    </form>

                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Username</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $student_res->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $row['last_name']; ?></td>
                                                    <td><?php echo $row['first_name']; ?></td>
                                                    <td><?php echo $row['username']; ?></td>
                                                    <td class="d-flex justify-content-evenly">
                                                        <a href="<?= $rootURL; ?>/admin/enrollments.php?id=<?php echo $row['id']; ?>" class="btn btn-secondary">Enrollment</a>
                                                        <a href="<?= $rootURL; ?>/admin/student.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                        <form action="$rootURL/admin/student.php?" method="POST">
                                                            <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>" />
                                                            <button type="submit" class="btn btn-danger">Delete User</button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            <?php endwhile; ?>
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