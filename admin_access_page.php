<?php
require 'db.php';
session_start();

// Ensure the user is logged in and is an admin
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in'] || (isset($_SESSION['userType']) && $_SESSION['userType'] != 'admin')) {
    header("Location: index.html");
    exit;
}

// Handle the POST request when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $staffName = $_POST['staff_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $authorityLevel = $_POST['authority_level'];

    $sql = "INSERT INTO staff(staff_name, email, password, authority_level) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $staffName, $email, $password, $authorityLevel);

    if ($stmt->execute()) {
        echo "New staff created successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="db_style.css">
</head>
<body>
    <h2>Admin Dashboard</h2>
    <h3>Add New Staff</h3>
    <form action="" method="POST">
        <label for="staff_name">Staff Name:</label>
        <input type="text" name="staff_name" required><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br>

        <label for="authority_level">Authority Level:</label>
        <select name="authority_level" required>
            <option value="adder">Adder</option>
            <option value="checker">Checker</option>
            <option value="approver">Approver</option>
            <option value="admin">Admin</option>
        </select><br>

        <input type="submit" value="Add Staff">
    </form>
</body>
</br>
</br>
</br>
    <a href="admin_dashboard.php" style="font-family: 'Ubuntu', sans-serif;">
        <strong><u> go back to dashboard </u></strong>
    </a>
</html>
