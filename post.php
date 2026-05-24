<?php
session_start();
require_once "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: homepage.php"); // redirect if not logged in
    exit;
}

$user_id = $_SESSION['user_id'];

$user_id = $_SESSION['user_id']; // logged-in user's ID
$stmt = $conn->prepare("SELECT first_name, email, role, birthdate, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$user_role = strtolower($user['role'] ?? '');

// Restrict access based on role
if (isset($_SESSION['role']) && $_SESSION['role'] === 'applicant') {
    echo "
    <div style='text-align:center; margin-top:100px; font-family: Arial, sans-serif;'>
        <h2 style='color:red;'>Access Denied</h2>
        <p>Applicants cannot post jobs.</p>
        <button onclick=\"window.location.href='homepage.php'\" 
                style='background-color:#123c7a; color:white; border:none; padding:10px 20px; border-radius:6px; cursor:pointer; font-size:16px;'>
            &larr; Back to Homepage
        </button>
    </div>
    ";
    exit;
}


// Connect to database
$conn = new mysqli("localhost", "root", "", "job_portal");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle the form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Validate the input
    $position_applied = $_POST['position_applied'];
    $salary = $_POST['salary'];
    $location = $_POST['location'];
    $description = $_POST['description'];

    // Insert the job post into the database
    $sql = "INSERT INTO jobs (position_applied, salary, location, description, user_id) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $position_applied, $salary, $location, $description, $_SESSION['user_id']);

    if ($stmt->execute()) {
        $successMessage = "Job posted successfully!";
    } else {
        $errorMessage = "Error: Could not post the job.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Post a Job</title>
<style>
body {
  font-family: Arial, sans-serif;
  background: linear-gradient(135deg, #0a1f44, #123c7a);
  padding: 20px;
}
.form-container {
  background: white;
  padding: 20px;
  border-radius: 6px;
  max-width: 500px;
  margin: auto;
}
.form-container h2 {
  text-align: center;
  margin-bottom: 15px;
}
.form-group {
  margin-bottom: 12px;
}
.form-group label {
  display: block;
  margin-bottom: 4px;
}
.form-group input,
.form-group textarea {
  width: 100%;
  padding: 8px;
  border: 1px solid #888;
  border-radius: 4px;
}
.form-group input[type=file] {
  padding: 3px;
}
.back {
  margin-bottom: 25px;
  text-align: left;
}
.back button {
  background-color: #123c7a;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  font-size: 14px;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: 0.3s ease;
}
.back button:hover {
  background-color: #0a1f44;
}
.success {
  background-color: #d4edda;
  color: #155724;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
}
.error {
  background-color: #f8d7da;
  color: #721c24;
  padding: 10px;
  margin-bottom: 15px;
  border-radius: 5px;
}
button[type=submit] {
  background-color: #123c7a;
  color: white;
  border: none;
  padding: 10px 18px;
  border-radius: 6px;
  font-size: 16px;
  cursor: pointer;
  width: 100%;
}
button[type=submit]:hover {
  background-color: #0a1f44;
}
</style>
</head>
<body>

<div class="form-container">

  <div class="back">
      <button onclick="location.href='homepage.php'">
          <i class="fa-solid fa-arrow-left"></i> Back
      </button>
  </div>

  <h2>Post a Job</h2>

  <?php if(isset($successMessage)) { echo "<div class='success'>$successMessage</div>"; } ?>
  <?php if(isset($errorMessage)) { echo "<div class='error'>$errorMessage</div>"; } ?>

  <form method="POST" enctype="multipart/form-data">


    <div class="form-group">
      <label for="position">Available Position:</label>
      <input type="text" id="position_applied" name="position_applied" placeholder="e.g., Sales Associate" required>
    </div>

    <div class="form-group">
      <label for="location">Location:</label>
      <input type="text" id="location" name="location" placeholder="e.g., Cebu City, PH" required>
    </div>

    <div class="form-group">
      <label for="salary">Monthly Salary:</label>
      <input type="text" id="salary" name="salary" placeholder="e.g., ₱20,000 – ₱25,000" required>
    </div>

    <div class="form-group">
      <label for="description">Job Description:</label>
      <textarea id="description" name="description" rows="5" placeholder="Write the job details..." required></textarea>
    </div>

    <button type="submit" name="submit">Post Job</button>

  </form>
</div>

</body>
</html>
