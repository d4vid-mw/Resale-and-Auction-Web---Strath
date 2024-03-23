<?php
include 'connect.php';
session_start();

// Check if the user is logged in, if not then redirect to login page
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("location: login.php");
    exit;
}

// Assuming the user's ID and type are stored in session variables
$userId = $_SESSION['id'];
$userType = $_SESSION['user_type']; // Let's say 'buyer' or 'seller'

// Redirect buyers away from this page
if ($userType !== 'seller') {
    exit('Access denied: This page is only for sellers.');
}

$error = '';

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Use mysqli real escape string to prevent SQL Injection
    $productName = $conn->real_escape_string($_POST['product_name']);
    $productDetails = $conn->real_escape_string($_POST['product_details']);
    $sellerName = $_SESSION['username']; // Get seller's username from session

    // File upload logic (simplified for brevity)
    $target_dir = "uploads/"; // Directory where images are stored
    $target_file = $target_dir . basename($_FILES["product_image"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Check if image file is an actual image or fake image
    $check = getimagesize($_FILES["product_image"]["tmp_name"]);
    if ($check !== false) {
        if (move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Prepare an insert statement
            $sql = "INSERT INTO products (product_name,product_details,price,image,seller_name) VALUES (?, ?, ?, ?, ?)";

            if ($stmt = $conn->prepare($sql)) {
                // Bind parameters to prevent SQL Injection
                $stmt->bind_param("sssis", $productName, $productDetails, $target_file, $userId, $sellerName);

                if ($stmt->execute()) {
                    echo "Product listed successfully!";
                } else {
                    $error = "Error: " . $conn->error;
                }

                $stmt->close();
            }
        } else {
            $error = "Sorry, there was an error uploading your file.";
        }
    } else {
        $error = "File is not an image.";
    }
}

// Close connection
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>List a Product</title>
</head>
<script>
    function validateProductForm() {
        var productName = document.forms["productForm"]["product_name"].value;
        var productDetails = document.forms["productForm"]["product_details"].value;
        var productImage = document.forms["productForm"]["product_image"].value;

        if (productName.length < 1) {
            alert("Please enter a product name.");
            return false;
        }

        if (productDetails.length < 1) {
            alert("Please enter product details.");
            return false;
        }

        if (productImage.length < 1) {
            alert("Please upload a product image.");
            return false;
        }

        // Additional checks for file type or size can be added here

        return true; // form is valid
    }
    </script>
<body>
    <h1>List a New Product</h1>

    <?php
    if (!empty($error)) {
        echo '<div>' . $error . '</div>';
    }
    ?>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data" onsubmit="return validateProductForm()">
    <label for="product_name">Product Name:</label><br>
    <input type="text" id="product_name" name="product_name" required><br><br>
    
    <label for="product_details">Product Details:</label><br>
    <textarea id="product_details" name="product_details" rows="4" required></textarea><br><br>
    
    <label for="product_image">Product Image:</label><br>
    <input type="file" id="product_image" name="product_image" accept="image/*" required><br><br>
    
    <input type="submit" value="List Product">
</form>
</body>
</html>


