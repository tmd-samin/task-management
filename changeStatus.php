<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['sn']) || !isset($_GET['tn']) || !isset($_GET['status'])) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno != 0) {
    die("Database connection failed: " . $connection->connect_error);
}

// Escape and validate input
$shortName = $connection->real_escape_string($_GET['sn']);
$taskNum = (int)$_GET['tn'];
$newStatus = (int)$_GET['status'];

// Update query with corrected column name
$sql = "UPDATE tasks SET state = '$newStatus' WHERE project_short_name = '$shortName' AND task_num = $taskNum";

if ($connection->query($sql)) {
    // Redirect back to the board after a successful update
    header("Location: board.php?sn=" . urlencode($shortName));
    exit();
} else {
    echo '<div class="error-msg">Error updating status: ' . htmlspecialchars($connection->error) . '</div>';
    exit();
}

$connection->close();
?>
