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

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

$productID = $_POST['productID'];
$userID = $_POST['userid'];
$rating = $_POST['rating'];
$comment = $_POST['comment'];

// Prepare and bind
$stmt = $conn->prepare("INSERT INTO comments (UserID, Rating, Comment, ProductID) VALUES (?, ?, ?, ?)");
$stmt->bind_param("idss", $userID, $rating, $comment, $productID);

// Execute and check if successful
if ($stmt->execute()) {
    header("Location: ../product/?id=" . $productID);
    exit();
} else {
    echo "Error: " . $stmt->error;
}

// Close the connection
$stmt->close();
$conn->close();

/*if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $file = "../data/comment.csv";

    $username = isset($_POST['username']) && trim($_POST['username']) !== "" ? htmlspecialchars($_POST['username']) : "Anonymous";
    $rating = isset($_POST['rating']) && trim($_POST['rating']) !== "" ? htmlspecialchars($_POST['rating']) : null;
    $comment = isset($_POST['comment']) && trim($_POST['comment']) !== "" ? htmlspecialchars($_POST['comment']) : null;
    $productID = isset($_POST['productID']) && trim($_POST['productID']) !== "" ? htmlspecialchars($_POST['productID']) : null;

    if (!$rating || !$comment || !$productID) {
        echo "Missing required fields!";
        exit;
    }

    // Ensure file exists and has a header
    if (!file_exists($file) || filesize($file) === 0) {
        file_put_contents($file, "CommentID,User,Rating,Comment,ProductID\n");
    }

    // Read existing CSV file
    $csvData = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $lastCommentID = 0;

    // Get the last comment ID (skip header row)
    if (count($csvData) > 1) {
        $lastLine = str_getcsv($csvData[count($csvData) - 1]);
        $lastCommentID = intval($lastLine[0]);
    }

    $newCommentID = $lastCommentID + 1;
    $newEntry = "{$newCommentID},{$username},{$rating},{$comment},{$productID}";

    // Append new entry to the file
    $handle = fopen($file, "a");
    if ($handle) {
        flock($handle, LOCK_EX);
        // Add newline only if file is not empty
        $prefix = (filesize($file) > 0) ? "\n" : "";
        fwrite($handle, $prefix . $newEntry);
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);
        echo "Success";
    }
}*/
?>
