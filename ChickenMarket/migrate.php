<?php
require 'vendor/autoload.php'; // Make sure this path is correct

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['DB_HOST'];
$username = $_ENV['DB_USER'];
$password = $_ENV['DB_PASSWORD'];
$dbname = $_ENV['DB_NAME'];

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// USERS
$conn->query("CREATE TABLE IF NOT EXISTS Users (
    UserID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    Name VARCHAR(100),
    Email VARCHAR(100),
    Role VARCHAR(50),
    Password VARCHAR(255)
)");

// PRODUCTS
$conn->query("CREATE TABLE IF NOT EXISTS Products (
    ProductID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    Name VARCHAR(100),
    ShopName VARCHAR(100),
    Description TEXT,
    Price DECIMAL(10,2),
    AverageRating FLOAT,
    Keyword TEXT,
    Pokemon TEXT,
    Location TEXT
)");

// PRODUCT PICTURES
$conn->query("CREATE TABLE IF NOT EXISTS VisualContent (
    ID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    Name VARCHAR(100),
    Description TEXT,
    ShortName VARCHAR(30),
    FileType VARCHAR(5),
    CSSClass VARCHAR(15),
    ProductID INT,
    FOREIGN KEY (ProductID) REFERENCES products(ProductID) ON DELETE CASCADE
)");

// COMMENTS
$conn->query("CREATE TABLE IF NOT EXISTS Comments (
    CommentID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    UserID INT,
    Rating INT,
    Comment TEXT,
    ProductID INT,
    FOREIGN KEY (ProductID) REFERENCES products(ProductID) ON DELETE CASCADE,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
)");

// CART (many-to-many between users and products)
$conn->query("CREATE TABLE IF NOT EXISTS Cart (
    UserID INT,
    ProductID INT,
    Quantity INT,
    PRIMARY KEY (UserID, ProductID),
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES products(ProductID) ON DELETE CASCADE
)");

// ORDERS
$conn->query("CREATE TABLE IF NOT EXISTS Orders (
    OrderID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    UserID INT,
    address TEXT,
    status INT,
    dateOrdered DATE,
    dateComplete DATE,
    FOREIGN KEY (UserID) REFERENCES users(UserID) ON DELETE CASCADE
)");

// ORDER ITEMS
$conn->query("CREATE TABLE IF NOT EXISTS OrderItem (
    OrderID INT,
    ProductID INT,
    Quantity INT CHECK (quantity > 0),
    PRIMARY KEY (OrderID, ProductID),
    FOREIGN KEY (OrderID) REFERENCES orders(OrderID) ON DELETE CASCADE,
    FOREIGN KEY (ProductID) REFERENCES products(ProductID) ON DELETE CASCADE
)");

// ARTICLES
$conn->query("CREATE TABLE IF NOT EXISTS Articles (
    ArticleID INT AUTO_INCREMENT PRIMARY KEY,
    Title VARCHAR(100),
    Content TEXT
)");

// ARTICLE PICTURES
$conn->query("CREATE TABLE IF NOT EXISTS ArticleVisualContent (
    ID INT AUTO_INCREMENT PRIMARY KEY UNIQUE,
    Name VARCHAR(100),
    Description TEXT,
    ShortName VARCHAR(30),
    FileType VARCHAR(5),
    CSSClass VARCHAR(15),
    ArticleID INT,
    FOREIGN KEY (ArticleID) REFERENCES Articles(ArticleID) ON DELETE CASCADE
)");

echo "Tables created successfully!";

//IMPORTING FROM CSV TO DB
function importCSV($conn, $filename, $queryBuilder, $skipHeader = true) {
    $path = __DIR__ . "/data/$filename";
    $file = fopen($path, 'r');
    if (!$file) {
        echo "Cannot open $path<br>";
        return;
    }

    if ($skipHeader) fgetcsv($file);  // Skip header row if necessary

    while (($row = fgetcsv($file)) !== false) {
        $query = $queryBuilder($conn, $row);

        if (empty($query)) continue;

        if (!$conn->query($query)) {
            echo "Error: $query<br>" . $conn->error . "<br>";
        }
    }

    fclose($file);
    echo "Imported: $filename<br>";
}


