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

// Get product ID
$productID = $_GET['id'] ?? null;
if (!$productID) {
    echo json_encode([
        "imagePath" => "images/default.png",
        "alt" => "Default Image",
        "class" => "standard"
    ]);
    exit;
}

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode([
        "imagePath" => "images/default.png",
        "alt" => "Default Image",
        "class" => "standard"
    ]);
    exit;
}

// Query for visual content
$stmt = $conn->prepare("SELECT ShortName, FileType, CSSClass FROM VisualContent WHERE ProductID = ?");
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode([
        "imagePath" => "images/{$row['ShortName']}.{$row['FileType']}",
        "alt" => $row['ShortName'],
        "class" => $row['CSSClass'] ?? "standard"
    ]);
} else {
    echo json_encode([
        "imagePath" => "images/default.png",
        "alt" => "Default Image",
        "class" => "standard"
    ]);
}

$stmt->close();
$conn->close();
?>
