<?php
require 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['c_name'];
    $email = $_POST['c_email'];
    $password = $_POST['pwd'];
    $inputUserType = $_POST['userType'];

    // If the user type is "customer", set access level to "c". Else, use the input value (assuming 'staff' or other values)
    $accessLevel = ($inputUserType == 'customer') ? 'c' : $inputUserType;

    $sql = "INSERT INTO customer(c_name, c_email, pwd, c_access_level) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $email, $password, $accessLevel);

    if ($stmt->execute()) {
        echo "New record created successfully";
        // Here you can also redirect to a login page or dashboard
        header('Location: index.php');
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
