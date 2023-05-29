<?php
session_start();
include('../../config.php');
$conn = mysqli_connect($host, $username, $password, $database);

if ($conn) {
    $sql = "SELECT * FROM users";

    $faculty_res = mysqli_query($conn, $sql);

    if (isset($_POST['submit'])) {
        if (isset($_POST['remove_id'])) {
            $id = $_POST['remove_id'];
            $sql = "UPDATE users SET fingerprint_id = '0' WHERE id = '$id'";

            if (mysqli_query($conn, $sql)) {
                $_SESSION['msg_type'] = 'success';
                $_SESSION['flash_message'] = 'Fingerprint unenrolled.';
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
                            <div class="display-6">Fingerprint Management</div>
                            <div class="row mt-4">
                                <div class="col-12">
                                    <table class="table table-striped align-middle">
                                        <thead>
                                            <tr class="table-primary">
                                                <th>Last Name</th>
                                                <th>First Name</th>
                                                <th>Username</th>
                                                <th>Fingerprint ID</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php while ($row = $faculty_res->fetch_assoc()) : ?>
                                                <tr>
                                                    <td><?php echo $row['last_name']; ?></td>
                                                    <td><?php echo $row['first_name']; ?></td>
                                                    <td><?php echo $row['username']; ?></td>
                                                    <td><?php echo $row['fingerprint_id'] > 0 ? $row['fingerprint_id'] : '-'; ?></td>
                                                    <td>
                                                        <?php if ($row['fingerprint_id'] == 0) : ?>
                                                            <a href="<?= $rootURL; ?>/admin/fingerprint_management/add_fingerprint.php?id=<?php echo $row['id']; ?>" class="btn btn-primary">Assign a Fingerprint</a>
                                                        <?php else : ?>
                                                            <form method="POST">
                                                                <input type="hidden" name="remove_id" value="<?= $row['id']; ?>">
                                                                <button type="submit" class="btn btn-danger" name="submit">Remove Fingerprint</button>
                                                            </form>
                                                        <?php endif; ?>
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