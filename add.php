<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Form</title>
    <link rel="stylesheet" href="style_customer.css">
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
</head>
<body>
    <?php
    include("db.php");
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $model = $_POST['model'];
        $version = $_POST['version'];
        $name = $_POST['name'];
        $type = $_POST['type'];
        $access = '';
        $status = "WIP_add";

        // First, insert product details
        $stmtProduct = $conn->prepare("INSERT INTO products (p_model, p_version, p_name, p_type, status) VALUES (?, ?, ?, ?, ?)");
        $stmtProduct->bind_param("sssss", $model, $version, $name, $type, $status);

        if (!$stmtProduct->execute()) {
            echo "Error: " . $stmtProduct->error;
            exit;
        }

        $lastProductId = $conn->insert_id;  // Get the ID of the last inserted product

        // Upload image data if available
        if (isset($_FILES['product_image']) && $_FILES['product_image']['error'] == 0) {
            $imageData = addslashes(file_get_contents($_FILES['product_image']['tmp_name']));
            $stmtImage = $conn->prepare("INSERT INTO product_image (image_data, p_id) VALUES (?, ?)");
            $stmtImage->bind_param("si", $imageData, $lastProductId);
            if (!$stmtImage->execute()) {
                echo "Error: " . $stmtImage->error;
                exit;
            }
        }
        
        // Insert product text as VARCHAR
        $productText = $_POST['product_text']; // Get the product text from the form
        $stmtText = $conn->prepare("INSERT INTO product_text (content, p_id) VALUES (?, ?)");
        $stmtText->bind_param("si", $productText, $lastProductId);

        if (!$stmtText->execute()) {
            echo "Error: " . $stmtText->error;
            exit;
        }

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
        header('Location: adder_dashboard.php');
        exit;
    }
    ?>

    <form method="post" action="" enctype="multipart/form-data" onsubmit="prepareFormData()">
        <table border="1" width="150">
            <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">
            <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

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
                <td>Product Image</td>
                <td>:</td>
                <td><input type="file" name="product_image" id="product_image"></td>
            </tr>
            <tr>
                <td>Product Description</td>
                <td>:</td>
                <td>
                    <!-- Replace this div with a Quill editor -->
                    <div id="editor" style="height: 150px;"></div>
                    <textarea name="product_text" id="product_text" style="display: none;"></textarea>
                </td>
            </tr>
            <script>
                const quill = new Quill('#editor', {
                    theme: 'snow',
                });

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

                function prepareFormData() {
                    const productText = quill.root.innerHTML; // Get the Quill editor's HTML content
                    document.getElementById('product_text').value = productText; // Set the value in the hidden textarea
                }

                // Call toggleFeatures() on page load
                window.onload = function () {
                    toggleFeatures();
                    updateName(); // Call updateName() as well if needed
                };
            </script>

            <tr>
                <td colspan="3" align="center">
                    <input type="hidden" name="features_json" id="features_json">
                    <input type="submit" value="SAVE">
                </td>
            </tr>
        </table>
    </form>
</body>
</html>
