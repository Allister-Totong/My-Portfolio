<?php
header("Content-Type: application/json");

require_once __DIR__ . '/../vendor/autoload.php';
use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$host     = $_ENV['DB_HOST'];
$user     = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname   = $_ENV['DB_NAME'];

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $conn->connect_error]);
    exit;
}

$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

if (is_null($data)) {
    echo json_encode(["error" => "Invalid JSON input"]);
    exit;
}

$userId = isset($data['UserID']) ? $data['UserID'] : null;
$productId = isset($data['ProductID']) ? $data['ProductID'] : null;
$action = isset($data['action']) ? $data['action'] : null;
$quantity = isset($data['quantity']) ? $data['quantity'] : null;

if (empty($userId) || empty($productId) || empty($action)) {
    echo json_encode(["error" => "Missing required parameters"]);
    exit;
}

try {
    if ($action === 'remove') {
        $stmt = $conn->prepare("DELETE FROM cart WHERE UserID = ? AND ProductID = ?");
        $stmt->bind_param("ii", $userId, $productId);
    } elseif ($action === 'update' && !empty($quantity)) {
        $stmt = $conn->prepare("UPDATE cart SET Quantity = ? WHERE UserID = ? AND ProductID = ?");
        $stmt->bind_param("iii", $quantity, $userId, $productId);
    } else {
        echo json_encode(["error" => "Invalid action"]);
        exit;
    }

    $stmt->execute();
    
    if ($stmt->affected_rows > 0) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["error" => "No changes made"]);
    }
    
    $stmt->close();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

$conn->close();
?>