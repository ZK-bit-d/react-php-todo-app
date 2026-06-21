<?php
// 1. Овозможуваме React да може да комуницира со ова API (CORS)
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// Справување со OPTIONS барања од прелистувачот
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// 2. Поврзување со базата
$host = '127.0.0.1'; // Користиме IP адреса наместо зборот localhost
$db_user = 'root';
$db_pass = '';        // Овде останува целосно празно
$db_name = 'todo_db';
$port = 3307;         // Ја дефинираме точната порта од вашиот XAMPP

// Ја додаваме портата како 5-ти параметар на крајот
$conn = new mysqli($host, $db_user, $db_pass, $db_name, $port);

if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// 3. GET Барање - Ги враќа сите задачи од базата
if ($method === 'GET') {
    $result = $conn->query("SELECT * FROM tasks ORDER BY created_at DESC");
    $tasks = [];
    
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
    
    echo json_encode($tasks);
}

// 4. POST Барање - Додава нова задача во базата
if ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (!empty($data['task_text'])) {
        $task_text = trim($data['task_text']);
        
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

// 5. DELETE Барање - Брише задача според ID
if ($method === 'DELETE') {
    if (isset($_GET['id'])) {
        $id = intval($_GET['id']);
        
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

$conn->close();
?>