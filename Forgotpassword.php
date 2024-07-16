<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'config.php';
    $username = $_POST['username'];

    $sql = "SELECT password FROM employees WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $password = $row['password'];
        echo "Your password is: " . $password;
    } else {
        echo "There is no account for the username";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
</head>
<body>
    <h1>Forgot Password</h1>
    <form method="POST">
        <label for="username">Enter your username:</label>
        <input type="text" name="username" id="username" required><br>
        <input type="submit" value="Show Password">
    </form>
</body>
</html>
