<?php
include 'connect.php';

$product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;
$product_info = [];
$bid_success = false;
$error_message = '';

if ($product_id > 0) {
    // Fetch product information
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $product_info = $result->fetch_assoc();
    } else {
        $error_message = "Product not found.";
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_bid'])) {
    // Assuming you have a user session with user ID
    $user_id = $_SESSION['user_id']; // Make sure you have session_start() at the beginning if using sessions
    $bid_amount = $_POST['bid_amount'];

    if ($bid_amount > 0) {
        // Insert bid into database
        $stmt = $conn->prepare("INSERT INTO bids (product_id, user_id, bid_amount) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $product_id, $user_id, $bid_amount);
        if ($stmt->execute()) {
            $bid_success = true;
        } else {
            $error_message = "Failed to place bid.";
        }
    } else {
        $error_message = "Invalid bid amount.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Place Bid</title>
</head>
<body>
    <h1>Place Bid on Product</h1>
    <?php if (!empty($product_info)): ?>
        <h2><?php echo htmlspecialchars($product_info['product_name']); ?></h2>
        <p><?php echo htmlspecialchars($product_info['product_details']); ?></p>
        <?php if ($bid_success): ?>
            <p>Your bid was successfully placed!</p>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form action="" method="post">
            <input type="number" name="bid_amount" placeholder="Enter your bid amount" required>
            <input type="submit" name="place_bid" value="Place Bid">
        </form>
    <?php else: ?>
        <p>Product not found.</p>
    <?php endif; ?>
</body>
</html>
