<?php

include("db.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Start by deleting the related features
    $stmtFeatures = $conn->prepare("DELETE FROM features WHERE product_id = ?");
    $stmtFeatures->bind_param("i", $id);
    
    if (!$stmtFeatures->execute()) {
        echo "Error deleting features: " . $stmtFeatures->error;
        $stmtFeatures->close();
        $conn->close();
        exit();
    }
    $stmtFeatures->close();

    // Now delete the product
    $stmtProduct = $conn->prepare("DELETE FROM products WHERE p_id = ?");
    $stmtProduct->bind_param("i", $id);

    if ($stmtProduct->execute()) {
        echo "Product and its features deleted successfully!";
    } else {
        echo "Error deleting product: " . $stmtProduct->error;
    }

    $stmtProduct->close();
    $conn->close();

    header("Location: " . $_SERVER['HTTP_REFERER']);    
    echo "No product ID provided.";
}

?>
   
