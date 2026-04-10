<?php 
require_once __DIR__ . '/../vendor/autoload.php';

use Dotenv\Dotenv;

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host     = $_ENV['DB_HOST'];
$user     = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname   = $_ENV['DB_NAME'];

// Connect to MySQL
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get POST data safely
$userid = isset($_POST['userid']) ? intval($_POST['userid']) : 0;
$productid = isset($_POST['productid']) ? intval($_POST['productid']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 0;

// Insert into cart table
if ($userid > 0 && $productid > 0 && $quantity > 0) {
    // First check if the item already exists in cart
    $checkStmt = $conn->prepare("SELECT quantity FROM cart WHERE userid = ? AND productid = ?");
    $checkStmt->bind_param("ii", $userid, $productid);
    $checkStmt->execute();
    $checkStmt->store_result();

    if ($checkStmt->num_rows > 0) {
        // Item exists, so update quantity
        $checkStmt->bind_result($existingQuantity);
        $checkStmt->fetch();
        $newQuantity = $existingQuantity + $quantity;

        $updateStmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE userid = ? AND productid = ?");
        $updateStmt->bind_param("iii", $newQuantity, $userid, $productid);

        if ($updateStmt->execute()) {
            header("Location: ../cart");
            exit;
        } else {
            die("Error updating cart: " . $updateStmt->error);
        }

        $updateStmt->close();
    } else {
        // Item does not exist, insert new
        $insertStmt = $conn->prepare("INSERT INTO cart (userid, productid, quantity) VALUES (?, ?, ?)");
        $insertStmt->bind_param("iii", $userid, $productid, $quantity);

        if ($insertStmt->execute()) {
            header("Location: ../cart");
            exit;
        } else {
            die("Error adding to cart: " . $insertStmt->error);
        }

        $insertStmt->close();
    }

    $checkStmt->close();
} else {
    die("Invalid input.");
}

$conn->close();
?>
