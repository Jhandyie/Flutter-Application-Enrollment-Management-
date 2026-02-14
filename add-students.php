<?php 
include 'initialize.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$data = json_decode(file_get_contents("php://input"));

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'POST') {
        // map input

        $name = $data->name;
        $email = $data->email;
        $phone = $data->phone;
        $grade = $data->grade;

        $sql = "INSERT INTO students (name, email, phone, grade) VALUES ('$name', '$email', '$phone', '$grade')";

        if ($connection->query($sql) === TRUE) {
            $results = $connection->query("SELECT id, name, email, phone, grade FROM students");
        } else {
            throw new Exception($connection->error);
        }
        if ($results) {
            $users = [];
            while ($row = $results->fetch_assoc()) {
                $users[] = $row;
            }
            
            http_response_code(201);
            echo json_encode([
                "success" => true,
                "data" => $users,
                "count" => count($users)
            ], JSON_PRETTY_PRINT);
        } else {
            throw new Exception($connection->error);
        }
    } else {
        http_response_code(405);
        echo json_encode([
            "success" => false,
            "error" => "Method not allowed",
            "message" => "Only POST requests are supported"
        ]);
    }
    
} catch (Exception $e) {
    // Handle database errors
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => "Database query failed",
        "message" => $e->getMessage()
    ], JSON_PRETTY_PRINT);
}

// Close the database connection
$connection->close();

?>