<?php
session_start();

if (!(isset($_SESSION['logged-in']))) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
    exit();
}

require_once "connect.php";
$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    echo json_encode(['success' => false, 'message' => 'Database connection error.']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['fullName']) || !isset($data['shortName'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input data.']);
    exit();
}

$fullName = $connection->real_escape_string($data['fullName']);
$shortName = $connection->real_escape_string($data['shortName']);

// Update the project with both Full Name and Short Name
$sql = "UPDATE projects SET `Full name` = '$fullName', `Short name` = '$shortName' WHERE `Short name` = '$shortName'";

if ($connection->query($sql)) {
    echo json_encode(['success' => true, 'message' => 'Project updated successfully.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating project.']);
}
?>
