<?php
session_start();
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $name = $_POST['name'];

    // Fetch admin details from the database
    $sql = "SELECT * FROM admins WHERE email = ? AND name = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $email, $name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        // Set session variables for the admin
        $_SESSION['admin_email'] = $admin['email'];
        $_SESSION['admin_name'] = $admin['name'];
        $_SESSION['admin_id'] = $admin['id']; // Store the admin ID in the session
        header('Location: admin_verify.php'); // Redirect to the admin page
        exit;
    } else {
        $error = "Invalid email or name.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            max-width: 400px;
            margin: 100px auto;
            background-color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        input {
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: #dc3545;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Admin Login</h1>
    <?php if (isset($error)) { echo "<p class='error'>$error</p>"; } ?>
    <form action="admin_login.php" method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="text" name="name" placeholder="Admin Name" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>
