<?php
include 'connect.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirmPassword'];
}


    if ($role === 'buyer') {
        $sql = $conn->prepare("INSERT INTO buyers (username, email, password) VALUES ('$username', '$email', '$hashed_password')");
    } else if ($role === 'seller') {
        $sql = $conn->prepare("INSERT INTO sellers (username, email, password) VALUES ('$username', '$email', '$hashed_password')");
    } else {
        return false; // Invalid user type
    }

        if ($conn->query($sql) === TRUE) {
            $signup_message = "Signup successful!";
            header("Location: home.html");
            exit();
        } else {
            $signup_message = "Signup failed. Please try again.";
        }

// Handling the POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $userType = $_POST['role'] ?? ''; // 'buyer' or 'seller'
    
    // Basic validation
   if (!preg_match("/^[a-zA-Z ]*$/", $username)) {
        $signup_message = "Username should contain only letters and space.";
    } 
    
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $signup_message = "Invalid email format.";
    }
    
    elseif ($password !== $confirmPassword) {
        $signup_message = "Passwords do not match.";
    } else {
         $hashed_password = password_hash($password, PASSWORD_DEFAULT);
     }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up</title>
</head>
<script>
    function validateSignupForm() {
        var username = document.forms["signupForm"]["username"].value;
        var email = document.forms["signupForm"]["email"].value;
        var password = document.forms["signupForm"]["password"].value;
        var confirmPassword = document.forms["signupForm"]["confirmPassword"].value;
        var userType = document.forms["signupForm"]["userType"].value;

        if (username.length < 3) {
            alert("Username must be at least 3 characters long.");
            return false;
        }

        if (!email.includes("@")) {
            alert("Please enter a valid email address.");
            return false;
        }

        if (password.length < 6) {
            alert("Password must be at least 6 characters long.");
            return false;
        }

        if (password !== confirmPassword) {
            alert("Passwords do not match. Please re-enter.");
            return false;
        }

        if (role !== "buyer" && role !== "seller") {
            alert("Please select a user type.");
            return false;
        }

        return true; // form is valid
    }
    </script>
<body>
    <form name="signupForm" action="signup.php" method="post" onsubmit="return validateSignupForm()">
        <label>Username:</label>
        <input type="text" name="username" placeholder="Username" required>
        <label>Email:</label>
        <input type="email" name="email" placeholder="Email" required>
        <label>Password:</label>
        <input type="password" name="password" placeholder="Password" required>
        <label>Confirm Password:</label>
        <input type="password" name="confirmPassword" placeholder="confirmPassword" required><br>
        <select name="role" required>
            <option value="buyer">Buyer</option>
            <option value="seller">Seller</option>
        </select>
        <button type="submit">Sign Up</button>
    </form>
</body>
</html>
