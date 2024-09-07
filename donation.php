<?php
session_start();
require 'config.php';

// Check if alumni is logged in
if (!isset($_SESSION['alumni_id'])) {
    header('Location: login.php');
    exit;
}

$alumni_id = $_SESSION['alumni_id'];

// Handle form submission for new donations
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_donation'])) {
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    // Insert donation into database
    $sql = "INSERT INTO donations (alumni_id, amount, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ids', $alumni_id, $amount, $description);
    $stmt->execute();
    header('Location: donation.php'); // Redirect to avoid form resubmission
    exit;
}

// Handle edit donation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_donation'])) {
    $donation_id = $_POST['donation_id'];
    $amount = $_POST['amount'];
    $description = $_POST['description'];

    $sql = "UPDATE donations SET amount = ?, description = ? WHERE id = ? AND alumni_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('dsii', $amount, $description, $donation_id, $alumni_id);
    $stmt->execute();
    header('Location: donation.php'); // Redirect to avoid form resubmission
    exit;
}

// Handle delete donation
if (isset($_GET['delete'])) {
    $donation_id = $_GET['delete'];

    $sql = "DELETE FROM donations WHERE id = ? AND alumni_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $donation_id, $alumni_id);
    $stmt->execute();
    header('Location: donation.php'); // Redirect after deletion
    exit;
}

// Fetch all donations made by the alumni
$sql = "SELECT * FROM donations WHERE alumni_id = ?";
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
    <title>Donation Tracking - AIU Alumni Hub</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }

        .content {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-left: 220px;
            padding: 20px;
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
    margin-top: 40px; /* Adjust this to move the content down */
        }
        /* Optional: Ensure form and table do not stretch too wide */
form {
    width: 80%; /* Adjust this percentage as needed */
    max-width: 500px; /* Set a maximum width */
    margin-top: 80px; /* Adjust this to move the content down */
}
table {
    width: 80%; /* Adjust this percentage as needed */
    max-width: 800px; /* Set a maximum width */
    margin-top: 80px; /* Adjust this to move the content down */
}

        h1 {
            font-size: 2.5rem;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 40px;
        }

        input[type="number"], input[type="text"] {
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

        /* Sidebar styles */
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
    </style>
</head>
<body>
<div class="sidebar" id="sidebar">
    <h2>AIU Alumni Hub</h2>
    <ul>
        <li><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></li>
        <li><a href="profile.php" target="_blank" style="text-decoration: none; color: inherit;">Profile</a></li>
        <li><a href="event_management.php" style="text-decoration: none; color: inherit;">Event Management</a></li>
        <li><a href="donation.php" style="text-decoration: none; color: inherit;">Donation Tracking</a></li>
    </ul>
</div>

<div class="container">
    <h1>Donation Tracking</h1>

    <!-- Donation Form -->
    <form action="donation.php" method="POST">
        <h2>Record a New Donation</h2>
        <input type="number" name="amount" placeholder="Donation Amount" required>
        <input type="text" name="description" placeholder="Description (e.g., fundraiser or pledge)" required>
        <button type="submit" name="add_donation">Record Donation</button>
    </form>

    <!-- Edit Donation Form (only visible when editing) -->
    <?php if (isset($_GET['edit'])): 
        $donation_id = $_GET['edit'];
        $sql = "SELECT * FROM donations WHERE id = ? AND alumni_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ii', $donation_id, $alumni_id);
        $stmt->execute();
        $donation_to_edit = $stmt->get_result()->fetch_assoc();
    ?>
    <form action="donation.php" method="POST">
        <h2>Edit Donation</h2>
        <input type="hidden" name="donation_id" value="<?php echo $donation_id; ?>">
        <input type="number" name="amount" value="<?php echo $donation_to_edit['amount']; ?>" required>
        <input type="text" name="description" value="<?php echo $donation_to_edit['description']; ?>" required>
        <button type="submit" name="edit_donation">Update Donation</button>
    </form>
    <?php endif; ?>

    <!-- Display Past Donations -->
    <h2>Your Donations</h2>
    <table>
        <tr>
            <th>Amount</th>
            <th>Description</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while ($donation = $result->fetch_assoc()) {
                echo "<tr>
                        <td>" . $donation['amount'] . "</td>
                        <td>" . $donation['description'] . "</td>
                        <td>" . $donation['date'] . "</td>
                        <td>
                            <a href='donation.php?edit=" . $donation['id'] . "' class='edit-btn''>Edit</a>
                            <a href='donation.php?delete=" . $donation['id'] . "' class='delete-btn'' onclick=\"return confirm('Are you sure you want to delete this donation?');\">Delete</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='4'>No donations recorded yet.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>
