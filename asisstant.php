<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'assistant') {
    header("Location: login.php");
    exit();
}

include 'config.php';

// Debugging: Check session values
echo "<pre>";
print_r($_SESSION);
echo "</pre>";

// Ensure the assistantID and departmentID are set
if (!isset($_SESSION['id']) || !isset($_SESSION['departmentID'])) {
    echo "Session variables are not set correctly.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['course'])) {
    $courseID = $_POST['course'];
    $assistantID = $_SESSION['id'];

    $updateSql = "UPDATE schedule SET monday = 'Unavailable' WHERE assistantID = ? AND courseID = ?";
    $stmt = $conn->prepare($updateSql);
    if ($stmt) {
        $stmt->bind_param("ii", $assistantID, $courseID);
        $stmt->execute();
        $stmt->close();
    } else {
        echo "Failed to prepare statement for updating schedule.";
    }
}

$selectSql = "SELECT id, name FROM courses WHERE departmentID = ?";
$stmt = $conn->prepare($selectSql);
if ($stmt) {
    $stmt->bind_param("i", $_SESSION['departmentID']);
    $stmt->execute();
    $result = $stmt->get_result();
} else {
    echo "Failed to prepare statement for selecting courses.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assistant Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['name']; ?></h1>

    <form method="post">
        <label for="course">Select Course:</label>
        <select name="course" id="course">
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
            } else {
                echo '<option value="">No courses available</option>';
            }
            ?>
        </select>
        <input type="submit" value="Mark as Unavailable">
    </form>

    <h2>Weekly Plan</h2>
    <table border="1">
        <tr>
            <th>Time Slot</th>
            <th>Monday</th>
            <th>Tuesday</th>
            <th>Wednesday</th>
            <th>Thursday</th>
            <th>Friday</th>
        </tr>
        <?php
        $scheduleSql = "SELECT * FROM schedule WHERE assistantID = ?";
        $stmt = $conn->prepare($scheduleSql);
        if ($stmt) {
            $stmt->bind_param("i", $_SESSION['id']);
            $stmt->execute();
            $scheduleResult = $stmt->get_result();

            if ($scheduleResult && $scheduleResult->num_rows > 0) {
                while ($row = $scheduleResult->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['timeSlot'] . "</td>";
                    echo "<td>" . $row['monday'] . "</td>";
                    echo "<td>" . $row['tuesday'] . "</td>";
                    echo "<td>" . $row['wednesday'] . "</td>";
                    echo "<td>" . $row['thursday'] . "</td>";
                    echo "<td>" . $row['friday'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="6">No schedule available</td></tr>';
            }
            $stmt->close();
        } else {
            echo "<tr><td colspan='6'>Failed to prepare statement for selecting schedule.</td></tr>";
        }
        ?>
    </table>
</body>
</html>

<?php $conn->close(); ?>
