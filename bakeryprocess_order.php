<?php
$host = 'localhost';
$db = 'supermarket';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['name']) && isset($data['address']) && isset($data['phone']) && isset($data['email']) && isset($data['city']) && isset($data['cart'])) {
    $name = $conn->real_escape_string($data['name']);
    $address = $conn->real_escape_string($data['address']);
    $phone = $conn->real_escape_string($data['phone']);
    $email = $conn->real_escape_string($data['email']);
    $city = $conn->real_escape_string($data['city']);
    $success = true;

    $sql = "INSERT INTO orders (name, address, phone, email, city) VALUES ('$name', '$address', '$phone', '$email', '$city')";
    if ($conn->query($sql)) {
        $orderId = $conn->insert_id;

        foreach ($data['cart'] as $item) {
            $itemName = $conn->real_escape_string($item['item']);
            $quantity = (int)$item['quantity'];
            $totalPrice = (float)$item['totalPrice'];

            $sql = "INSERT INTO order_items (order_id, item_name, quantity, total_price) 
                    VALUES ($orderId, '$itemName', $quantity, $totalPrice)";
            if (!$conn->query($sql)) {
                $success = false;
                break;
            }
        }
    } else {
        $success = false;
    }

    echo json_encode(['success' => $success]);
} else {
    echo json_encode(['success' => false]);
}

$conn->close();
?>
