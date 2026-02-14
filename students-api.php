<?php 
include 'initialize.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

try {
    // Get request method
    $method = $_SERVER['REQUEST_METHOD'];
    
    if ($method === 'GET') {
        // Fetch all users
        $results = $connection->query("SELECT id, name, email, phone, grade FROM students");
        
        if ($results) {
            $users = [];
            while ($row = $results->fetch_assoc()) {
                $users[] = $row;
            }
            
            http_response_code(200);
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
            "message" => "Only GET requests are supported"
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
