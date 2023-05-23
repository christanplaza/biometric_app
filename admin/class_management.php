<?php
include('../../config.php');
session_start();
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT c.*, u.first_name, u.last_name 
          FROM classes c 
          JOIN users u ON c.teacher_id = u.id 
          WHERE c.status != 'deleted'";

    $classes_result = mysqli_query($conn, $sql);

    if (isset($_POST['submit'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            $sql = "SELECT * FROM classes WHERE id = '$id'";

            $class_result = mysqli_query($conn, $sql);
            $class = $class_result->fetch_assoc();
            $class_id = $class['id'];

            $sql = "UPDATE classes SET status = 'deleted' WHERE id = '$id'";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['msg_type'] = 'success';
                $_SESSION['flash_message'] = 'Class has been deleted';
                header("Refresh: 0");
                session_write_close();
            } else {
                $_SESSION['msg_type'] = 'danger';
                $_SESSION['flash_message'] = 'Something went wrong.';
                header("Refresh: 0");
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
                            <?php if (isset($_SESSION['msg_type']) && isset($_SESSION['flash_message'])) : ?>
                                <div class="alert alert-<?php echo $_SESSION["msg_type"]; ?> alert-dismissible fade show" role="alert">
                                    <?php echo $_SESSION["flash_message"]; ?>
                                </div>
                            <?php endif; ?>
                            <?php
                            unset($_SESSION['msg_type']);
                            unset($_SESSION['flash_message']);
                            ?>
                            <div class="display-6">Class Management</div>
                            <div class="row mt-4">
                                <div class="col-12 mb-4">
                                    <a href="<?= $rootURL; ?>/admin/class_management/add_class.php" class="btn btn-success float-end">New Class</a>
                                </div>
                                <div class="col-12">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>Class Title</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $classes_result->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $row['title']; ?></td>
                                                    <td>
                                                        <a href="<?= $rootURL; ?>/admin/class_management/class.php?id=<?php echo $row['id']; ?>" class="btn btn-primary mx-2">View Details</a>
                                                        <a href="<?= $rootURL; ?>/admin/class_management/edit_class.php?id=<?= $row['id']; ?>" class="btn btn-warning mx-2">Edit</a>
                                                        <form method="POST" style="display: inline-block;" onsubmit="return confirm('Are you sure you want to remove this class? This action is permanent and cannot be undone.');">
                                                            <input type="hidden" name="id" id="id" value="<?php echo $row['id']; ?>" />
                                                            <button type="submit" name="submit" class="btn btn-danger">Remove Class</button>
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