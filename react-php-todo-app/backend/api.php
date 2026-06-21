<?php
// 1. Enable CORS so the React frontend can communicate with this API
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Handle preflight OPTIONS requests from the browser
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Database configuration parameters
$host = '127.0.0.1'; 
$db_user = 'root';
$db_pass = '';        
$db_name = 'todo_db';
$port = 3307;         // Custom XAMPP port configuration

// Establish connection to MySQL database using MySQLi
$conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

// Check database connection reliability
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// 3. GET Request - Fetch all tasks from the database
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = [];
    
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    echo json_encode($tasks);
}

// 4. POST Request - Add a new task to the database safely
if ($method === 'POST') {
    // Decode incoming JSON payloads from React
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['task_text'])) {
        $task_text = trim($data['task_text']);
        
        // Protect backend using Prepared Statements against SQL Injection
        $stmt = $conn->prepare("INSERT INTO tasks (task_text) VALUES (?)");
        $stmt->bind_param("s", $task_text);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Task created successfully", "id" => $conn->insert_id]);
        } else {
            echo json_encode(["error" => "Failed to create task"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Task text is empty"]);
    }
}

// 5. DELETE Request - Remove a task from the database by ID
if ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
        // Secured deletion using Prepared Statements
        $stmt = $conn->prepare("DELETE FROM tasks WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode(["message" => "Task deleted successfully"]);
        } else {
            echo json_encode(["error" => "Failed to delete task"]);
        }
        $stmt->close();
    } else {
        echo json_encode(["error" => "Missing task ID"]);
    }
}

// Close database instance connection
$conn->close();
?>
