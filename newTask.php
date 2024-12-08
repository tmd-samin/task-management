<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

// Ensure `sn` parameter is provided
if (!isset($_GET['sn'])) {
    header('Location: index.php');
    exit();
}

$shortName = htmlspecialchars($_GET['sn']);
?>

<?php include 'header.php'; ?>
<div class="container mt-5">
    <h1 class="text-center">New Task <span class="text-primary">(<?php echo $shortName; ?>)</span></h1>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow p-4">
                <form method="post" action="newTaskValidation.php?sn=<?php echo urlencode($shortName); ?>">
                    <div class="mb-3">
                        <label for="taskTitle" class="form-label">Title:</label>
                        <textarea name="taskTitle" id="taskTitle" class="form-control" rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="taskDescription" class="form-label">Description:</label>
                        <textarea name="taskDescription" id="taskDescription" class="form-control" rows="5" required></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">Add Task</button>
                    </div>
                </form>
                <?php
                if (isset($_SESSION['addProjectError'])) {
                    echo "<div class='alert alert-danger mt-3' role='alert'>" . htmlspecialchars($_SESSION['addProjectError']) . "</div>";
                    unset($_SESSION['addProjectError']);
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>
