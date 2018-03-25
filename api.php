<?php
error_reporting(0);
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if ($_REQUEST['api_key'] == $API_KEY) {

        if (isset($_REQUEST['hodnota'])) {
            $statement = $connect->prepare("INSERT INTO mereni (hodnota,time) VALUES (?, ?)");
            $statement->bind_param("ii", $_REQUEST['hodnota'], time());
            if ($statement->execute()) {
                die("OK");
            } else {
                die("Failed");
            }
        } else {
            die("Invalid data");
        }
    } else {
        die("Invalid API key provided.");
    }
} else {die("Invalid request.");}
