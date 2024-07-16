<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

echo "Welcome " . $_SESSION['name'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Dashboard</h1>
    <?php
    if ($_SESSION['role'] == 'assistant') {
        include 'assistant.php';
    } elseif ($_SESSION['role'] == 'secretary') {
        include 'secretary.php';
    } elseif ($_SESSION['role'] == 'Head of department') {
        include 'head_of_department.php';
    } elseif ($_SESSION['role'] == 'Head of secretary') {
        include 'head_of_secretary.php';
    } elseif ($_SESSION['role'] == 'dean') {
        include 'dean.php';
    }
    ?>
</body>
</html>

