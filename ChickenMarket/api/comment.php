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

// Connect to database
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed."]);
    exit;
}

// Handle different actions
$action = $_GET['action'] ?? '';
$commentId = $_GET['id'] ?? 0;

if ($action === 'edit') {
    handleEditComment($conn, $commentId);
} elseif ($action === 'delete') {
    handleDeleteComment($conn, $commentId);
} else {
    fetchAllComments($conn);
}

function fetchAllComments($conn) {
    $sql = "SELECT c.CommentID, u.Name, c.Rating, c.Comment, c.ProductID, c.UserID
            FROM comments c
            LEFT JOIN users u ON c.UserID = u.UserID";

    $result = $conn->query($sql);

    $comments = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $comments[] = $row;
        }
    }

    echo json_encode($comments, JSON_PRETTY_PRINT);
}

function handleEditComment($conn, $commentId) {
    // In a real app, you should verify the user owns this comment
    $data = json_decode(file_get_contents('php://input'), true);
    $newComment = $conn->real_escape_string($data['comment']);

    $sql = "UPDATE comments SET Comment = '$newComment' WHERE CommentID = $commentId";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to update comment']);
    }
}

function handleDeleteComment($conn, $commentId) {
    // In a real app, you should verify the user owns this comment
    $sql = "DELETE FROM comments WHERE CommentID = $commentId";
    
    if ($conn->query($sql)) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to delete comment']);
    }
}

$conn->close();
?>