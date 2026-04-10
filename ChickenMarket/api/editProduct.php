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
$name = $conn->real_escape_string($data['Name']);
$shopName = $conn->real_escape_string($data['ShopName']);
$price = (float) $data['Price'];
$keyword = $conn->real_escape_string($data['Keyword']);
$description = $conn->real_escape_string($data['Description']);
$location = $conn->real_escape_string($data['Location']);

// Update the product
$sql = "UPDATE products 
        SET Name='$name', ShopName='$shopName', Price=$price, Keyword='$keyword', Description='$description', Location='$location' 
        WHERE ProductID=$productID";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true]);
} else {
    http_response_code(500);
    echo json_encode(["error" => "Failed to update product."]);
}

$conn->close();
?>
