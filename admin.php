<?php
$servername = "localhost";
$username = "root"; // default for XAMPP
$password = "";     // default for XAMPP
$dbname = "restaurant";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch customers
$customer_sql = "SELECT * FROM customers";
$customer_result = $conn->query($customer_sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Restaurant Admin Dashboard</title>
    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background-color: #f7f7f7;
        }
        h2 {
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 40px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #eee;
        }
    </style>
</head>
<body>
    <h2>Customer Orders</h2>

    <?php
    if ($customer_result->num_rows > 0) {
        while($customer = $customer_result->fetch_assoc()) {
            echo "<h3>" . htmlspecialchars($customer["full_name"]) . " (ID: " . $customer["id"] . ")</h3>";
            echo "<p>Address: " . htmlspecialchars($customer["address"]) . "<br>";
            echo "Phone: " . htmlspecialchars($customer["phone"]) . "<br>";
            echo "Payment Method: " . htmlspecialchars($customer["payment_method"]) . "<br>";
            echo "Order Time: " . $customer["order_time"] . "</p>";

            // Fetch orders for this customer
            $order_sql = "SELECT * FROM orders WHERE customer_id = " . $customer["id"];
            $order_result = $conn->query($order_sql);

            if ($order_result->num_rows > 0) {
                echo "<table>
                        <tr>
                            <th>Item Name</th>
                            <th>Price</th>
                            <th>Quantity</th>
                        </tr>";
                while($order = $order_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . htmlspecialchars($order["item_name"]) . "</td>
                            <td>$" . number_format($order["price"], 2) . "</td>
                            <td>" . $order["quantity"] . "</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p><em>No orders found.</em></p>";
            }
        }
    } else {
        echo "<p>No customers found.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
