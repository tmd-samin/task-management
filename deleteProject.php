<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    $_SESSION['deleteProjectError'] = "Database connection failed: " . $connection->connect_error;
    header('Location: index.php');
    exit();
}

if (isset($_GET['sn'])) {
    $shortName = $connection->real_escape_string($_GET['sn']);

    // Delete the project
    $sql = "DELETE FROM projects WHERE short_name = '$shortName'";
    if ($connection->query($sql)) {
        $_SESSION['deleteProjectSuccess'] = "Project deleted successfully.";
        header('Location: index.php');
        exit();
    } else {
        $_SESSION['deleteProjectError'] = "Error deleting project: " . $connection->error;
        header('Location: index.php');
        exit();
    }
} else {
    $_SESSION['deleteProjectError'] = "No project specified for deletion.";
    header('Location: index.php');
    exit();
}

$connection->close();
