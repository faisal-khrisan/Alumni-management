<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Event</title>
    <style>
        /* General Reset */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body, html {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .form-container {
            background-color: white;
            padding: 40px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            width: 400px;
        }

        h2 {
            margin-bottom: 20px;
            font-size: 2rem;
            color: #007bff; /* Changed to blue */
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
            font-size: 1.2rem;
            color: #333;
        }

        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .submit-btn {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        .success-message {
            text-align: center;
            color: green;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Join Event</h2>

    <?php
    // Database connection parameters
    $servername = "localhost"; // Change if your DB is hosted elsewhere
    $username = "root"; // DB username
    $password = ""; // DB password
    $dbname = "alumni_management"; // Database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Collect form data and sanitize it
        $name = htmlspecialchars($_POST['name']);
        $school = htmlspecialchars($_POST['school']);
        $email = htmlspecialchars($_POST['email']);

        // Prepare and bind
        $stmt = $conn->prepare("INSERT INTO event_participants (name, school, email) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $school, $email);

        // Execute the query
        if ($stmt->execute()) {
            echo "<p class='success-message'>Thank you, $name! You have successfully joined the event.</p>";
        } else {
            echo "<p class='success-message' style='color: red;'>Error: " . $stmt->error . "</p>";
        }

        // Close the statement and connection
        $stmt->close();
    }

    // Close connection
    $conn->close();
    ?>

    <form method="POST" action="join_event.php">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>

        <label for="school">School:</label>
        <input type="text" id="school" name="school" required>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>

        <button type="submit" class="submit-btn">Join Event</button>
    </form>
</div>

</body>
</html>
