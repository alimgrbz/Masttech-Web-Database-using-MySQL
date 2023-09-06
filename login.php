<?php
require 'db.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: index.html");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['c_email'];
    $password = $_POST['pwd'];

    $entities = [
        'customer' => ['c_name', 'c_email', 'pwd', 'c_access_level'],
        'staff' => ['staff_name', 'email', 'password', 'authority_level']
    ];

    $user = null;

    foreach ($entities as $table => $columns) {
        $sql = "SELECT {$columns[0]}, {$columns[1]}, {$columns[2]}, {$columns[3]} FROM {$table} WHERE {$columns[1]}=?";
        
        if (!$stmt = $conn->prepare($sql)) {
            die("SQL prepare statement failed: " . $conn->error);
        }
        
        $stmt->bind_param("s", $email);
        
        if (!$stmt->execute()) {
            die("SQL execute statement failed: " . $stmt->error);
        }

        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user) {
            $user['c_name'] = $user[$columns[0]];
            $user['c_email'] = $user[$columns[1]];
            $user['pwd'] = $user[$columns[2]];
            $user['c_access_level'] = ($table == 'staff') ? $user[$columns[3]] : $user[$columns[3]];
            break;
        }
    }

    if ($user) {
        if ($password == $user['pwd']) {
            $_SESSION['logged_in'] = true;
            $_SESSION['name'] = $user['c_name'];
            $_SESSION['email'] = $user['c_email'];
            $_SESSION['userType'] = $user['c_access_level'];

            switch ($_SESSION['userType']) {
                case 'c':
                    header('Location: customerindex.php');
                    break;
                
                case 'adder':
                    header('Location: adder_dashboard.php');
                    break;
                case 'checker':
                    header('Location: checker_dashboard.php');
                    break;
                case 'approver':
                    header('Location: approver_dashboard.php');
                    break;
                case 'admin':
                    // Redirect to any default dashboard for admin or leave this blank if you want to implement multiple choices for admin
                    header('Location: staffindex.php');
                    break;
                default:
                    echo "Invalid user type.";
                    break;
            }
            exit;
        } else {
            echo "Login failed. Password does not match.";
        }
    } else {
        echo "Login failed. User not found.";
    }
}
?>
