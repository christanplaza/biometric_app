<?php
include('../../config.php');
session_start();
date_default_timezone_set('Asia/Singapore');
$conn = mysqli_connect($host, $username, $password, $database);

// Check if there is a row in board_mode
$sql = "SELECT * FROM board_mode LIMIT 1";
$result = mysqli_query($conn, $sql);
$board = $result->fetch_assoc();

if (isset($_POST['enrollment'])) {
    $sql = "UPDATE board_mode SET mode = 'enrollment' WHERE 1 = 1";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_type'] = 'success';
        $_SESSION['flash_message'] = 'Board mode set, reset NodeMCU';
        header("Refresh: 0");
        session_write_close();
    } else {
        $_SESSION['msg_type'] = 'error';
        $_SESSION['flash_message'] = 'Something went wrong';
        header("Refresh: 0");
        session_write_close();
    }
}

if (isset($_POST['scan'])) {
    $sql = "UPDATE board_mode SET mode = 'scan' WHERE 1 = 1";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['msg_type'] = 'success';
        $_SESSION['flash_message'] = 'Board mode set, reset NodeMCU';
        header("Refresh: 0");
        session_write_close();
    } else {
        $_SESSION['msg_type'] = 'error';
        $_SESSION['flash_message'] = 'Something went wrong';
        header("Refresh: 0");
        session_write_close();
    }
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
                        <div class="card-body">
                            <div class="display-3">Set Board Mode</div>
                            <div class="display-6">Current Mode: <?= $board['mode'] == 'enrollment' ? 'Enrollment' : 'Scan' ?></div>
                            <div class="row">
                                <div class="col-12 mt-4">
                                    <form method="POST">
                                        <button type="submit" name="enrollment" class="btn btn-lg w-100 mb-4 <?= $board['mode'] == 'enrollment' ? 'btn-primary' : 'btn-secondary' ?>">Enrollment</button>
                                    </form>
                                    <form method="POST">
                                        <button type="submit" name="scan" class="btn btn-lg w-100  <?= $board['mode'] == 'scan' ? 'btn-primary' : 'btn-secondary' ?>">Scan</button>
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