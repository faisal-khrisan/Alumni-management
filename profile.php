<?php
session_start();
require 'config.php';

// Ensure the user is logged in
if (!isset($_SESSION['alumni_id'])) {
    header('Location: login.php');
    exit;
}

$alumni_id = $_SESSION['alumni_id'];

// Fetch alumni data
$sql = "SELECT * FROM alumni WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$result = $stmt->get_result();
$alumni = $result->fetch_assoc();

// Update profile logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_profile'])) {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $gender = $_POST['gender'];
    $batch = $_POST['batch'];
    $course_graduated = $_POST['course_graduated'];
    $connected_to = $_POST['connected_to'];
    $email = $_POST['email'];
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : $alumni['password']; // Only update if password is provided

    $sql = "UPDATE alumni SET first_name = ?, middle_name = ?, last_name = ?, gender = ?, batch = ?, course_graduated = ?, connected_to = ?, email = ?, password = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssssssssi', $first_name, $middle_name, $last_name, $gender, $batch, $course_graduated, $connected_to, $email, $password, $alumni_id);
    $stmt->execute();
    header('Location: profile.php'); // Redirect after update
    exit;
}

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $sql = "DELETE FROM alumni WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $alumni_id);
    $stmt->execute();
    
    // Destroy session and redirect to login page
    session_destroy();
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profile</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f7f7f7;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .profile-container {
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.1);
            width: 700px;
            padding: 30px;
        }

        .profile-container h1 {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 20px;
            text-align: center;
        }

        .profile-form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .profile-form .form-group {
            flex-basis: calc(50% - 20px); /* Two-column layout */
            display: flex;
            flex-direction: column;
        }

        .profile-form label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .profile-form input, .profile-form select {
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            transition: border-color 0.3s ease-in-out;
        }

        .profile-form input:focus, .profile-form select:focus {
            border-color: #007bff;
        }

        .form-actions {
            display: flex;
            justify-content: center;
            gap: 20px;
            width: 100%;
        }

        .form-actions button {
            flex: 1;
            padding: 12px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .edit-btn {
            background-color: #007bff;
            color: white;
        }

        .edit-btn:hover {
            background-color: #0056b3;
        }

        .delete-btn {
            background-color: #dc3545;
            color: white;
        }

        .delete-btn:hover {
            background-color: #c82333;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .profile-form .form-group {
                flex-basis: 100%; /* One column on smaller screens */
            }
        }

        @media (max-width: 600px) {
            .profile-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>

<div class="profile-container">
    <h1>Update Profile</h1>
    <form class="profile-form" action="profile.php" method="POST">
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($alumni['first_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="middle_name">Middle Name</label>
            <input type="text" id="middle_name" name="middle_name" value="<?php echo htmlspecialchars($alumni['middle_name']); ?>">
        </div>

        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($alumni['last_name']); ?>" required>
        </div>

        <div class="form-group">
            <label for="gender">Gender</label>
            <select id="gender" name="gender" required>
                <option value="Male" <?php echo $alumni['gender'] == 'Male' ? 'selected' : ''; ?>>Male</option>
                <option value="Female" <?php echo $alumni['gender'] == 'Female' ? 'selected' : ''; ?>>Female</option>
            </select>
        </div>

        <div class="form-group">
            <label for="batch">Batch</label>
            <input type="text" id="batch" name="batch" value="<?php echo htmlspecialchars($alumni['batch']); ?>" required>
        </div>

        <div class="form-group">
            <label for="course_graduated">Course Graduated</label>
            <input type="text" id="course_graduated" name="course_graduated" value="<?php echo htmlspecialchars($alumni['course_graduated']); ?>" required>
        </div>

        <div class="form-group">
            <label for="connected_to">Connected To</label>
            <input type="text" id="connected_to" name="connected_to" value="<?php echo htmlspecialchars($alumni['connected_to']); ?>" required>
        </div>

        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($alumni['email']); ?>" required>
        </div>

        <div class="form-group">
            <label for="password">New Password</label>
            <input type="password" id="password" name="password" placeholder="Enter new password (leave empty to keep current)">
        </div>

        <div class="form-actions">
            <button type="submit" name="update_profile" class="edit-btn">Update Profile</button>
            <button type="submit" name="delete_account" class="delete-btn" onclick="return confirm('Are you sure you want to delete your account?');">Delete Account</button>
        </div>
    </form>
</div>

</body>
</html>
