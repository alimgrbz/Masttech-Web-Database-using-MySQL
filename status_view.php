<?php

session_start(); // Start the session.

// Redirect to login page if not authenticated.
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    header('Location: index.html'); // Adjust the path to your actual login page.
    exit;
}



error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");

$statuses = ['WIP_add', 'WIP_check', 'accepted', 'published'];
$data = [];

foreach ($statuses as $status) {
    $result = $conn->query("SELECT products.*, f1.f_value AS cost, f2.f_value AS price FROM products 
    LEFT JOIN features AS f1 ON products.p_id = f1.product_id AND f1.f_name = 'cost'
    LEFT JOIN features AS f2 ON products.p_id = f2.product_id AND f2.f_name = 'price'
    WHERE products.status = '{$status}'");

    while ($row = $result->fetch_assoc()) {
        $data[$status][] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masttech Product Database By Status</title>
    <link rel="stylesheet" href="db_style.css">
</head>
<body>
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">
    <br>

    <?php
    foreach ($statuses as $status) {
        echo "<h2>Products with Status: {$status}</h2>";

        echo "<table border='1' width='600'>
            <tr align='center' bgcolor='#042433'>
                <td>Model</td>
                <td>Version</td>
                <td>Name</td>
                <td>Type</td>
                <td>Cost</td>
                <td>Price</td>
                <td>Features</td>
                <td colspan='2'>Action</td>
            </tr>";

        if (isset($data[$status]) && !empty($data[$status])) {
            foreach ($data[$status] as $product) {
                $id = $product['p_id'] ?? '';
                $model = $product['p_model'] ?? '';
                $version = $product['p_version'] ?? '';
                $name = $product['p_name'] ?? '';
                $type = $product['p_type'] ?? '';
                $cost = $product['cost'] ?? ''; 
                $price = $product['price'] ?? ''; 

                echo "<tr>
                    <td>{$model}</td>
                    <td>{$version}</td>
                    <td>{$name}</td>
                    <td>{$type}</td>
                    <td>{$cost}</td>
                    <td>{$price}</td>
                    <td><a href='view_features.php?id={$id}'>View Features</a></td>
                    <td><a href='edit_product.php?id=$id'>EDIT</a></td>
                    <td><a href='delete_product.php?id=$id'>DELETE</a></td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='9'>No products found with status {$status}.</td></tr>";
        }

        echo "</table><br>";
    }
    ?>

    <br>
    <a href="add.php"><button class="custom-link">ADD DATA</button></a><br>
    <br>
    <a href="staffindex.php" style="font-family: 'Ubuntu', sans-serif;">
        <strong><u>Return to Main View</u></strong>
    </a>

</body>
</html>
