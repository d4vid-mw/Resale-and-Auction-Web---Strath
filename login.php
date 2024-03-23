<?php
include 'connect.php';

function validateUser($username, $password) {
    global $conn;
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            return $user['id'];
        }
    }
    
    return null;
}

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        echo "Please enter both username and password.";
    } else {
        $userId = validateUser($username, $password);
        
        if ($userId) {
            $_SESSION['loggedin'] = true;
            $_SESSION['id'] = $userId;
            $_SESSION['username'] = $username;
            // Redirect to a dashboard or home page after successful login
            header("Location: dashboard.php");
            exit;
        } else {
            echo "Invalid username or password. Please try again.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>

    <!-- Script for form validation -->
    <script>
    function validateLoginForm() {
        var username = document.forms["loginForm"]["username"].value;
        var password = document.forms["loginForm"]["password"].value;

        if (username.trim() === "") {
            alert("Please enter your username.");
            return false;
        }

        if (password.trim() === "") {
            alert("Please enter your password.");
            return false;
        }

        // Additional validation logic can be added here, such as password length

        return true; // form is valid
    }
    </script>
</head>

<body>
    <div>
        <h2>Login</h2>
        <p>Please fill in your credentials to login.</p>
        <form name="loginForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" onsubmit="return validateLoginForm()">
            <div>
                <label>Username</label>
                <input type="text" name="username" required>
            </div>    
            <div>
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            <div>
                <input type="submit" value="Login">
            </div>
            <?php 
            if (!empty($login_err)) {
                echo '<div>' . $login_err . '</div>';
            }        
            ?>
        </form>
    </div>
</body>
</html>
