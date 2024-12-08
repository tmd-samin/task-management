<?php
session_start();

// Ensure the user is logged in
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

// Check if required parameters are provided
if (!isset($_GET['sn']) || !isset($_GET['tn'])) {
    $_SESSION['deleteTaskError'] = "Invalid task parameters.";
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

// Check for connection errors
if ($connection->connect_errno != 0) {
    $_SESSION['deleteTaskError'] = "Database connection failed: " . $connection->connect_error;
    header('Location: board.php?sn=' . urlencode($_GET['sn']));
    exit();
}

$shortName = $connection->real_escape_string($_GET['sn']);
$taskNum = (int)$_GET['tn'];

// Delete the task
$sql = "DELETE FROM tasks WHERE project_short_name = '$shortName' AND task_num = $taskNum";
if ($connection->query($sql)) {
    $_SESSION['deleteTaskSuccess'] = "Task deleted successfully.";
    $alertType = "success";
    $alertMessage = "Task deleted successfully.";
} else {
    $_SESSION['deleteTaskError'] = "Error deleting task: " . $connection->error;
    $alertType = "error";
    $alertMessage = "Error deleting task: " . htmlspecialchars($connection->error);
}

// Close the connection
$connection->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete Task</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
            icon: '<?php echo $alertType; ?>',
            title: '<?php echo $alertType === "success" ? "Success" : "Error"; ?>',
            text: '<?php echo $alertMessage; ?>',
            showConfirmButton: false,
            timer: 3000
        }).then(() => {
            window.location.href = 'board.php?sn=<?php echo urlencode($shortName); ?>';
        });
    });
</script>
</body>
</html>
