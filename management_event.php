<?php
session_start();
require 'config.php';

// Check if alumni is logged in
if (!isset($_SESSION['alumni_id'])) {
    header('Location: login.php');
    exit;
}

$alumni_id = $_SESSION['alumni_id'];

// Handle form submission for new events
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $description = $_POST['description'];

    // Insert event into the database
    $sql = "INSERT INTO events (alumni_id, event_name, event_date, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $alumni_id, $event_name, $event_date, $description);
    $stmt->execute();
}

// Fetch all events created by the alumni
$sql = "SELECT * FROM events WHERE alumni_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $alumni_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Management - AIU Alumni Hub</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }

        .container {
            width: 80%;
            margin: 0 auto;
            padding: 20px;
        }

        h1 {
            font-size: 1.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 40px;
        }

        input[type="text"], input[type="date"] {
            padding: 10px;
            width: 100%;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        button {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        table, th, td {
            border: 1px solid #ddd;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #007bff;
            color: white;
        }
    </style>
</head>
<body>

    <div class="container">
        <h1>Event Management</h1>

        <!-- Event Form -->
        <form action="event_management.php" method="POST">
            <h2>Create a New Event</h2>
            <input type="text" name="event_name" placeholder="Event Name" required>
            <input type="date" name="event_date" placeholder="Event Date" required>
            <input type="text" name="description" placeholder="Event Description" required>
            <button type="submit">Create Event</button>
        </form>

        <!-- Display Events -->
        <h2>Your Events</h2>
        <table>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Description</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($event = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $event['event_name'] . "</td>
                            <td>" . $event['event_date'] . "</td>
                            <td>" . $event['description'] . "</td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No events created yet.</td></tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
