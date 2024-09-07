<?php
$error_message = ""; // Initialize error message

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';
    
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM alumni WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $alumnus = $result->fetch_assoc();
        if (password_verify($password, $alumnus['password'])) {
            session_start();
            $_SESSION['alumni_id'] = $alumnus['id'];
            header('Location: dashboard.php');
        } else {
            $error_message = "Invalid credentials."; // Store error message
        }
    } else {
        $error_message = "Invalid credentials."; // Store error message
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
<style>
    /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body, html {
    height: 100%;
    font-family: 'Arial', sans-serif;
    background: url('https://studenthub.aiu.edu.my/_next/image?url=%2Fassets%2Fimages%2Fbackground.jpeg&w=1920&q=75') no-repeat center center fixed;
    background-size: cover;
}

.login-container {
    height: 100vh;
    display: flex;
    justify-content: center;
    align-items: center;
    background-color: rgba(0, 0, 0, 0.6); /* Set the opacity of the background */
    box-shadow: -5px 0px 15px rgba(0,0,0,0.5);
}

.login-box {
    width: 850px;
    height: 420px;
    background-color: rgba(255, 255, 255, 0.95); /* Transparent white background */
    border-radius: 10px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    display: flex;
}

.login-left, .login-right {
    width: 50%;
    padding: 40px;
}

.login-left {
    background-color: #0056b3; /* Dark blue background */
    border-radius: 10px 0 0 10px;
    color: white;
}

.login-left h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

.login-left label {
    display: block;
    font-size: 14px;
    margin-bottom: 5px;
    color: #e0e0e0;
}

.login-left input {
    width: 100%;
    padding: 12px;
    margin-bottom: 15px;
    border-radius: 5px;
    border: none;
    background-color: #e7f1ff;
    color: #333;
}

.login-left button {
    width: 100%;
    padding: 12px;
    background-color: #4CAF50; /* Bright green */
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
}

.login-left button:hover {
    background-color: #45a049;
}

.forgot-password {
    color: #00b3ff;
    display: block;
    margin-bottom: 10px;
    text-align: right;
}

.signup-text {
    text-align: center;
    margin-top: 15px;
    color: #e0e0e0;
}

.signup-text a {
    color: #00b3ff;
    text-decoration: none;
}

.signup-text a:hover {
    text-decoration: underline;
}

.login-right {
    background-color: rgba(0, 0, 0, 0.8);
    border-radius: 0 10px 10px 0;
    color: white;
    text-align: left;
}

.login-right h2 {
    font-size: 28px;
    margin-bottom: 20px;
}

.login-right ul {
    list-style-type: disc; /* Adds bullet points (discs) before each list item */
    padding-left: 0;
    margin-bottom: 20px;
}

.login-right ul li {
    font-size: 16px;
    margin-bottom: 10px;
}

/* Styling for the error message */
.error-message {
    color: red; /* Red color for error */
    text-align: center; /* Center align */
    font-weight: bold; /* Make it bold */
    margin-bottom: 15px; /* Add some space below */
}
</style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="login-left">
                <form action="login.php" method="POST">
                    <h2>AIU Alumni</h2>

                    <!-- Error Message Display -->
                    <?php if (!empty($error_message)) : ?>
                        <div class="error-message"><?php echo $error_message; ?></div>
                    <?php endif; ?>

                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>

                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>

                    <a href="#" class="forgot-password">Forgot Your Password?</a>

                    <button type="submit">Login</button>

                    <p class="signup-text">Don't have an account? <a href="register.php">Sign Up</a></p>
                </form>
            </div>
            <div class="login-right">
                <h2>Welcome to AIU Alumni</h2>
                <p>Your comprehensive solution for alumni services at AIU, all in one place:</p>
                <br>
                <ul>
                    <li>Alumni Registration</li>
                    <li>Events & Reunions</li>
                    <li>Exclusive Alumni Resources</li>
                </ul>
                <p><em>Stay connected with your mates and fellow alumni.</em></p>
            </div>
        </div>
    </div>
</body>
</html>
