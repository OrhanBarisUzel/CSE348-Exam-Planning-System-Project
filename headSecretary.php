--Head of secretary
<?php

session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Head of secretary') {
    header("Location: login.php");
    exit();
}

include 'config.php'; 


$coursesSql = "SELECT id, name FROM courses WHERE FacultyID = ?";
$stmt = $conn->prepare($coursesSql);
$stmt->bind_param("i", $_SESSION['FacultyID']); 
$stmt->execute();
$coursesResult = $stmt->get_result();

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['insert_exam'])) {
    $courseID = $_POST['course'];
    $examDate = $_POST['exam_date'];
    $examTime = $_POST['exam_time'];
    $numAssistants = $_POST['assistants_needed'];

    $insertExamSql = "INSERT INTO exams (courseID, examDate, examTime, numClasses) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($insertExamSql);
    $stmt->bind_param("issi", $courseID, $examDate, $examTime, $numAssistants);
    $stmt->execute();
    $examID = $stmt->insert_id;
    $stmt->close();
    
    $assistantSql = "SELECT employees.id, employees.name FROM employees 
                     JOIN departments ON employees.departmentID = departments.id
                     WHERE departments.FacultyID = ? AND employees.role = 'assistant'
                     ORDER BY (SELECT score FROM AssistantScore WHERE AssistantScore.assistantID = employees.id) ASC
                     LIMIT ?";
    $stmt = $conn->prepare($assistantSql);
    $stmt->bind_param("ii", $_SESSION['FacultyID'], $numAssistants);
    $stmt->execute();
    $assistantsResult = $stmt->get_result();

    while ($assistant = $assistantsResult->fetch_assoc()) {
        echo " " . $assistant['name'] . "<br>";

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
    <title>Head of Secretary Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

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
</body>
</html>

<?php $conn->close(); ?>