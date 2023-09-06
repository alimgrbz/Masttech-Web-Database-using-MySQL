<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");

$data = [];

$result = $conn->query("SELECT products.*, f1.f_value AS cost, f2.f_value AS price FROM products 
LEFT JOIN features AS f1 ON products.p_id = f1.product_id AND f1.f_name = 'cost'
LEFT JOIN features AS f2 ON products.p_id = f2.product_id AND f2.f_name = 'price'");

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masttech Product Database</title>
    <link rel="stylesheet" href="db_style.css">
</head>
<body>
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">
    <br>

    <script>
        function changeColorOnHover(row) {
            if (row.classList.contains('accepted')) {
                row.style.backgroundColor = 'green';
            }
        }

        function revertColor(row) {
            if (row.classList.contains('accepted')) {
                row.style.backgroundColor = '';
            }
        }
    </script>

    <table border="1" width="600">
        <tr align="center" bgcolor="#042433">
            <td>Model</td>
            <td>Version</td>
            <td>Name</td>
            <td>Type</td>
            <td>Cost</td>
            <td>Price</td>
            <td>Status</td> <!-- Added column for Status -->
            <td>Features</td>
            <td colspan="2">Action</td>
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
                $status = $product['status'] ?? '';  // Fetch status

                echo "<tr class='{$status}' onmouseover='changeColorOnHover(this)' onmouseout='revertColor(this)'>
                    <td>{$model}</td>
                    <td>{$version}</td>
                    <td>{$name}</td>
                    <td>{$type}</td>
                    <td>{$cost}</td>
                    <td>{$price}</td>
                    <td>{$status}</td>  <!-- Display status -->
                    <td><a class='subtle-button' href='view_features.php?id={$id}'>View Features</a></td>
                    <td><a href='edit_product.php?id=$id'>EDIT</a></td>
                    <td><a href='delete_product.php?id=$id'>DELETE</a></td>
                </tr>";
            }
        }
        ?>
    </table> 

    <br>
    <a href="add.php"><button class="custom-link">ADD DATA</button></a><br>
    <br>
    <a href="customerindex.php" style="font-family: 'Ubuntu', sans-serif;">
        <strong><u>See the options for customers</u></strong>
    </a>
</body>
</html>
