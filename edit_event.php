<?php
session_start();
require 'config.php';

// Check if alumni is logged in
if (!isset($_SESSION['alumni_id'])) {
    header('Location: login.php');
    exit;
}

$alumni_id = $_SESSION['alumni_id'];

// Get event data based on ID
if (isset($_GET['id'])) {
    $event_id = $_GET['id'];
    $sql = "SELECT * FROM events WHERE id = ? AND alumni_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $event_id, $alumni_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $event = $result->fetch_assoc();
}

// Handle form submission to update the event
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $description = $_POST['description'];

    // Update the event in the database
    $sql = "UPDATE events SET event_name = ?, event_date = ?, description = ? WHERE id = ? AND alumni_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssii', $event_name, $event_date, $description, $event_id, $alumni_id);
    $stmt->execute();

    header('Location: event_management.php'); // Redirect back to the event management page
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Event - AIU Alumni Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            background-color: #f7f9fc;
            display: flex;
            justify-content: center;
            align-items: center;
            background-color: #e6e6e6;
        }

        .container {
            width: 100%;
            max-width: 400px; /* Adjusted for a more appropriate width */
            padding: 20px;
            background-color: white;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        h1 {
            font-size: 1.8rem;
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 25px;
           
        }

        input[type="text"], input[type="date"] {
            padding: 12px;
            width: 100%;
            max-width: 100%; /* Prevents input fields from expanding too much */
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 16px;
            max-width: 370px;
            
        }

        button {
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            max-width: 100%; /* Button size will not exceed its container */
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Ensures the container and form are centered properly */
        body, html {
            height: 100%;
        }

        .form-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Edit Event</h1>

        <!-- Edit Event Form -->
        <form action="edit_event.php?id=<?php echo $event_id; ?>" method="POST">
            <input type="text" name="event_name" value="<?php echo isset($event['event_name']) ? htmlspecialchars($event['event_name']) : ''; ?>" required>
            <input type="date" name="event_date" value="<?php echo isset($event['event_date']) ? htmlspecialchars($event['event_date']) : ''; ?>" required>
            <input type="text" name="description" value="<?php echo isset($event['description']) ? htmlspecialchars($event['description']) : ''; ?>" required>
            <button type="submit">Update Event</button>
        </form>
    </div>

</body>
</html>
