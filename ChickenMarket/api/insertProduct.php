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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    // Upload the image
    $uploadDirectory = '../images/';
    if (!file_exists($uploadDirectory)) {
        mkdir($uploadDirectory, 0777, true);
    }

    $fileName = basename($_FILES['ProdPic']['name']);
    $targetFile = $uploadDirectory . $fileName;
    $fileType = pathinfo($targetFile, PATHINFO_EXTENSION);
    $shortName = pathinfo($fileName, PATHINFO_FILENAME);
    $cssClass = "standard";

    if (move_uploaded_file($_FILES['ProdPic']['tmp_name'], $targetFile)) {

        // Insert into product table
        $name = $conn->real_escape_string($_POST['ProdName']);
        $shop = $conn->real_escape_string($_POST['ProdShop']);
        $desc = $conn->real_escape_string($_POST['ProdDesc']);
        $price = floatval($_POST['ProdPrice']);
        $keyword = $conn->real_escape_string($_POST['ProdKeyword']);
        $pokemon = $conn->real_escape_string($_POST['ProdPokemon']);
        $location = $conn->real_escape_string($_POST['ProdLoc']);
        $avgRating = 0;

        $insertProductSql = "INSERT INTO products (Name, ShopName, Description, Price, AverageRating, Keyword, Pokemon, Location)
                             VALUES ('$name', '$shop', '$desc', $price, $avgRating, '$keyword', '$pokemon', '$location')";

        if ($conn->query($insertProductSql)) {
            $productId = $conn->insert_id;

            // Insert into visualcontent table
            $insertVisualSql = "INSERT INTO visualcontent (Name, Description, ShortName, FileType, CSSClass, ProductID)
                                VALUES ('$fileName', '$desc', '$shortName', '$fileType', '$cssClass', $productId)";

            if ($conn->query($insertVisualSql)) {
                header("Location: ../index");
                exit;
            } 
        } 
    } 
} 

$conn->close();
?>
