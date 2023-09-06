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
if (!isset($_SESSION['userType']) || $_SESSION['userType'] !== 'approver') {
    header("Location: index.html");  // Redirect to a page informing them they are unauthorized
    exit;
}


$statuses = ['WIP_add', 'WIP_check'];
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

if(isset($_GET['change_to_check']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $conn->query("UPDATE products SET status = 'WIP_check' WHERE p_id = {$id}");
    header("Location: ".$_SERVER['PHP_SELF']);  // Redirect back to the same page to reflect changes
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
            <td>Status</td>
            <td>Features</td>";
    if ($status == 'WIP_add') {
        echo "<td>Action</td>";
    }
    echo "</tr>";

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
            <td>{$status}</td>
            <td><a href='view_features.php?id={$id}'>View Features</a></td>";
        
            if ($status == 'WIP_add') {
                echo "<td><a href='edit_product.php?id=$id'>EDIT</a> | <a href='?change_to_check=1&id={$id}'> mark as CHECKED</a></td>";
            }
            echo "</tr>";
        }
    } else {
        // Adjusting the colspan based on the status
        $colspan = ($status == 'WIP_add') ? 9 : 8;
        echo "<tr><td colspan='{$colspan}'>No products found with status {$status}.</td></tr>";
    }
    echo "</table><br>";
}
?>

    <br>
    <!-- The "ADD DATA" button is removed from here -->
    <a href="staffindex.php" style="font-family: 'Ubuntu', sans-serif;">
        <strong><u>Return to Main View</u></strong>
    </a>

</body>
</html>
