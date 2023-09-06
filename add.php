<?php
include("db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $model = $_POST['model'];
    $version = $_POST['version'];
    $name = $_POST['name'];
    $type = $_POST['type'];
    $access = ''; // Assuming you'll get this value from the form or somewhere
    $status = "WIP_add"; // Setting the status value

    // First, insert product details
    $stmtProduct = $conn->prepare("INSERT INTO products (p_model, p_version, p_name, p_type, status) VALUES (?, ?, ?, ?, ?)");
    $stmtProduct->bind_param("sssss", $model, $version, $name, $type, $status);

    
    if (!$stmtProduct->execute()) {
        echo "Error: " . $stmtProduct->error;
        exit;
    }

    $lastProductId = $conn->insert_id;  // Get the ID of the last inserted product

    // Prepare to insert features
    $stmtFeature = $conn->prepare("INSERT INTO features (f_name, f_value, f_access, product_id) VALUES (?, ?, ?, ?)");
    $stmtFeature->bind_param("sssi", $featureName, $featureValue, $access, $lastProductId);

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
        if (!$stmtFeature->execute()) {
            echo "Error: " . $stmtFeature->error;
            exit;
        }
    }

    echo "New record created successfully";
    header('Location: staffindex.php');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link rel="stylesheet" href="style_customer.css">

</head>
<body>
<form method="post" action="">

    <table border="1" width="150">
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">

        <tr>
            <td>Model</td>
            <td>:</td>
            <td><input type="text" name="model" id="model" oninput="updateName()"></td>
        </tr>
        <tr>
            <td>Version</td>
            <td>:</td>
            <td><input type="text" name="version" id="version" oninput="updateName()"></td>
        </tr>
        <tr>
            <td>Name</td>
            <td>:</td>
            <td><input type="text" name="name" id="name"></td>
        </tr>
        <tr>
            <td>Cost</td>
            <td>:</td>
            <td><input type="text" name="cost" id="cost"></td>
        </tr>
        <tr>
            <td>Price</td>
            <td>:</td>
            <td><input type="text" name="price" id="price"></td>
        </tr>
        <tr>
            <td>Type</td>
            <td>:</td>
            <td>
                <select name="type" id="type" onchange="toggleFeatures()">
                    <option value="Mast">Mast</option>
                    <option value="Accessory">Accessory</option>
                    <option value="Control Panel">Control Panel</option>
                </select>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <div id="featuresDiv" style="display: none;">
                    <table border="0" width="100%" id="featuresTable">
                        <tr>
                            <td>Extended Height(m)</td>
                            <td>:</td>
                            <td><input type="text" name="feature_Extended_Height_m"></td>
                        </tr>
                        <tr>
                            <td>Retracted Height(m)</td>
                            <td>:</td>
                            <td><input type="text" name="feature_Retracted_Height_m"></td>
                        </tr>
                        <tr>
                            <td>Weight(kg)</td>
                            <td>:</td>
                            <td><input type="text" name="feature_Weight_kg"></td>
                        </tr>
                        <tr>
                            <td>Number of Sections</td>
                            <td>:</td>
                            <td><input type="text" name="number_of_sections"></td>
                        </tr>
                        <tr>
                            <td>Maximum Payload Capacity(kg)</td>
                            <td>:</td>
                            <td><input type="text" name="maximum_payload_capacity_kg"></td>
                        </tr>
                    </table>
                </div>
            </td>
        </tr>
        <tr>
            <td colspan="3" align="center">
                <input type="hidden" name="features_json" id="features_json">
                <input type="submit" value="SAVE">
            </td>
        </tr>
    </table>
</form>

<script>
    function updateName() {
        const model = document.getElementById('model').value;
        const version = document.getElementById('version').value;
        document.getElementById('name').value = model + "-" + version;
    }

    function toggleFeatures() {
        const type = document.getElementById('type').value;
        const featuresDiv = document.getElementById('featuresDiv');
        featuresDiv.style.display = (type === 'Mast') ? 'block' : 'none';
    }

    function setNameAndFeatures() {
        updateName();

        const features = {
            'Cost': document.getElementsByName('cost')[0].value,
            'Price': document.getElementsByName('price')[0].value,
            'Extended Height(m)': document.getElementsByName('feature_Extended_Height_m')[0].value,
            'Retracted Height(m)': document.getElementsByName('feature_Retracted_Height_m')[0].value,
            'Weight(kg)': document.getElementsByName('feature_Weight_kg')[0].value,
            'Number of Sections': document.getElementsByName('number_of_sections')[0].value,
            'Maximum Payload Capacity(kg)': document.getElementsByName('maximum_payload_capacity_kg')[0].value
        };

        document.getElementById('features_json').value = JSON.stringify(features);
        
        return true;
    }

    // Call this function on page load to set the initial state
    window.onload = toggleFeatures;
</script>

</body>
</html>