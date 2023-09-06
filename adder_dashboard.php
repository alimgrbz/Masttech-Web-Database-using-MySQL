<?php



error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.html");
    exit;
}

// Check if the user is not an 'adder' staff
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'adder') {
    header("Location: index.html");  // Redirect to a page informing them they are unauthorized
    exit;
}


$status = 'WIP_add';
$data = [];

$result = $conn->query("SELECT products.*, f1.f_value AS cost, f2.f_value AS price FROM products 
LEFT JOIN features AS f1 ON products.p_id = f1.product_id AND f1.f_name = 'cost'
LEFT JOIN features AS f2 ON products.p_id = f2.product_id AND f2.f_name = 'price'
WHERE products.status = '{$status}'");

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masttech Product Database - WIP_add</title>
    <link rel="stylesheet" href="db_style.css">
</head>
<body>
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">
    <br>

    <h2>Products</h2>

    <table border='1' width='600'>
        <tr align='center' bgcolor='#042433'>
            <td>Model</td>
            <td>Version</td>
            <td>Name</td>
            <td>Type</td>
            <td>Cost</td>
            <td>Price</td>
            <td>Features</td>
            <td colspan='2'>Action</td>
        </tr>

        <?php
        if (!empty($data)) {
            foreach ($data as $product) {
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
            echo "<tr><td colspan='8'>No products found with status WIP_add.</td></tr>";
        }
        ?>

    </table><br>

    <a href="add.php"><button class="custom-link">ADD DATA</button></a><br><br>
    <a href="staffindex.php" style="font-family: 'Ubuntu', sans-serif;"><strong><u>Return to Main View</u></strong></a>

</body>
</html>
