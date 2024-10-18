<?php
$host = 'localhost';
$db = 'supermarket'; // Use your existing database
$user = 'root'; // Database username
$pass = ''; // Database password

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Read the incoming JSON data
$data = json_decode(file_get_contents('php://input'), true);

// Check if required data is present
if (isset($data['name']) && isset($data['address']) && isset($data['phone']) && isset($data['email']) && isset($data['city']) && isset($data['cartItems'])) {
    $name = $conn->real_escape_string($data['name']);
    $address = $conn->real_escape_string($data['address']);
    $phone = $conn->real_escape_string($data['phone']);
    $email = $conn->real_escape_string($data['email']);
    $city = $conn->real_escape_string($data['city']);
    $success = true;

    // Insert order information
    $orderSql = "INSERT INTO orders (name, address, phone,email,city) VALUES ('$name', '$address', '$phone', '$email','$city')";
    if ($conn->query($orderSql)) {
        $orderId = $conn->insert_id; // Get the last inserted order ID

        // Insert each cart item related to this order
        foreach ($data['cartItems'] as $item) {
            $itemName = $conn->real_escape_string($item['item']);
            $quantity = (int) $item['quantity'];
            $totalPrice = (float) $item['totalPrice'];

            $itemSql = "INSERT INTO order_items (order_id, item_name, quantity, total_price) 
                        VALUES ($orderId, '$itemName', $quantity, $totalPrice)";

            if (!$conn->query($itemSql)) {
                $success = false;
                break;
            }
        }
    } else {
        $success = false;
    }

    // Return response
    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid data received.']);
}

$conn->close();
?>
