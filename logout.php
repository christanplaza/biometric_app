<?php
if (isset($_POST['logout'])) {
    setcookie('id', null, -1);
    setcookie('username', null, -1);
    setcookie('role', null, -1);
    setcookie('name', null, -1);
    setcookie('logged_in', null, -1);
    header("location: $rootURL/index.php");
}
