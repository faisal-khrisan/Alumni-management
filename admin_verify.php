<?php
session_start();
require 'config.php';

// Check if the admin is logged in
if (!isset($_SESSION['admin_email']) || !isset($_SESSION['admin_name'])) {
    header('Location: admin_login.php'); // Redirect to the login page if not logged in
    exit;
}

// Admin details
$admin_email = $_SESSION['admin_email'];
$admin_name = $_SESSION['admin_name'];
$admin_id = $_SESSION['admin_id']; // Admin ID to prevent self-deletion

// Process the form to add a new admin
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $sql = "INSERT INTO admins (name, email) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $name, $email);
    $stmt->execute();
    header('Location: admin_verify.php');
    exit;
}

// Delete an admin
if (isset($_GET['delete_admin'])) {
    $id = $_GET['delete_admin'];
    if ($admin_id != $id) { // Prevent deleting the logged-in admin
        $sql = "DELETE FROM admins WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        header('Location: admin_verify.php');
        exit;
    }
}

// Verify an alumni
if (isset($_GET['verify_alumni'])) {
    $alumni_id = $_GET['verify_alumni'];
    $sql = "UPDATE alumni SET status = 'Verified' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $alumni_id);
    if ($stmt->execute()) {
        $success_message = "Alumni verified successfully.";
    } else {
        $error_message = "Error verifying alumni.";
    }
}

// Fetch all admins
$sql_admins = "SELECT * FROM admins";
$admins_result = $conn->query($sql_admins);

// Fetch pending alumni verifications
$sql_alumni = "SELECT * FROM alumni WHERE status = 'Pending'";
$alumni_result = $conn->query($sql_alumni);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Management & Alumni Verification</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 80%;
            max-width: 800px;
            margin: 50px auto;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            border-radius: 10px;
        }
        h1, h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        input, button {
            padding: 10px;
            width: 100%;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .admin-list, .alumni-list {
            list-style-type: none;
            padding: 0;
        }
        .admin-item, .alumni-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .delete-btn, .verify-btn {
            background-color: #dc3545;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            text-decoration: none;
        }
        .delete-btn:hover {
            background-color: #c82333;
        }
        .verify-btn {
            background-color: #28a745;
        }
        .verify-btn:hover {
            background-color: #218838;
        }
        .success-message, .error-message {
            text-align: center;
            margin-bottom: 20px;
            font-size: 1.2rem;
        }
        .success-message {
            color: #28a745;
        }
        .error-message {
            color: #dc3545;
        }
    </style>
</head>
<body>

<div class="container">
    <!-- Admin Header -->
    <div class="header">
        <p><strong>Admin:</strong> <?php echo htmlspecialchars($admin_name); ?> | <strong>Email:</strong> <?php echo htmlspecialchars($admin_email); ?></p>
        <div>
            <a class="logout-btn" href="logout.php">Logout</a>
        </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($success_message)) echo "<p class='success-message'>$success_message</p>"; ?>
    <?php if (isset($error_message)) echo "<p class='error-message'>$error_message</p>"; ?>

    <!-- Add New Admin Form -->
    <h2>Add New Admin</h2>
    <form action="admin_verify.php" method="POST">
        <input type="text" name="name" placeholder="Admin Name" required>
        <input type="email" name="email" placeholder="Admin Email" required>
        <button type="submit" name="add_admin">Add Admin</button>
    </form>

    <!-- List of Admins -->
    <h2>Manage Admins</h2>
    <ul class="admin-list">
        <?php while ($admin = $admins_result->fetch_assoc()) { ?>
            <li class="admin-item">
                <span><?php echo htmlspecialchars($admin['name']) . " (" . htmlspecialchars($admin['email']) . ")"; ?></span>
                <?php if ($admin['id'] != $admin_id) { ?>
                    <a class="delete-btn" href="admin_verify.php?delete_admin=<?php echo $admin['id']; ?>">Delete</a>
                <?php } ?>
            </li>
        <?php } ?>
    </ul>

    <!-- List of Pending Alumni Verifications -->
    <h2>Pending Alumni Verifications</h2>
    <ul class="alumni-list">
        <?php if ($alumni_result->num_rows > 0) {
            while ($alumnus = $alumni_result->fetch_assoc()) { ?>
                <li class="alumni-item">
                    <span><?php echo htmlspecialchars($alumnus['first_name']) . " " . htmlspecialchars($alumnus['last_name']) . " - " . htmlspecialchars($alumnus['email']); ?></span>
                    <a class="verify-btn" href="admin_verify.php?verify_alumni=<?php echo $alumnus['id']; ?>">Verify</a>
                </li>
            <?php }
        } else { ?>
            <p>No pending alumni verifications.</p>
        <?php } ?>
    </ul>
</div>

</body>
</html>
