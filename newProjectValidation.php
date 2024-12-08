<?php
session_start();
require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    $_SESSION['addProjectError'] = "Database connection failed: " . $connection->connect_error;
    header('Location: newProject.php');
    exit();
}

// Validate inputs
if (empty($_POST['full']) || empty($_POST['short'])) {
    $_SESSION['addProjectError'] = "Both Full Project Name and Short Project Name are required.";
    header('Location: newProject.php');
    exit();
}

$fullName = $connection->real_escape_string(trim($_POST['full']));
$shortName = $connection->real_escape_string(trim($_POST['short']));

// Insert project into database
$sql = "INSERT INTO projects (id, full_name, short_name) VALUES (NULL, '$fullName', '$shortName')";

if ($connection->query($sql)) {
    $_SESSION['newProjectSuccess'] = "Project added successfully!";
    unset($_SESSION['addProjectError']);
    header('Location: index.php');
    exit();
} else {
    $_SESSION['addProjectError'] = "Error adding project: " . $connection->error;
    header('Location: newProject.php');
    exit();
}

$connection->close();
?>
