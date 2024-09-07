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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $event_name = $_POST['event_name'];
    $event_date = $_POST['event_date'];
    $description = $_POST['description'];

    // Insert event into the database
    $sql = "INSERT INTO events (alumni_id, event_name, event_date, description) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('isss', $alumni_id, $event_name, $event_date, $description);
    $stmt->execute();
}

// Handle event deletion
if (isset($_GET['delete'])) {
    $event_id = $_GET['delete'];
    $sql = "DELETE FROM events WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $event_id);
    $stmt->execute();
    header("Location: event_management.php"); // Redirect to the event management page
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
            display: flex;
    flex-direction: column; /* Stack content vertically */
    align-items: center; /* Center horizontally */
    margin-left: 220px; /* Keep space for the sidebar */
    padding: 20px;
    transition: margin-left 0.3s ease; /* Smooth transition */
    margin-top: 30px; /* Adjust this to move the content down */
        }
/* Optional: Ensure form and table do not stretch too wide */
form {
    width: 80%; /* Adjust this percentage as needed */
    max-width: 500px; /* Set a maximum width */
    margin-top: 70px; /* Adjust this to move the content down */
}
table {
    width: 80%; /* Adjust this percentage as needed */
    max-width: 800px; /* Set a maximum width */
    margin-top: 70px; /* Adjust this to move the content down */
}
        h1 {
            font-size: 40px;
            color: #333;
            margin-bottom: 10px;
        }

        form {
            margin-bottom: 40px;
        }

        /* Styling the inputs with double borders and glow */
input[type="text"], input[type="date"], textarea {
    width: 100%;
    padding: 12px;
    margin: 8px 0;
    border: 2px solid #00bfff; /* Light blue outer border */
    border-radius: 5px;
    outline: none;
    background-color: white;
    position: relative;
    z-index: 1;
}

input[type="text"]:focus, input[type="date"]:focus, textarea:focus {
    box-shadow: 0 0 8px 2px rgba(0, 191, 255, 0.75), 0 0 0 2px #00bfff; /* Glow and inner border */
    border-color: #007bff; /* Inner border color when focused */
    background-color: white;
    outline: none;
}

input[type="text"]::placeholder, textarea::placeholder {
    color: #888;
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

        .edit-btn, .delete-btn {
            padding: 5px 10px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
            margin-right: 5px;
        }

        .edit-btn {
            background-color: #28a745;
        }

        .delete-btn {
            background-color: #dc3545;
        }

        .edit-btn:hover, .delete-btn:hover {
            opacity: 0.8;
        }
        /* sider start */
        /* Sidebar Styles */
.sidebar {
    width: 250px;
    background-color: #fff;
    height: 100vh;
    position: fixed;
    padding-top: 20px;
    box-shadow: 2px 0px 5px rgba(0,0,0,0.1);
}

.sidebar h2 {
    text-align: center;
    font-size: 1.2rem;
    color: #333;
}

.sidebar ul {
    list-style-type: none;
    padding: 0;
}

.sidebar ul li {
    padding: 15px 20px;
    font-size: 1rem;
    color: #333;
    cursor: pointer;
}

.sidebar ul li:hover {
    background-color: #f0f0f0;
}

.sidebar ul li.active {
    background-color: #007bff;
    color: white;
}

/* Main Content Styles */
.main-content {
    margin-left: 250px; /* Adjust based on your sidebar width */
    padding: 20px;
    background-color: #f9f9f9;
    min-height: 100vh;
    /* You can use flexbox for layout adjustment if needed */
    display: flex;
    flex-direction: column;
}


    </style>
</head>
<body>
<div class="sidebar">
    <h2>AIU Alumni Hub</h2>
    <ul>
        <li><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></li>
        <li><a href="profile.php" target="_blank" style="text-decoration: none; color: inherit;">Profile</a></li>
        <li><a href="event_management.php" style="text-decoration: none; color: inherit;">Event Management</a></li>
        <li><a href="donation.php" style="text-decoration: none; color: inherit;">Donation Tracking</a></li>
    </ul>
</div>


    <div class="container">
        <h1>Event Management</h1>

        <!-- Event Form -->
        <form action="event_management.php" method="POST">
            <h2>Create a New Event</h2>
            <input type="text" name="event_name" placeholder="Event Name" required>
            <input type="date" name="event_date" placeholder="Event Date" required>
            <input type="text" name="description" placeholder="Event Description" required>
            <button type="submit" name="create">Create Event</button>
        </form>

        <!-- Display Events -->
        <h2>Your Events</h2>
        <table>
            <tr>
                <th>Event Name</th>
                <th>Event Date</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
            <?php
            if ($result->num_rows > 0) {
                while ($event = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $event['event_name'] . "</td>
                            <td>" . $event['event_date'] . "</td>
                            <td>" . $event['description'] . "</td>
                            <td>
                                <a href='edit_event.php?id=" . $event['id'] . "' class='edit-btn'>Edit</a>
                                <a href='event_management.php?delete=" . $event['id'] . "' class='delete-btn' onclick='return confirm(\"Are you sure you want to delete this event?\");'>Delete</a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='4'>No events created yet.</td></tr>";
            }
            ?>
        </table>
    </div>

</body>
</html>
