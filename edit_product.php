<?php
include("db.php");

$product = [];
$features = [];

// Fetch the existing data if there's an id parameter in the URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmtProduct = $conn->prepare("SELECT * FROM products WHERE p_id = ?");
    $stmtProduct->bind_param("i", $id);
    $stmtProduct->execute();
    $productResult = $stmtProduct->get_result();
    $product = $productResult->fetch_assoc();

    $stmtFeatures = $conn->prepare("SELECT f_name, f_value FROM features WHERE product_id = ?");
    $stmtFeatures->bind_param("i", $id);
    $stmtFeatures->execute();
    $featuresResult = $stmtFeatures->get_result();
    while ($feature = $featuresResult->fetch_assoc()) {
        $features[$feature['f_name']] = $feature['f_value'];
    }
}

// Process the form if it's submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Update product details
    $stmtProduct = $conn->prepare("UPDATE products SET p_model = ?, p_version = ?, p_name = ?, p_type = ? WHERE p_id = ?");
    $stmtProduct->bind_param("ssssi", $_POST['model'], $_POST['version'], $_POST['name'], $_POST['type'], $id);
    $stmtProduct->execute();

    // Update features
    $stmtFeature = $conn->prepare("UPDATE features SET f_value = ? WHERE product_id = ? AND f_name = ?");
    $stmtFeature->bind_param("sis", $featureValue, $id, $featureName);

    $featuresArr = array(
        "Cost" => $_POST['cost'],
        "Price" => $_POST['price'],
        "Extended Height(m)" => $_POST['feature_Extended_Height_m'],
        "Retracted Height(m)" => $_POST['feature_Retracted_Height_m'],
        "Weight(kg)" => $_POST['feature_Weight_kg'],
        "Number of Sections" => $_POST['number_of_sections'],
        "Maximum Payload Capacity(kg)" => $_POST['maximum_payload_capacity_kg']
    );

    foreach ($featuresArr as $featureName => $featureValue) {
        $stmtFeature->execute();
    }

    echo "Record updated successfully";
    header('Location: staffindex.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="style_customer.css">
</head>
<body>

<form method="post" action="">
    <table border="0" width="500">
        <!-- ... Other fields ... -->
        <tr>
            <td>Model</td>
            <td>:</td>
            <td><input type="text" name="model" value="<?= $product['p_model'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Version</td>
            <td>:</td>
            <td><input type="text" name="version" value="<?= $product['p_version'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Name</td>
            <td>:</td>
            <td><input type="text" name="name" value="<?= $product['p_name'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Type</td>
            <td>:</td>
            <td><input type="text" name="type" value="<?= $product['p_type'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Cost</td>
            <td>:</td>
            <td><input type="text" name="cost" value="<?= $features['Cost'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Price</td>
            <td>:</td>
            <td><input type="text" name="price" value="<?= $features['Price'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Extended Height(m)</td>
            <td>:</td>
            <td><input type="text" name="feature_Extended_Height_m" value="<?= $features['Extended Height(m)'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Retracted Height(m)</td>
            <td>:</td>
            <td><input type="text" name="feature_Retracted_Height_m" value="<?= $features['Retracted Height(m)'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Weight(kg)</td>
            <td>:</td>
            <td><input type="text" name="feature_Weight_kg" value="<?= $features['Weight(kg)'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Number of Sections</td>
            <td>:</td>
            <td><input type="text" name="number_of_sections" value="<?= $features['Number of Sections'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>Maximum Payload Capacity(kg)</td>
            <td>:</td>
            <td><input type="text" name="maximum_payload_capacity_kg" value="<?= $features['Maximum Payload Capacity(kg)'] ?? '' ?>"></td>
        </tr>
        <tr>
            <td>
                <input type="hidden" name="id" value="<?= $id ?>">
                <input type="submit" value="SAVE">
            </td>
        </tr>
    </table>
</form>

<a href="staffindex.php"><button>BACK TO MAIN TABLE</button></a>

</body>
</html>
