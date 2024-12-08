<?php
session_start();
if (!(isset($_SESSION['logged-in']))) {
    header('Location: login.php');
    exit();
}

require_once "connect.php";

$connection = new mysqli($host, $db_user, $db_password, $db_name);

if ($connection->connect_errno != 0) {
    echo "Error: " . $connection->connect_errno . "<br>";
    echo "Description: " . $connection->connect_error;
    exit();
}
?>

<?php include 'header.php'; ?>

<div class="container projectListContainer">
    <h1>Projects list</h1>
    <div class="lg-6 whoami">
        <?php echo 'Logged in as <strong>' . htmlspecialchars($_SESSION['user']) . '</strong> <a href="logout.php">[logout]</a>'; ?>
    </div>
    <div class="lg-6 createBoard">
        <a href="newProject.php" class="btn">Create board</a>
    </div>
    <div class="lg-12">
        <table class="project-list">
            <thead>
                <tr>
                    <th>Full name</th>
                    <th>Short name</th>
                    <th>Tasks left</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM projects";

                if ($result = $connection->query($sql)) {
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $fullName = htmlspecialchars($row['full_name']);
                            $shortName = htmlspecialchars($row['short_name']);

                            $tasksLeft = 0;

                            $sumSQL = "SELECT COUNT(*) as tasksLeft FROM tasks WHERE project_short_name = '$shortName' AND state != 4";
                            if ($sumResult = $connection->query($sumSQL)) {
                                if ($row2 = $sumResult->fetch_assoc()) {
                                    $tasksLeft = $row2['tasksLeft'];
                                }
                                $sumResult->free_result();
                            }

                            echo "
                            <tr>
                                <td>$fullName</td>
                                <td>$shortName</td>
                                <td>$tasksLeft</td>
                                <td>
                                    <a href='board.php?sn=$shortName' class='btn'>Board</a>
                                    <a href='editProject.php?sn=$shortName' class='btn btn-edit'>Edit</a>
                                    <button class='btn btn-delete' onclick=\"confirmDelete('$shortName')\">Delete</button>
                                </td>
                            </tr>";
                        }
                        $result->free_result();
                    } else {
                        echo "<tr><td colspan='4'>No projects found.</td></tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>Error fetching projects: " . $connection->error . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<?php $connection->close(); ?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(shortName) {
        Swal.fire({
            title: 'Are you sure?',
            text: "This will permanently delete the project and all associated tasks.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'deleteProject.php?sn=' + encodeURIComponent(shortName);
            }
        });
    }

    <?php if (isset($_SESSION['deleteProjectSuccess'])) : ?>
    Swal.fire({
        icon: 'success',
        title: 'Deleted!',
        text: '<?php echo addslashes($_SESSION['deleteProjectSuccess']); ?>',
        showConfirmButton: false,
        timer: 3000
    });
    <?php unset($_SESSION['deleteProjectSuccess']); endif; ?>

    <?php if (isset($_SESSION['deleteProjectError'])) : ?>
    Swal.fire({
        icon: 'error',
        title: 'Error',
        text: '<?php echo addslashes($_SESSION['deleteProjectError']); ?>',
        showConfirmButton: true
    });
    <?php unset($_SESSION['deleteProjectError']); endif; ?>
</script>

<?php include 'footer.php'; ?>
