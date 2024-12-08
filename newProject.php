<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}
?>
<?php include 'header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">New Project</h1>
    <div class="card p-4">
        <form method="post" action="newProjectValidation.php">
            <div class="mb-3">
                <label for="fullName" class="form-label">Full Project Name:</label>
                <input type="text" name="full" id="fullName" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="shortName" class="form-label">Short Project Name:</label>
                <input type="text" name="short" id="shortName" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Add New Project</button>
        </form>
        <?php
        if (isset($_SESSION['addProjectError'])) {
            echo '<div class="alert alert-danger mt-3">' . htmlspecialchars($_SESSION['addProjectError']) . '</div>';
            unset($_SESSION['addProjectError']);
        }
        ?>
    </div>
</div>

<?php include 'footer.php'; ?>
