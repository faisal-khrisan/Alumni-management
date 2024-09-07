<?php
session_start();
require 'config.php';

// Check if the alumni is logged in
if (!isset($_SESSION['alumni_id'])) {
    header('Location: login.php');
    exit;
}

$alumni_id = $_SESSION['alumni_id'];

// Fetch job listings from the database
$sql = "SELECT * FROM jobs";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AIU Alumni Hub Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
        }

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

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .content h1 {
            font-size: 1.5rem;
            color: #333;
        }

        .feature-section {
            margin-bottom: 30px;
            margin-left: 50px; 
        }

        .feature-section h2 {
            font-size: 1.5rem;
            color: #007bff;
            margin-left: 50px; 
        }

        .feature-section p {
            font-size: 1.2rem;
            color: #555;
            line-height: 1.6;
            margin-left: 70px; 
        }

        .job-listings {
            margin-top: 30px;
            margin-left: 70px; 
           
        }

        .job-listings h2 {
            color: #007bff;
            margin-left: 30px;
            
        }

        .job-listings ul {
            list-style-type: disc;
            padding-left: 20px;
            margin-left: 50px; 
        }

        .job-listings li {
            font-size: 1rem;
            color: #333;
            margin-bottom: 5px;
        }
    .newcontent{
        margin-top: 80px; /* Adjust this to move the content down */
        
    }

    </style>
</head>
<body>

<div class="sidebar">
    <h2>AIU Alumni Hub</h2>
    <ul>
    
        <li><a href="dashboard.php" style="text-decoration: none; color: inherit;">Dashboard</a></li>
        <li><a href="profile.php" target="_blank" style="text-decoration: none; color: inherit;">Profile</a></li>
        <li><a href="event_management.php" style="text-decoration: none; color: inherit;">Event Management</a></li> <!-- Link to Event Management Page -->
        <li><a href="donation.php" style="text-decoration: none; color: inherit;">Donation Tracking</a></li> <!-- Link to Donation Tracking Page -->
    </ul>
</div>


    <div class="content">
        <h1   style="width: 80%;
            margin: 0 auto;
            padding: 20px;
            font-size: 30px;
            display: flex;
    flex-direction: column; /* Stack content vertically */
    align-items: center; /* Center horizontally */
    margin-left: 180px; /* Keep space for the sidebar */
    padding: 20px;
    transition: margin-left 0.3s ease; /* Smooth transition */
    margin-top: 40px; /* Adjust this to move the content down */" >Welcome to the AIU Alumni Hub Dashboard</h1>
       <div class="newcontent">
        <div class="feature-section">
            <h2>Data Entry Interface</h2>
            <p>
                • Alumni registration and profile updates.<br>
                • Allows alumni to update their personal and professional information.<br>
                • Easy-to-use forms for efficient data entry.
            </p>
        </div>

        <div class="feature-section">
            <h2>Event Management Interface</h2>
            <p>
                • Organize alumni reunions and fundraisers.<br>
                • Manage invitations, RSVPs, and schedules for alumni events.<br>
                • Track event participation and engagement.
            </p>
        </div>

        <div class="feature-section">
            <h2>Donation Tracking Interface</h2>
            <p>
                • Record contributions and pledges made by alumni.<br>
                • Track donations and generate reports.<br>
                • Manage and track fundraising goals.
            </p>
        </div>

        <div class="job-listings">
            <h2>Job Listings</h2>
            <ul>
            
                <?php
                if ($result->num_rows > 0) {
                    // Loop through job listings
                    while ($job = $result->fetch_assoc()) {
                        echo "<li>" . $job['job_title'] . " at " . $job['company'] . "</li>";
                    }
                } else {
                    echo "<li>No jobs posted yet.</li>";
                }
                ?>
            </ul>
        </div>
        </div> 
    </div>

</body>
</html>

