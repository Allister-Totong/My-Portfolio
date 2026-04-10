<?php
header("Content-Type: application/json");

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
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['ProductID'])) {
    http_response_code(400);
    echo json_encode(["error" => "Invalid data."]);
    exit;
}

$productID = (int) $data['ProductID'];

// First delete related records (like comments) to maintain referential integrity
$conn->begin_transaction();

try {
    $conn->query("DELETE FROM comments WHERE ProductID = $productID");
    $conn->query("DELETE FROM cart WHERE ProductID = $productID");
    $conn->query("DELETE FROM orderitem WHERE ProductID = $productID");
    
    // Delete the product
    $sql = "DELETE FROM products WHERE ProductID = $productID";
    
    if ($conn->query($sql)) {
        $conn->commit();
        echo json_encode(["success" => true]);
    } else {
        throw new Exception("Failed to delete product.");
    }
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}

$conn->close();
?>