<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT u1.id, u1.first_name, u1.last_name, u1.role, u1.phone_number, u2.first_name AS student_first_name, u2.last_name AS student_last_name
          FROM users u1
          LEFT JOIN users u2 ON u1.student_id = u2.id
          WHERE u1.role = 'parent'";

    $parent_res = mysqli_query($conn, $sql);
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
                            <div class="display-6">Parent Management</div>
                            <div class="row mt-4">
                                <div class="col-12">

                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>Parent Name</th>
                                                <th>Student Name</th>
                                                <th>Phone Number</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $parent_res->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?= $row['first_name']; ?></td>
                                                    <td><?= $row['student_first_name']; ?></td>
                                                    <td><?= $row['phone_number']; ?></td>
                                                    <td class="d-flex justify-content-evenly">
                                                        <a href="<?= $rootURL; ?>/admin/faculty.php?id=<?= $row['id']; ?>" class="btn btn-primary">View Details</a>
                                                        <form action="$rootURL/admin/faculty.php?" method="POST">
                                                            <input type="hidden" name="id" id="id" value="<?= $row['id']; ?>" />
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