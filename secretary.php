<?php
// Start session and check if user is logged in and has the secretary role
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'secretary') {
    header("Location: login.php");
    exit();
}

include 'config.php'; // Database connection

// Fetch courses for the dropdown
$coursesSql = "SELECT id, name FROM courses WHERE departmentID = ?";
$stmt = $conn->prepare($coursesSql);
$stmt->bind_param("i", $_SESSION['departmentID']); // Assuming department ID is stored in session
$stmt->execute();
$coursesResult = $stmt->get_result();

// Handle exam insertion
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['insert_exam'])) {
    $courseID = $_POST['course'];
    $examDate = $_POST['exam_date'];
    $examTime = $_POST['exam_time'];
    $numAssistants = $_POST['assistants_needed'];

    // Insert exam details
    $insertExamSql = "INSERT INTO exams (courseID, examDate, examTime, numClasses) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertExamSql);
    $stmt->bind_param("issi", $courseID, $examDate, $examTime, $numAssistants);
    $stmt->execute();
    $examID = $stmt->insert_id;
    $stmt->close();

    // Select assistants based on their scores and availability
    $assistantSql = "SELECT id FROM employees WHERE role = 'assistant' AND departmentID = ? ORDER BY (SELECT score FROM AssistantScore WHERE AssistantScore.assistantID = employees.id) ASC LIMIT ?";
    $stmt = $conn->prepare($assistantSql);
    $stmt->bind_param("ii", $_SESSION['departmentID'], $numAssistants);
    $stmt->execute();
    $assistantsResult = $stmt->get_result();

    // Output the selected assistants and update their scores
    while ($assistant = $assistantsResult->fetch_assoc()) {
        echo "→ " . $assistant['name'] . "<br>";

        // Update scores
        $updateScoreSql = "UPDATE AssistantScore SET score = score + 1 WHERE assistantID = ?";
        $scoreStmt = $conn->prepare($updateScoreSql);
        $scoreStmt->bind_param("i", $assistant['id']);
        $scoreStmt->execute();
        $scoreStmt->close();
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secretary Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

    <!-- Form to insert exam details -->
    <form method="post">
        <label for="course">Select Course:</label>
        <select name="course" id="course">
            <?php
            while ($row = $coursesResult->fetch_assoc()) {
                echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
            }
            ?>
        </select>
        <input type="date" name="exam_date" required>
        <input type="time" name="exam_time" required>
        <input type="number" name="assistants_needed" min="1" placeholder="Number of Assistants Needed" required>
        <input type="submit" name="insert_exam" value="Insert Exam">
    </form>

    <!-- Display for scores of all assistants -->
    <h2>Assistant Scores</h2>
    <table border="1">
        <tr>
            <th>Assistant Name</th>
            <th>Score</th>
        </tr>
        <?php
        $scoresSql = "SELECT employees.name, AssistantScore.score FROM employees JOIN AssistantScore ON employees.id = AssistantScore.assistantID WHERE employees.role = 'assistant' AND employees.departmentID = ?";
        $scoreStmt = $conn->prepare($scoresSql);
        $scoreStmt->bind_param("i", $_SESSION['departmentID']);
        $scoreStmt->execute();
        $scoresResult = $scoreStmt->get_result();
        while ($scoreRow = $scoresResult->fetch_assoc()) {
            echo "<tr><td>" . $scoreRow['name'] . "</td><td>" . $scoreRow['score'] . "</td></tr>";
        }
        $scoreStmt->close();
        ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>