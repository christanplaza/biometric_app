<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $teacher_id = $_COOKIE['id'];
    $sql = "SELECT c.*, u.first_name, u.last_name 
          FROM classes c 
          JOIN users u ON c.teacher_id = u.id 
          WHERE c.status != 'deleted' AND c.teacher_id = '$teacher_id'";

    $classes_result = mysqli_query($conn, $sql);

    if (isset($_POST['submit'])) {
        $classes = $_POST['classes'];
        $date = $_POST['announcement_date'];
        $text = $_POST['announcement'];

        foreach ($classes as $class) {
            $sql = "INSERT INTO class_announcements (class_id, teacher_id, announcement_date, text) VALUES ('$class', '$teacher_id', '$date', '$text')";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['msg_type'] = 'success';
                $_SESSION['flash_message'] = 'Announcement Posted';
                header("location: $rootURL/faculty/");
                session_write_close();
            } else {
                $_SESSION['msg_type'] = 'error';
                $_SESSION['flash_message'] = 'Something went wrong, please try again.';
                header("location: $rootURL/faculty/");
                session_write_close();
            }
        }
    }
} else {
    echo "Couldn't connect to database.";
}

include('../logout.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php include_once "../components/header.php"; ?>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
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
                        <div class="card-body">
                            <div class="display-6">Create an announcement</div>
                            <div class="row mt-4">
                                <div class="col-6">
                                    <form method="POST">
                                        <div class="mb-3">
                                            <label for="announcement_date" class="form-label">Date of Announcement</label>
                                            <input type="date" class="form-control" id="announcement_date" name="announcement_date">
                                        </div>
                                        <div class="mb-3">
                                            <label for="announcement" class="form-label">Your Announcement</label>
                                            <input type="text" class="form-control" id="announcement" name="announcement">
                                        </div>
                                        <p>Classes Affected</p>
                                        <?php while ($row = $classes_result->fetch_assoc()) : ?>
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" value="<?= $row['id']; ?>" id="class<?= $row['id']; ?>" name="classes[]">
                                                <label class="form-check-label" for="class<?= $row['id']; ?>">
                                                    <?= $row['title']; ?>
                                                </label>
                                            </div>
                                        <?php endwhile; ?>
                                        <button type="submit" name="submit" class="btn btn-warning mt-4">Create Announcement</button>
                                    </form>
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