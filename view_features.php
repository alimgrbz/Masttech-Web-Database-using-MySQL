<?php


session_start(); // Start the session.

// Redirect to login page if not authenticated.
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.html'); // Adjust the path to your actual login page.
    exit;
}



include("db.php");

$id = $_GET['id'] ?? null;

$features = [];

if ($id) {
    $stmt = $conn->prepare("SELECT f_name, f_value FROM features WHERE product_id = ?");  // Ensure the column name matches your database
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $features[$row['f_name']] = $row['f_value'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Features</title>
    <link rel="stylesheet" href="style_customer.css">
</head>

<body>
    <h1>Product Features</h1>
    <table border="1" width="300">
        <tr bgcolor="#042433">
            <td>Feature</td>
            <td>Value</td>
        </tr>
        <?php
        if(!empty($features)) {
            foreach ($features as $feature => $value) {
                echo "<tr>
                    <td>{$feature}</td>
                    <td>{$value}</td>
                </tr>";
            }
        } else {
            echo "<tr>
                <td colspan='2'>No features found for the selected product.</td>
            </tr>";
        }
        ?>
    </table>
    <br>
    <a href="javascript:history.back()"><strong><u>Back to Main Page</u></strong></a>
</body>
</html>
