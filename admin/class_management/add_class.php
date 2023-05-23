<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT * FROM users WHERE role = 'faculty' ORDER BY last_name ASC";

    $faculties_res = mysqli_query($conn, $sql);

    if (isset($_POST['submit'])) {
        $title = mysqli_real_escape_string($conn, $_POST['class_title']);
        $description = mysqli_real_escape_string($conn, $_POST['class_description']);
        $teacher_id = mysqli_real_escape_string($conn, $_POST['class_teacher']);
        $class_limits = mysqli_real_escape_string($conn, $_POST['class_limits']);

        $sql = "INSERT INTO classes (teacher_id, title, description, status, access_limit) VALUES ('$teacher_id', '$title', '$description', 'inactive', '$class_limits');";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_type'] = 'success';
            $_SESSION['flash_message'] = 'Class Created';
            header("location: $rootURL/admin/class_management.php");
            session_write_close();
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
                            <div class="display-6">Create a Class</div>
                            <div class="row mt-4">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="class_title" class="form-label">Title</label>
                                        <input type="text" name="class_title" id="class_title" class="form-control" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_description" class="form-label">Description</label>
                                        <textarea class="form-control" name="class_description" id="class_description" cols="30" rows="5" style="resize: none;" required></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_teacher" class="form-label">Teacher</label>
                                        <select name="class_teacher" id="class_teacher" class="form-select" required>
                                            <option label="Select a Teacher"></option>
                                            <?php while ($faculty = $faculties_res->fetch_assoc()) : ?>
                                                <option value="<?= $faculty['id']; ?>"><?= $faculty['last_name']; ?>, <?= $faculty['first_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_limits" class="form-label">Access Limits (Number of people allowed fingerprint access)</label>
                                        <input type="number" name="class_limits" id="class_limits" class="form-control" required min="1" pattern="[1-9]\d*" value="<?= $class_data['access_limit'] ?>">
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-success float-end">Create</button>
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