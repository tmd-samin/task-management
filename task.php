<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

if (!isset($_GET['sn']) || !isset($_GET['tn'])) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);
if ($connection->connect_errno != 0) {
    die("Database connection failed: " . $connection->connect_error);
}

$shortName = $connection->real_escape_string($_GET['sn']);
$taskNum = (int)$_GET['tn'];

// Updated column name to `task_num`
$sql = "SELECT * FROM tasks WHERE project_short_name = '$shortName' AND task_num = $taskNum";
if ($result = $connection->query($sql)) {
    if ($result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo '<div class="alert alert-danger text-center">Task not found.</div>';
        exit();
    }
} else {
    echo '<div class="alert alert-danger text-center">Error fetching task: ' . $connection->error . '</div>';
    exit();
}
?>

<?php include 'header.php'; ?>
<div class="d-flex justify-content-center align-items-center vh-100">
    <div class="card col-md-6">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0"><?php echo htmlspecialchars($task['task_name']); ?></h2>
            <p class="mb-0">Task Short Name: <?php echo htmlspecialchars($shortName); ?></p>
            <p class="mb-0">Task Number: <?php echo htmlspecialchars($taskNum); ?></p>
        </div>
        <div class="card-body text-center">
            <p><?php echo nl2br(htmlspecialchars($task['task_desc'])); ?></p>
            <div class="d-flex justify-content-around">
                <a class="btn btn-secondary" href="board.php?sn=<?php echo urlencode($shortName); ?>">Back to Board</a>
                <a class="btn btn-danger" href="deleteTask.php?sn=<?php echo urlencode($shortName); ?>&tn=<?php echo $taskNum; ?>">Delete Task</a>
            </div>
        </div>
    </div>
</div>
<?php $connection->close(); ?>
<?php include 'footer.php'; ?>
