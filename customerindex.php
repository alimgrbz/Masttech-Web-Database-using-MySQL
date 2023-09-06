<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("db.php");

$order_by = 'p_model';
$order_dir = 'ASC';

if (isset($_GET['order_by'])) {
    $order_by = $_GET['order_by'];
}

if (isset($_GET['order_dir'])) {
    $order_dir = $_GET['order_dir'];
}
$p_type_filter = isset($_GET['p_type']) ? $_GET['p_type'] : '';

// Capture filter inputs
$min_price_filter = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? $_GET['min_price'] : null;
$max_price_filter = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? $_GET['max_price'] : null;

$feature_Extended_Height_m_min = isset($_GET['feature_Extended_Height_m_min']) ? $_GET['feature_Extended_Height_m_min'] : '';
$feature_Extended_Height_m_max = isset($_GET['feature_Extended_Height_m_max']) ? $_GET['feature_Extended_Height_m_max'] : '';

$feature_Retracted_Height_m_min = isset($_GET['feature_Retracted_Height_m_min']) ? $_GET['feature_Retracted_Height_m_min'] : '';
$feature_Retracted_Height_m_max = isset($_GET['feature_Retracted_Height_m_max']) ? $_GET['feature_Retracted_Height_m_max'] : '';

$feature_Weight_kg_min = isset($_GET['feature_Weight_kg_min']) ? $_GET['feature_Weight_kg_min'] : '';
$feature_Weight_kg_max = isset($_GET['feature_Weight_kg_max']) ? $_GET['feature_Weight_kg_max'] : '';

$number_of_sections_min = isset($_GET['number_of_sections_min']) ? $_GET['number_of_sections_min'] : '';
$number_of_sections_max = isset($_GET['number_of_sections_max']) ? $_GET['number_of_sections_max'] : '';

$maximum_payload_capacity_kg_min = isset($_GET['maximum_payload_capacity_kg_min']) ? $_GET['maximum_payload_capacity_kg_min'] : '';
$maximum_payload_capacity_kg_max = isset($_GET['maximum_payload_capacity_kg_max']) ? $_GET['maximum_payload_capacity_kg_max'] : '';

$filter_clauses = ["products.status = 'published'"];

if ($p_type_filter !== '') {
    $filter_clauses[] = "products.p_type = '{$p_type_filter}'";
}

// Price filter
if ($min_price_filter !== null) {
    $filter_clauses[] = "priceFeature.f_value >= {$min_price_filter}";
}
if ($max_price_filter !== null) {
    $filter_clauses[] = "priceFeature.f_value <= {$max_price_filter}";
}

// Extended Height(m) filter
if ($feature_Extended_Height_m_min !== '') {
    $filter_clauses[] = "extendedHeightFeature.f_value >= {$feature_Extended_Height_m_min}";
}
if ($feature_Extended_Height_m_max !== '') {
    $filter_clauses[] = "extendedHeightFeature.f_value <= {$feature_Extended_Height_m_max}";
}

// Retracted Height(m) filter
if ($feature_Retracted_Height_m_min !== '') {
    $filter_clauses[] = "retractedHeightFeature.f_value >= {$feature_Retracted_Height_m_min}";
}
if ($feature_Retracted_Height_m_max !== '') {
    $filter_clauses[] = "retractedHeightFeature.f_value <= {$feature_Retracted_Height_m_max}";
}

// Weight(kg) filter
if ($feature_Weight_kg_min !== '') {
    $filter_clauses[] = "weightFeature.f_value >= {$feature_Weight_kg_min}";
}
if ($feature_Weight_kg_max !== '') {
    $filter_clauses[] = "weightFeature.f_value <= {$feature_Weight_kg_max}";
}

// Number of Sections filter
if ($number_of_sections_min !== '') {
    $filter_clauses[] = "sectionsFeature.f_value >= {$number_of_sections_min}";
}
if ($number_of_sections_max !== '') {
    $filter_clauses[] = "sectionsFeature.f_value <= {$number_of_sections_max}";
}

// Maximum Payload Capacity(kg) filter
if ($maximum_payload_capacity_kg_min !== '') {
    $filter_clauses[] = "payloadFeature.f_value >= {$maximum_payload_capacity_kg_min}";
}
if ($maximum_payload_capacity_kg_max !== '') {
    $filter_clauses[] = "payloadFeature.f_value <= {$maximum_payload_capacity_kg_max}";
}


// Construct the WHERE clause
$where_clause = implode(' AND ', $filter_clauses);

$query = "SELECT products.*, priceFeature.f_value AS price 
          FROM products 
          LEFT JOIN features AS priceFeature ON products.p_id = priceFeature.product_id AND priceFeature.f_name = 'price'
          LEFT JOIN features AS extendedHeightFeature ON products.p_id = extendedHeightFeature.product_id AND extendedHeightFeature.f_name = 'Extended Height(m)'
          LEFT JOIN features AS retractedHeightFeature ON products.p_id = retractedHeightFeature.product_id AND retractedHeightFeature.f_name = 'Retracted Height(m)'
          LEFT JOIN features AS weightFeature ON products.p_id = weightFeature.product_id AND weightFeature.f_name = 'Weight(kg)'
          LEFT JOIN features AS sectionsFeature ON products.p_id = sectionsFeature.product_id AND sectionsFeature.f_name = 'Number of Sections'
          LEFT JOIN features AS payloadFeature ON products.p_id = payloadFeature.product_id AND payloadFeature.f_name = 'Maximum Payload Capacity(kg)'
          WHERE {$where_clause}
          ORDER BY {$order_by} {$order_dir}";



