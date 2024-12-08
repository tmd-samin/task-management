<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

require_once "connect.php";
$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    die("Database connection failed: " . $connection->connect_error);
}

// Fetch project data
if (isset($_GET['sn'])) {
    $shortName = $connection->real_escape_string($_GET['sn']);
    $sql = "SELECT * FROM projects WHERE short_name = '$shortName'";
    $result = $connection->query($sql);

    if ($result && $result->num_rows > 0) {
        $projectData = $result->fetch_assoc();
    } else {
        $_SESSION['newProjectSuccess'] = "Project not found.";
        header('Location: index.php');
        exit();
    }
} else {
    header('Location: index.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $fullName = $connection->real_escape_string($_POST['fullName']);
    $newShortName = $connection->real_escape_string($_POST['shortName']);
    $originalShortName = $connection->real_escape_string($_POST['originalShortName']);

    $updateSQL = "UPDATE projects SET full_name = '$fullName', short_name = '$newShortName' WHERE short_name = '$originalShortName'";

    if ($connection->query($updateSQL)) {
        $_SESSION['newProjectSuccess'] = "Project updated successfully.";
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Project Updated',
                        text: 'The project details have been successfully updated.',
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        window.location.href = 'index.php';
                    });
                });
              </script>";
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'There was an issue updating the project. Please try again.'
                    });
                });
              </script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Project</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="/tasker/styles.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h1>Edit Project</h1>
        <form method="POST" action="">
            <div class="form-group">
                <label for="fullName">Full Name</label>
                <input
                    type="text"
                    id="fullName"
                    name="fullName"
                    class="form-control"
                    value="<?php echo htmlspecialchars($projectData['full_name']); ?>"
                    required
                >
            </div>
            <div class="form-group">
                <label for="shortName">Short Name</label>
                <input
                    type="text"
                    id="shortName"
                    name="shortName"
                    class="form-control"
                    value="<?php echo htmlspecialchars($projectData['short_name']); ?>"
                    required
                >
            </div>
            <input type="hidden" name="originalShortName" value="<?php echo htmlspecialchars($projectData['short_name']); ?>">
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Update</button>
                <a href="index.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
