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

// Read the incoming JSON
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// Check if JSON decoding worked
if (is_null($data)) {
    echo json_encode(["error" => "Invalid JSON input: " . $rawData]);
    exit;
}

$userId = isset($data['UserID']) ? $data['UserID'] : null;
$address = isset($data['address']) ? $data['address'] : null;
$cartItems = isset($data['cartItems']) ? $data['cartItems'] : null;

if (empty($userId) || empty($address) || empty($cartItems)) {
    echo json_encode(["error" => "Invalid input data. Received: " . json_encode($data)]);
    exit;
}

// Start a transaction to ensure atomicity
$conn->begin_transaction();

try {
    // Insert order into orders table with dateOrdered and dateComplete (NULL initially)
    $orderSql = "INSERT INTO orders (UserID, address, status, dateOrdered, dateComplete) VALUES (?, ?, 0, NOW(), NULL)";
    $stmt = $conn->prepare($orderSql);
    $stmt->bind_param("is", $userId, $address);
    $stmt->execute();
    $orderId = $stmt->insert_id; // Get the OrderID of the new order

    // Insert each cart item into orderitems table
    $orderItemsSql = "INSERT INTO orderitem (OrderID, ProductID, Quantity) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($orderItemsSql);
    foreach ($cartItems as $item) {
        $stmt->bind_param("iii", $orderId, $item['ProductID'], $item['Quantity']);
        $stmt->execute();
    }

    // Remove cart items for the specified UserID
    $deleteCartSql = "DELETE FROM cart WHERE UserID = ?";
    $stmt = $conn->prepare($deleteCartSql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    // Commit the transaction
    $conn->commit();

    // Close the connection
    $stmt->close();
    $conn->close();

    // Return success
    echo json_encode(["success" => true, "orderId" => $orderId]);

} catch (Exception $e) {
    // Rollback the transaction in case of an error
    $conn->rollback();

    // Log error message to help with debugging
    error_log($e->getMessage());
    
    // Return error with message
    http_response_code(500);
    echo json_encode(["error" => "There was an issue processing your order: " . $e->getMessage()]);
}