$result = $conn->query($query);
$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

function toggleOrder($currentDir) {
    return $currentDir === 'ASC' ? 'DESC' : 'ASC';
}
?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masttech Product Database - Products</title>
    <link rel="stylesheet" href="db_style.css">
    <style>
        input[type="number"] {
            width: 80px;
            padding: 5px;
            margin: 5px 0;
        }

        td {
            padding: 10px;
        }

        /* Additional styles */
        .feature-filters {
            background-color: #e1e5e8;
            margin: 20px 0;
            padding: 15px;
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
        }

        .feature-set {
            margin-right: 10px;
            border: 1px solid #ccc;
            padding: 10px;
            border-radius: 5px;
            flex: 1;
        }

        .feature-set:last-child {
            margin-right: 0;
        }
    </style>
</head>

<body>
    <img src="masttech-logo.png" alt="Masttech Logo" id="masttechLogo">
    <br>
    <h2>Products</h2>

    <form action="" method="get">
        <div class="feature-filters">

            <fieldset class="feature-set">
                <legend>Type</legend>
                <select name="p_type">
                    <option value="">-- Select Type --</option>
                    <option value="mast" <?php echo $p_type_filter === 'mast' ? 'selected' : ''; ?>>Mast</option>
                    <option value="accessory" <?php echo $p_type_filter === 'accessory' ? 'selected' : ''; ?>>Accessory</option>
                    <option value="control panel" <?php echo $p_type_filter === 'control panel' ? 'selected' : ''; ?>>Control Panel</option>
                </select>
            </fieldset>

            <fieldset class="feature-set">
                <legend>Extended Height(m)</legend>
                Min: <input type="number" name="feature_Extended_Height_m_min" value="<?php echo $feature_Extended_Height_m_min; ?>">
                Max: <input type="number" name="feature_Extended_Height_m_max" value="<?php echo $feature_Extended_Height_m_max; ?>">
            </fieldset>

            <fieldset class="feature-set">
                <legend>Retracted Height(m)</legend>
                Min: <input type="number" name="feature_Retracted_Height_m_min" value="<?php echo $feature_Retracted_Height_m_min; ?>">
                Max: <input type="number" name="feature_Retracted_Height_m_max" value="<?php echo $feature_Retracted_Height_m_max; ?>">
            </fieldset>

            <fieldset class="feature-set">
                <legend>Weight(kg)</legend>
                Min: <input type="number" name="feature_Weight_kg_min" value="<?php echo $feature_Weight_kg_min; ?>">
                Max: <input type="number" name="feature_Weight_kg_max" value="<?php echo $feature_Weight_kg_max; ?>">
            </fieldset>

            <fieldset class="feature-set">
                <legend>Number of Sections</legend>
                Min: <input type="number" name="number_of_sections_min" value="<?php echo $number_of_sections_min; ?>">
                Max: <input type="number" name="number_of_sections_max" value="<?php echo $number_of_sections_max; ?>">
            </fieldset>

            <fieldset class="feature-set">
                <legend>Maximum Payload Capacity(kg)</legend>
                Min: <input type="number" name="maximum_payload_capacity_kg_min" value="<?php echo $maximum_payload_capacity_kg_min; ?>">
                Max: <input type="number" name="maximum_payload_capacity_kg_max" value="<?php echo $maximum_payload_capacity_kg_max; ?>">
            </fieldset>

            <div>
                <input type="submit" value="Filter">
            </div>
            <div>
                <a href="<?php echo $_SERVER['PHP_SELF']; ?>" class="unfilter-btn">Unfilter</a>
            </div>

        </div>

        <table border='1' width='600'>
            <thead>
                <tr align='center' bgcolor='#042433'>
                    <td>Model</td>
                    <td>Version</td>
                    <td>Name</td>
                    <td>Type</td>
                    <td>
                        Price Range: 
                        <br>
                        <input type="number" name="min_price" placeholder="Min" value="<?php echo $min_price_filter; ?>">
                        -
                        <input type="number" name="max_price" placeholder="Max" value="<?php echo $max_price_filter; ?>">
                    </td>
                    <td>Features</td>
                </tr>
            </thead>
            <tbody>
                <!-- Product Data -->
                <?php
                if (!empty($data)) {
                    foreach ($data as $product) {
                        $id = $product['p_id'] ?? '';
                        $model = $product['p_model'] ?? '';
                        $version = $product['p_version'] ?? '';
                        $name = $product['p_name'] ?? '';
                        $type = $product['p_type'] ?? '';
                        $price = $product['price'] ?? ''; 

                        echo "<tr>
                            <td>{$model}</td>
                            <td>{$version}</td>
                            <td>{$name}</td>
                            <td>{$type}</td>
                            <td>{$price}</td>
                            <td><a href='view_features.php?id={$id}'>View Features</a></td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No published products found.</td></tr>";
                }
                
                ?>
            </tbody>
        </table>
    </form>
</body>

</html>
