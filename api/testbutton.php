<?php
require_once 'db_connect.php';

header('Content-Type: application/json');

$response = array(
    "success" => false,
    "message" => "Invalid request"
);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['message'])) {
        if ($_POST['message'] == "oo") {

            // Successful aton process
            $response = array(
                "success" => true,
                "message" => "indi"
            );
        } else {

            // Successful aton process
            $response = array(
                "success" => false,
                "message" => "du gago oo lang gid daw nd pa mahatag"
            );
        }
    }
}

echo json_encode($response);
