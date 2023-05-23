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

        $sql = "SELECT *
        FROM classes
        WHERE id NOT IN (
            SELECT class_id FROM enrollments WHERE student_id = " . $id . " AND status <> 'inactive'
        ) AND status = 'active'";

        $classes_res = mysqli_query($conn, $sql);

        $sql = "SELECT c.title, c.description, c.status, c.id AS class_id, u.first_name, u.last_name
            FROM classes c
            JOIN enrollments e ON c.id = e.class_id
            JOIN users u ON c.teacher_id = u.id
            WHERE e.student_id = " . $id . " AND e.status = 'active'";

        $enrolled_classes_res = mysqli_query($conn, $sql);

        if (!$classes_res) {
            echo "Error";
        } else {
        }

        if (isset($_POST['submit'])) {
            $student_id = mysqli_real_escape_string($conn, $_POST['student_id']);
            $classes = $_POST['classes'];
            $enrollment_end = mysqli_real_escape_string($conn, $_POST['enrollment_end']);
            foreach ($classes as $class_id) {
                $sql = "INSERT INTO enrollments (student_id, class_id, enrollment_end, status, grade) VALUES ('$student_id', '$class_id', '$enrollment_end', 'active', 'N/A')";
                echo $sql;
                mysqli_query($conn, $sql);
            }

            $_SESSION['msg_type'] = 'success';
            $_SESSION['flash_message'] = 'Student enrolled successfully';
            session_write_close();
            header("location: $rootURL/admin/enrollments.php?id=" . $id);
        }
    } else {
        echo "Couldn't connect to database.";
    }
} else {
    header("location: $rootURL/admin/student_management.php");
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
                    <div class="row mt-4">
                        <div class="col-6">
                            <div class="card">
                                <div class="card-body">
                                    <form method="POST">
                                        <?php if (mysqli_num_rows($classes_res) > 0) : ?>
                                            <div class="mb-3">
                                                <label for="enrollment_end" class="form-label">Enrollment End</label>
                                                <input type="date" class="form-control" name="enrollment_end" id="enrollment_end" min="<?php echo date('Y-m-d'); ?>" required>
                                            </div>
                                            <h4>Available Classes</h4>
                                            <hr>
                                            <div class="mb-3">
                                                <?php while ($class = mysqli_fetch_assoc($classes_res)) : ?>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="classes[]" value="<?= $class['id'] ?>" id="class<?= $class['id'] ?>">
                                                        <label class="form-check-label" for="class<?= $class['id'] ?>" style="cursor: pointer;">
                                                            <?= $class['title'] ?>
                                                        </label>
                                                    </div>
                                                <?php endwhile; ?>
                                            </div>
                                            <input type="hidden" name="student_id" value="<?= $id ?>">
                                            <button type="submit" name="submit" class="btn btn-primary mt-3">Enroll</button>
                                        <?php else : ?>
                                            <div class="alert alert-info" role="alert">
                                                There are no available classes for this student.
                                            </div>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-6">
                            <h3>Enrolled Classes</h3>
                            <?php while ($row = mysqli_fetch_assoc($enrolled_classes_res)) : ?>
                                <div class="card mb-4">
                                    <div class="card-body">
                                        <h5 class="card-title"><?= $row['title'] ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?= $row['first_name'] . ' ' . $row['last_name'] ?></h6>
                                        <p class="card-text"><?= $row['description'] ?></p>
                                        <p class="card-text"><strong>Status: </strong><?= $row['status'] ?></p>
                                        <a href="<?= $rootURL; ?>/admin/class_management/class.php?id=<?= $row['class_id']; ?>" class="card-link">View Class Details</a>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>