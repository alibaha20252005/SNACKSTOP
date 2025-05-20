<?php
header('Content-Type: application/json; charset=utf-8');

// اتصال بقاعدة البيانات
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "restaurant";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["message" => "❌ فشل الاتصال بقاعدة البيانات: " . $conn->connect_error]));
}

$data = json_decode(file_get_contents("php://input"), true);

if (
    !isset($data['full_name'], $data['address'], $data['phone'], $data['payment_method'], $data['cart']) ||
    !is_array($data['cart']) || count($data['cart']) === 0
) {
    die(json_encode(["message" => "❌ بيانات ناقصة أو السلة فارغة."]));
}

$conn->begin_transaction();

try {
    $stmt = $conn->prepare("INSERT INTO customers (full_name, address, phone, payment_method) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $data['full_name'], $data['address'], $data['phone'], $data['payment_method']);
    $stmt->execute();
    $customer_id = $stmt->insert_id;
    $stmt->close();

    $stmt_order = $conn->prepare("INSERT INTO orders (customer_id, item_name, price, quantity) VALUES (?, ?, ?, ?)");
    foreach ($data['cart'] as $item) {
        $stmt_order->bind_param("isdi", $customer_id, $item['name'], $item['price'], $item['quantity']);
        $stmt_order->execute();
    }
    $stmt_order->close();

    $conn->commit();
    echo json_encode(["message" => "✅ تم تسجيل الطلب بنجاح"]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(["message" => "❌ خطأ: " . $e->getMessage()]);
}

$conn->close();
?>