//import user
importCSV($conn, 'user.csv', function($conn, $row) {
    if (count($row) < 5) return null; // skip

    $id = intval($row[0]);
    $name = $conn->real_escape_string($row[1]);
    $email = $conn->real_escape_string($row[2]);
    $role = intval($row[3]);
    $password = password_hash($row[4], PASSWORD_DEFAULT);

    $sql = "INSERT INTO users (UserID, Name, Email,	Role, Password)
            VALUES ($id, '$name', '$email', $role, '$password')";
        return $sql;
});

//import products
importCSV($conn, 'product.csv', function($conn, $row) {
    $id = intval($row[0]);
    $name = $conn->real_escape_string($row[1]);
    $shopName = $conn->real_escape_string($row[2]);
    $description = $conn->real_escape_string($row[3]);
    $price = floatval($row[4]);
    $rating = floatval($row[5]);
    $keyword = $conn->real_escape_string($row[6]);
    $pokemon = $conn->real_escape_string($row[7]);
    $location = $conn->real_escape_string($row[8]);

    $sql = "INSERT INTO Products (ProductID, Name, ShopName, Description, Price, AverageRating, Keyword, Pokemon, Location)
            VALUES ($id, '$name', '$shopName', '$description', $price, $rating, '$keyword', '$pokemon', '$location')";
    return $sql;
});

//import comments
importCSV($conn, 'comment.csv', function($conn, $row) {
    if (count($row) < 4) return null;

    $commentID = intval($row[0]);
    $userID = intval($row[1]);
    $rating = intval($row[2]);
    $comment = $conn->real_escape_string($row[3]);
    $productID = intval($row[4]);

    //check if user and product exist
    $userExists = $conn->query("SELECT 1 FROM users WHERE UserID = $userID")->num_rows > 0;
    $productExists = $conn->query("SELECT 1 FROM products WHERE ProductID = $productID")->num_rows > 0;

    if (!$userExists || !$productExists) return null;

    return "INSERT INTO Comments (CommentID, UserID, Rating, Comment, ProductID)
            VALUES ($commentID, $userID, $rating, '$comment', $productID)";
    return $sql;
});


//import product visual
importCSV($conn, 'visualcontent.csv', function($conn, $row) {
    $id = intval($row[0]);
    $name = $conn->real_escape_string($row[1]);
    $description = $conn->real_escape_string($row[2]);
    $shortName = $conn->real_escape_string($row[3]);
    $fileType = $conn->real_escape_string($row[4]);
    $cssClass = $conn->real_escape_string($row[5]);
    $productId = intval($row[6]);

    return "INSERT INTO VisualContent (ID, Name, Description, ShortName, FileType, CSSClass, ProductID)
            VALUES ($id, '$name', '$description', '$shortName', '$fileType', '$cssClass', $productId)";
    return $sql;
});

//import articles
importCSV($conn, 'article.csv', function($conn, $row) {
    $id = intval($row[0]);
    $title = $conn->real_escape_string($row[1]);
    $body = $conn->real_escape_string($row[2]);

    return "INSERT INTO articles (ArticleID, Title, Content)
            VALUES ($id, '$title', '$body')";
});

//import article visual
importCSV($conn, 'articlevisualcontent.csv', function($conn, $row) {
    $id = intval($row[0]);
    $name = $conn->real_escape_string($row[1]);
    $description = $conn->real_escape_string($row[2]);
    $shortName = $conn->real_escape_string($row[3]);
    $fileType = $conn->real_escape_string($row[4]);
    $cssClass = $conn->real_escape_string($row[5]);
    $articleId = intval($row[6]);

    return "INSERT INTO ArticleVisualContent (ID, Name, Description, ShortName, FileType, CSSClass, ArticleID)
            VALUES ($id, '$name', '$description', '$shortName', '$fileType', '$cssClass', $articleId)";
    return $sql;
});

$conn->close();
echo "<br>All done.";
