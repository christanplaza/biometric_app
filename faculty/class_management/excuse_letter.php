<?php
include('../../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);
if (isset($_GET['id'])) {
    if ($conn) {
        $id = $_GET['id'];

        // SELECT DATE_FORMAT(el.created_at, '%m/%d/%Y') AS created_at_short, el.*, CONCAT_WS(', ', s.last_name, s.first_name) AS student_name FROM excuse_letters el JOIN users s ON el.student_id = s.id WHERE el.class_id = '$id'
        $sql = "SELECT DATE_FORMAT(el.created_at, '%m/%d/%Y %h:%i %p') AS created_at_short, el.*, c.title, CONCAT_WS(', ', s.last_name, s.first_name) AS student_name FROM excuse_letters el JOIN users s ON el.student_id = s.id JOIN classes c ON el.class_id = c.id WHERE el.id = '$id' LIMIT 1";

        $excuse_letter_res = mysqli_query($conn, $sql);
        $excuse_letter = mysqli_fetch_assoc($excuse_letter_res);
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
                            <div class="card shadow">
                                <div class="card-header bg-secondary-subtle">Excuse Letter</div>
                                <div class="card-body">
                                    <table class="table">
                                        <tr>
                                            <td>Student Name</td>
                                            <td><?php echo $excuse_letter['student_name']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Excuse Date</td>
                                            <td><?php echo $excuse_letter['excuse_date']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Submitted</td>
                                            <td><?php echo $excuse_letter['created_at_short']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Class</td>
                                            <td><?php echo $excuse_letter['title']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Note</td>
                                            <td><?php echo $excuse_letter['note']; ?></td>
                                        </tr>
                                        <tr>
                                            <td>Image Attached</td>
                                            <td>
                                                <img src="data:image/png;base64,<?= $excuse_letter['image_path']; ?>" alt="Base64 encoded image" />
                                            </td>
                                        </tr>
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