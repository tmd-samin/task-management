<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

if (!(isset($_GET['sn']))) {
    header('Location: index.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    die("Error: " . $connection->connect_errno . "<br>Description: " . $connection->connect_error);
}

$short_name = $connection->real_escape_string($_GET['sn']);
?>

<?php include 'header.php'; ?>

<?php
// Fetch project details
$sql = "SELECT * FROM projects WHERE short_name = '$short_name'";
if ($result = $connection->query($sql)) {
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $projectName = htmlspecialchars($row['full_name']);
    } else {
        echo '<div class="container"><span class="error-msg">Project not found. Please check the URL or select a valid project.</span></div>';
        include 'footer.php';
        exit();
    }
    $result->free_result();
} else {
    echo '<div class="container"><span class="error-msg">Error fetching project: ' . htmlspecialchars($connection->error) . '</span></div>';
    include 'footer.php';
    exit();
}
?>

<div class="container task-list-container">
    <h1 class="text-center mb-4">Task List</h1>
    <h2>Current Project: <strong><?php echo $projectName; ?></strong></h2>
    <div class="row mb-4">
        <div class="col-md-6">
            <p class="whoami">
                <?php echo 'Logged in as <strong>' . htmlspecialchars($_SESSION['user']) . '</strong> <a href="logout.php" class="text-danger">[logout]</a>'; ?>
            </p>
        </div>
        <div class="col-md-12 text-md-end">
            <a href="newTask.php?sn=<?php echo urlencode($short_name); ?>" class="btn btn-primary">Create Task</a>
        </div>
    </div>
    <div class="mb-3">
        <a class="btn btn-secondary" href="index.php">&lt; Back to Projects</a>
    </div>
    <div class="row">
        <?php
        // Define task states
        $states = [
            1 => "Backlog",
            2 => "In Progress",
            3 => "Test",
            4 => "Done"
        ];

        foreach ($states as $state => $stateName) {
            echo "<div class='col-md-3 mb-4'><h3 class='text-center'>$stateName</h3><div>";

            $taskSql = "SELECT * FROM tasks WHERE project_short_name = '$short_name' AND state = $state";
            if ($taskResult = $connection->query($taskSql)) {
                if ($taskResult->num_rows > 0) {
                    while ($task = $taskResult->fetch_assoc()) {
                        // Corrected column name: `task_num` instead of `project_task_num`
                        $tn = htmlspecialchars($task['task_num']);
                        $taskName = htmlspecialchars($task['task_name']);
                        $taskId = htmlspecialchars($task['project_short_name']) . "-" . $tn;

                        echo "
                        <div class='card border-secondary mb-3'>
                            <div class='card-body'>
                                <h5 class='card-title'>$taskName</h5>
                                <p class='card-text text-muted'>Task ID: $taskId</p>
                                <a href='task.php?sn=$short_name&tn=$tn' class='btn btn-sm btn-outline-primary'>View Task</a>
                            </div>
                            <div class='card-footer'>
                                <select class='form-select form-select-sm' onchange='location = this.value'>
                                    <option selected disabled>Change Status</option>
                                    <option value='changeStatus.php?sn=$short_name&tn=$tn&status=1'>Backlog</option>
                                    <option value='changeStatus.php?sn=$short_name&tn=$tn&status=2'>In Progress</option>
                                    <option value='changeStatus.php?sn=$short_name&tn=$tn&status=3'>Test</option>
                                    <option value='changeStatus.php?sn=$short_name&tn=$tn&status=4'>Done</option>
                                </select>
                            </div>
                        </div>";
                    }
                } else {
                    echo "<div class='alert alert-info text-center'>No tasks in $stateName.</div>";
                }
                $taskResult->free_result();
            } else {
                echo "<div class='alert alert-danger text-center'>Error fetching tasks for $stateName: " . htmlspecialchars($connection->error) . "</div>";
            }
            echo "</div></div>";
        }
        ?>
    </div>
</div>


<?php $connection->close(); ?>
<?php include 'footer.php'; ?>
