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

// Fetch ordereditems (you might want to filter by userId here)
$sql = "SELECT o.OrderID, o.ProductID, p.Name, o.Quantity FROM orderitem o LEFT JOIN products p ON o.ProductID=p.ProductID";
$result = $conn->query($sql);

$ordereditems = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $ordereditems[] = $row;
    }
}

// Return ordered items as JSON
echo json_encode($ordereditems, JSON_PRETTY_PRINT);

// Close the connection
$conn->close();
?>
