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
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'checker') {
    header("Location: index.html");  // Redirect to a page informing them they are unauthorized
    exit;
}
$data = [];
$acceptedData = [];

$result = $conn->query("SELECT products.*, f1.f_value AS cost, f2.f_value AS price FROM products 
LEFT JOIN features AS f1 ON products.p_id = f1.product_id AND f1.f_name = 'cost'
LEFT JOIN features AS f2 ON products.p_id = f2.product_id AND f2.f_name = 'price'
WHERE products.status = 'WIP_check'");

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

$acceptedResult = $conn->query("SELECT products.*, f1.f_value AS cost, f2.f_value AS price FROM products 
LEFT JOIN features AS f1 ON products.p_id = f1.product_id AND f1.f_name = 'cost'
LEFT JOIN features AS f2 ON products.p_id = f2.product_id AND f2.f_name = 'price'
WHERE products.status = 'accepted'");

while ($row = $acceptedResult->fetch_assoc()) {
    $acceptedData[] = $row;
}

if(isset($_GET['approve']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE products SET status = 'accepted' WHERE p_id = {$id}");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
if(isset($_GET['publish']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE products SET status = 'published' WHERE p_id = {$id}");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
if(isset($_GET['delete']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE products SET status = 'WIP_add' WHERE p_id = {$id}");
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
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

    <h2>Products with Status: checked</h2>

    <table border='1' width='600'>
        <tr align='center' bgcolor='#042433'>
            <td>Model</td>
            <td>Version</td>
            <td>Name</td>
            <td>Type</td>
            <td>Cost</td>
            <td>Price</td>
            <td>Features</td>
            <td>Actions</td>
        </tr>

        <?php
        foreach ($data as $product) {
            echo "<tr>
                <td>{$product['p_model']}</td>
                <td>{$product['p_version']}</td>
                <td>{$product['p_name']}</td>
                <td>{$product['p_type']}</td>
                <td>{$product['cost']}</td>
                <td>{$product['price']}</td>
                <td><a href='view_features.php?id={$product['p_id']}'>View Features</a></td>
                <td>
                    <a href='?approve=1&id={$product['p_id']}'>Approve</a> | 
                    <a href='?delete=1&id={$product['p_id']}'>Delete Request</a>
                </td>
            </tr>";
        }
        if (empty($data)) {
            echo "<tr><td colspan='8'>No products found with status WIP_check.</td></tr>";
        }
        ?>
    </table>

    <br>

    <h2>Products with Status: accepted</h2>
    <table border='1' width='600'>
        <tr align='center' bgcolor='#042433'>
            <td>Model</td>
            <td>Version</td>
            <td>Name</td>
            <td>Type</td>
            <td>Cost</td>
            <td>Price</td>
            <td>Features</td>
            <td>Publish</td>
        </tr>

        <?php
        foreach ($acceptedData as $product) {
            echo "<tr>
                <td>{$product['p_model']}</td>
                <td>{$product['p_version']}</td>
                <td>{$product['p_name']}</td>
                <td>{$product['p_type']}</td>
                <td>{$product['cost']}</td>
                <td>{$product['price']}</td>
                <td><a href='view_features.php?id={$product['p_id']}'>View Features</a></td>
                <td><a href='?publish=1&id={$product['p_id']}'>Publish</a></td>
            </tr>";
        }
        if (empty($acceptedData)) {
            echo "<tr><td colspan='8'>No products found with status 'accepted'.</td></tr>";
        }
        ?>
    </table>

    <br>
    <a href="staffindex.php" style="font-family: 'Ubuntu', sans-serif;">
        <strong><u>Return to Main View</u></strong>
    </a>
</body>

</html>
