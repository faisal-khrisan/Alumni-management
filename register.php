<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';

    // Process form data
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $middle_name = $_POST['middle_name'];
    $gender = $_POST['gender'];
    $batch = $_POST['batch'];
    $course_graduated = $_POST['course_graduated'];
    $connected_to = $_POST['connected_to'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    // Check if the email already exists in the database
    $check_sql = "SELECT * FROM alumni WHERE email = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Email already exists
        $error_message = "This email is already registered.";
    } else {
        // Insert the new user if the email doesn't exist
        $sql = "INSERT INTO alumni (first_name, last_name, middle_name, gender, batch, course_graduated, email, password, connected_to)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssissss", $first_name, $last_name, $middle_name, $gender, $batch, $course_graduated, $email, $password, $connected_to);
        
        if ($stmt->execute()) {
            $success_message = "Account created successfully. Please wait for admin verification.";
        } else {
            $error_message = "Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modern Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f7fa;
        }
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
            text-align: center;
        }
        .form-title {
            font-weight: bold;
            margin-bottom: 20px;
            color: #007bff;
        }
        .btn-custom {
            background-color: #007bff;
            color: white;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #0056b3;
        }
        .message-container {
            text-align: center;
            margin-top: 20px;
        }
        .error-message {
            color: red;
        }
        .success-message {
            color: green;
        }
    </style>
</head>
<body>

    <div class="form-container">
        <h2 class="form-title">Register</h2>

        <form method="post" action="register.php">
            <div class="row mb-3">
                <div class="col">
                    <label for="firstName" class="form-label">First Name</label>
                    <input type="text" class="form-control" id="firstName" name="first_name" required>
                </div>
                <div class="col">
                    <label for="lastName" class="form-label">Last Name</label>
                    <input type="text" class="form-control" id="lastName" name="last_name" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middle_name">
                </div>
                <div class="col">
                    <label for="gender" class="form-label">Gender</label>
                    <select class="form-select" id="gender" name="gender" required>
                        <option selected disabled value="">Choose...</option>
                        <option value="Male">Male</option>
                        <option value="Female">Female</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="batch" class="form-label">Batch</label>
                    <input type="text" class="form-control" id="batch" name="batch" required>
                </div>
                <div class="col">
                    <label for="courseGraduated" class="form-label">Course Graduated</label>
                    <input type="text" class="form-control" id="courseGraduated" name="course_graduated" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="connectedTo" class="form-label">Currently Connected To</label>
                    <input type="text" class="form-control" id="connectedTo" name="connected_to" required>
                </div>
                <div class="col">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
            </div>

            <button type="submit" class="btn btn-custom">Register</button>
        </form>

        <!-- Success/Error Messages Below the Button -->
        <div class="message-container">
            <?php if (isset($error_message)) { ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php } elseif (isset($success_message)) { ?>
                <p class="success-message"><?php echo $success_message; ?></p>
            <?php } ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
