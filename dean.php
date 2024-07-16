--Dean
<?php
// Start session and check if user is logged in and has the role of Dean
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'dean') {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Database connection

// Fetch departments under the faculty
$departmentsSql = "SELECT id, name FROM departments WHERE FacultyID = ?";
$deptStmt = $conn->prepare($departmentsSql);
$deptStmt->bind_param("i", $_SESSION['FacultyID']); // Assuming FacultyID is stored in session
$deptStmt->execute();
$departmentsResult = $deptStmt->get_result();

$selectedDept = $_POST['department'] ?? false;

// Fetch exams if a department is selected
if ($selectedDept) {
    $examSql = "SELECT exams.examName, exams.examDate, exams.examTime, courses.name AS courseName
                FROM exams
                JOIN courses ON exams.courseID = courses.id
                WHERE courses.departmentID = ?
                ORDER BY exams.examDate ASC, exams.examTime ASC";
    $examStmt = $conn->prepare($examSql);
    $examStmt->bind_param("i", $selectedDept);
    $examStmt->execute();
    $examResult = $examStmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Dean Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

    <form method="post">
        <label for="department">Select Department:</label>
        <select name="department" id="department" onchange="this.form.submit()">
            <option value="">Select a Department</option>
            <?php
            while ($row = $departmentsResult->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '"' . ($selectedDept == $row['id'] ? ' selected' : '') . '>' . $row['name'] . '</option>';
            }
            ?>
        </select>
    </form>

    <?php if ($selectedDept && $examResult->num_rows > 0): ?>
    <h2>Exams for Selected Department</h2>
    <table border="1">
        <tr>
            <th>Course Name</th>
            <th>Exam Name</th>
            <th>Exam Date</th>
            <th>Exam Time</th>
        </tr>
        <?php
        while ($exam = $examResult->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $exam['courseName'] . "</td>";
            echo "<td>" . $exam['examName'] . "</td>";
            echo "<td>" . $exam['examDate'] . "</td>";
            echo "<td>" . $exam['examTime'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <?php elseif ($selectedDept): ?>
    <p>No exams found for the selected department.</p>
    <?php endif; ?>

</body>
</html>

<?php
$deptStmt->close();
if ($selectedDept) {
    $examStmt->close();
}
$conn->close();
?>