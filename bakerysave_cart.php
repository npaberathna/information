<?php
$host = 'localhost';
$db = 'supermarket';  // Use your existing database
$user = 'root';       // Database username
$pass = '';           // Database password

// Create a connection
$conn = new mysqli($host, $user, $pass, $db);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

if (is_array($data)) {
    $success = true;

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO cart_items (item_name, quantity, total_price) VALUES (?, ?, ?)");

    foreach ($data as $item) {
        $itemName = $conn->real_escape_string($item['item']);
        $quantity = (int) $item['quantity'];
        $totalPrice = (float) $item['totalPrice'];

        // Bind parameters
        $stmt->bind_param("sid", $itemName, $quantity, $totalPrice);

        // Execute the statement
        if (!$stmt->execute()) {
            $success = false;
            break;
        }
    }

    $stmt->close();
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
