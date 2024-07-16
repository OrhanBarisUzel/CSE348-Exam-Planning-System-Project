<?php
session_start();
if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'Head of department') {
    header("Location: login.php");
    exit();
}

include 'config.php'; 


$examSql = "SELECT exams.examName, exams.examDate, exams.examTime, courses.name AS courseName
            FROM exams
            JOIN courses ON exams.courseID = courses.id
            WHERE courses.departmentID = ?
            ORDER BY exams.examDate ASC, exams.examTime ASC";
$stmt = $conn->prepare($examSql);
$stmt->bind_param("i", $_SESSION['departmentID']);
$stmt->execute();
$examResult = $stmt->get_result();

$scoresSql = "SELECT employees.name, AssistantScore.score, SUM(AssistantScore.score) AS total
              FROM employees
              JOIN AssistantScore ON employees.id = AssistantScore.assistantID
              WHERE employees.departmentID = ?
              GROUP BY employees.id";
$scoreStmt = $conn->prepare($scoresSql);
$scoreStmt->bind_param("i", $_SESSION['departmentID']);
$scoreStmt->execute();
$scoresResult = $scoreStmt->get_result();
$totalScores = 0;


while ($row = $scoresResult->fetch_assoc()) {
    $totalScores += $row['score'];
}

$scoresResult->data_seek(0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Head of Department Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

    <h2>Exam Schedule</h2>
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
            echo "<td>" .$exam['courseName'] . "</td>";
            echo "<td>" .$exam['examName'] . "</td>";
            echo "<td>" .$exam['examDate'] . "</td>";
            echo "<td>" .$exam['examTime'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>

    <h2>Assistant Workloads</h2>
    <table border="1">
        <tr>
            <th>Assistant Name</th>
            <th>Percentage</th>
        </tr>
        <?php
        if ($totalScores > 0) {
            while ($score = $scoresResult->fetch_assoc()) {
                $percentage = round(($score['score'] / $totalScores) * 100);
                echo "<tr>";
                echo "<td>" . $score['name'] . "</td>";
                echo "<td>" . $percentage . "%" . "</td>";
                echo "</tr>";
            }
        }
        $scoreStmt->close();
        ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>