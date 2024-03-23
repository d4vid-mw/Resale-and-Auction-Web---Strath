<?php
include 'connect.php';

$products = [];
$userType = ''; // Assuming this is defined somewhere in your session or user management logic

function searchProducts($searchTerm) {
    global $conn;
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_name LIKE ?");
    $searchTerm = "%$searchTerm%";
    $stmt->bind_param("s", $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();
    
    return $result->fetch_all(MYSQLI_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])) {
    $searchTerm = $_GET['search'];
    $products = searchProducts($searchTerm);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Search Products</title>
</head>
<body>
    <h1>Search for Products</h1>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="get">
        <input type="text" name="search" placeholder="Search products..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <input type="submit" value="Search">
    </form>

    <?php if (!empty($products)): ?>
        <h2>Results:</h2>
        <ul>
            <?php foreach ($products as $product): ?>
                <li>
                    <!-- Assuming your product array has 'image', 'name', 'details', and 'id' keys -->
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['product_name']); ?>" width="100" height="100">
                    <h3><?php echo htmlspecialchars($product['product_name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['product_details']); ?></p>
                    <!-- Example conditional action based on user type -->
                    <?php if ($userType === 'buyer'): ?>
                        <a href="place_bid.php?product_id=<?php echo $product['id']; ?>">Place Bid</a>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && !empty($_GET['search'])): ?>
        <p>No products found. Try different keywords.</p>
    <?php endif; ?>
</body>
</html>