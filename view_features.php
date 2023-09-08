<?php
include("db.php");
session_start();

// Check if the user is not logged in
if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
    header("Location: index.html");
    exit;
}

$id = $_GET['id'] ?? null;
$features = [];
$image_data = null;
$product_text = null; // Initialize product_text variable

if ($id) {
    // Fetching product features
    $stmt = $conn->prepare("SELECT f_name, f_value FROM features WHERE product_id = ?");
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $features[$row['f_name']] = $row['f_value'];
    }

    // Fetching product image
    $stmt_image = $conn->prepare("SELECT image_data FROM product_image WHERE p_id = ?");
    $stmt_image->bind_param("s", $id);
    $stmt_image->execute();
    $result_image = $stmt_image->get_result();

    if ($result_image->num_rows > 0) {
        $row_image = $result_image->fetch_assoc();
        $image_data = $row_image['image_data'];
    }

    // Fetching product text
    $stmt_text = $conn->prepare("SELECT content FROM product_text WHERE p_id = ?");
    $stmt_text->bind_param("s", $id);
    $stmt_text->execute();
    $result_text = $stmt_text->get_result();

    if ($result_text->num_rows > 0) {
        $row_text = $result_text->fetch_assoc();
        $product_text = $row_text['content'];
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
    <style>
        .product-info-container {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 20px;
        }

        .product-info-box {
            flex: 1;
            border: 1px solid #ccc;
            padding: 10px;
        }

        .product-image {
            flex: 1;
            max-width: 300px;
            padding: 10px;
        }

        .product-description {
            background-color: #748fad;
        }

        .custom-heading {
            font-weight: bold;
            color: #0e3a6e;
            text-decoration: underline;
        }

        table {
            margin-top: 20px;
            border-collapse: collapse;
            width: 100%;
        }

        table, th, td {
            border: 1px solid #042433;
        }

        th, td {
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #042433;
            color: white;
        }
    </style>
</head>
<body>
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo"> 
    <h1>Product Features</h1>
    <div class="product-info-container">
        <?php if ($image_data) : ?>
            <div class="product-image">
                <?php
                // Determine the image format (JPEG or PNG) based on the data
                $imageFormat = 'jpeg'; // Change to 'png' if PNG format
                $base64Image = 'data:image/' . $imageFormat . ';base64,' . base64_encode($image_data);
                ?>
                <img src="<?php echo $base64Image; ?>" alt="Image">
            </div>
        <?php endif; ?>
        <?php if ($product_text) : ?>
            <div class="product-info-box product-description">
                <h4 class="custom-heading">Product Information:</h4>
                <p><?php echo $product_text; ?></p>
            </div>
        <?php endif; ?>
    </div>

    <table>
        <tr bgcolor="#cfd0d1"> 
            <th>Feature</th>
            <th>Value</th>
        </tr>
        <?php
        if (!empty($features)) {
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
    <a href="javascript:history.back()"><strong><u> Back to Main View</u></strong></a>
</body>
</html>
