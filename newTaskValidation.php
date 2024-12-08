<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

// Check if required parameters are present
if (!isset($_GET['sn']) || !isset($_POST['taskTitle']) || !isset($_POST['taskDescription'])) {
    $_SESSION['addProjectError'] = "All fields are required.";
    header('Location: newTask.php?sn=' . urlencode($_GET['sn']));
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno != 0) {
    $_SESSION['addProjectError'] = "Database connection failed.";
    header('Location: newTask.php?sn=' . urlencode($_GET['sn']));
    exit();
}

$shortName = $connection->real_escape_string($_GET['sn']);
$taskTitle = $connection->real_escape_string(trim($_POST['taskTitle']));
$taskDescription = $connection->real_escape_string(trim($_POST['taskDescription']));

// Get the next task number for the project
$sqlCount = "SELECT COUNT(*) as taskCount FROM tasks WHERE project_short_name = '$shortName'";
if ($result = $connection->query($sqlCount)) {
    $taskData = $result->fetch_assoc();
    $taskCount = $taskData['taskCount'] + 1;

    // Corrected column name: `task_num` instead of `project_task_num`
    $sql = "INSERT INTO tasks (id, project_short_name, task_num, task_name, task_desc, state)
            VALUES (NULL, '$shortName', '$taskCount', '$taskTitle', '$taskDescription', 1)";

    if ($connection->query($sql)) {
        header('Location: board.php?sn=' . urlencode($shortName));
        exit();
    } else {
        $_SESSION['addProjectError'] = "Error adding task: " . $connection->error;
        header('Location: newTask.php?sn=' . urlencode($shortName));
        exit();
    }
} else {
    $_SESSION['addProjectError'] = "Error fetching task count: " . $connection->error;
    header('Location: newTask.php?sn=' . urlencode($shortName));
    exit();
}

$connection->close();
?>
