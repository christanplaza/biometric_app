<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $class_id = $_GET['id'];

    $class_sql = "SELECT * FROM classes WHERE id = '$class_id'";
    $class_res = mysqli_query($conn, $class_sql);
    $class_data = mysqli_fetch_assoc($class_res);

    $faculty_sql = "SELECT * FROM users WHERE role = 'faculty' ORDER BY last_name ASC";
    $faculties_res = mysqli_query($conn, $faculty_sql);

    if (isset($_POST['submit'])) {
        $title = mysqli_real_escape_string($conn, $_POST['class_title']);
        $description = mysqli_real_escape_string($conn, $_POST['class_description']);
        $teacher_id = mysqli_real_escape_string($conn, $_POST['class_teacher']);
        $status = mysqli_real_escape_string($conn, $_POST['class_status']);
        $limits = mysqli_real_escape_string($conn, $_POST['class_limits']);

        $sql = "UPDATE classes SET teacher_id = '$teacher_id', title = '$title', description = '$description', status = '$status', access_limit = '$limits' WHERE id = '$class_id';";

        if (mysqli_query($conn, $sql)) {
            $_SESSION['msg_type'] = 'success';
            $_SESSION['flash_message'] = 'Class Updated';
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
                            <div class="display-6">Edit Class</div>
                            <div class="row mt-4">
                                <form method="POST">
                                    <div class="mb-3">
                                        <label for="class_title" class="form-label">Title</label>
                                        <input type="text" name="class_title" id="class_title" class="form-control" value="<?= $class_data['title']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_description" class="form-label">Description</label>
                                        <textarea class="form-control" name="class_description" id="class_description" cols="30" rows="5" style="resize: none;" required><?= $class_data['description']; ?></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_teacher" class="form-label">Teacher</label>
                                        <select name="class_teacher" id="class_teacher" class="form-select" required>
                                            <?php while ($faculty = $faculties_res->fetch_assoc()) : ?>
                                                <option value="<?= $faculty['id']; ?>" <?= ($faculty['id'] == $class_data['teacher_id']) ? 'selected' : ''; ?>><?= $faculty['last_name']; ?>, <?= $faculty['first_name']; ?></option>
                                            <?php endwhile; ?>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_status" class="form-label">Status</label>
                                        <select name="class_status" id="class_status" class="form-select" required>
                                            <option value="active" <?= ($class_data['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                            <option value="inactive" <?= ($class_data['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                            <option value="archived" <?= ($class_data['status'] == 'archived') ? 'selected' : ''; ?>>Archived</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="class_limits" class="form-label">Access Limits (Number of people allowed fingerprint access)</label>
                                        <input type="number" name="class_limits" id="class_limits" class="form-control" required min="1" pattern="[1-9]\d*" value="<?= $class_data['access_limit'] ?>">
                                    </div>
                                    <button type="submit" name="submit" class="btn btn-success float-end">Update</button>
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