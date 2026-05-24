<?php
session_start();
require_once"dbconnection.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Collect and sanitize form data
    $first_name  = $conn->real_escape_string($_POST['first_name']);
    $middle_name  = $conn->real_escape_string($_POST['middle_name']);
    $last_name  = $conn->real_escape_string($_POST['last_name']);
    $suffix  = $conn->real_escape_string($_POST['suffix']);
    $email     = $conn->real_escape_string($_POST['email']);
    $phone     = $conn->real_escape_string($_POST['phone']);
    $address   = $conn->real_escape_string($_POST['address']);
    $password  = $_POST['password'];
    $birthdate = $_POST['birthdate'];
    $gender    = $_POST['gender'];

    // Check if email already exists
    $check = $conn->query("SELECT * FROM users WHERE email='$email'");
    if ($check->num_rows > 0) {
        $message = "Email already registered!";
    } else {
        // Insert data into table
        $sql = "INSERT INTO users (first_name, middle_name, last_name, suffix, email, phone, address, password, birthdate, gender)
                VALUES ('$first_name', '$middle_name', '$last_name', '$suffix', '$email', '$phone', '$address', '$password', '$birthdate', '$gender')";

        if ($conn->query($sql) === TRUE) {
            $message = "Registration successful! You can <a href='signin.php'>login now</a>.";
        } else {
            $message = "Error: " . $conn->error;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<link rel="stylesheet" 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<title>Job Portal | Register</title>

<style>
/* ===== BODY ===== */
body {
    margin: 0;
    background: linear-gradient(135deg, #0a1f44, #123c7a);
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

/* ===== LOGO ===== */
.logo1 {
    background-color: white;
    color: #123c7a;
    width: 80px;
    font-weight: bold;
    border-radius: 8px;
    padding: 8px 15px;
    margin-left: 40px;
    font-size: 18px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.2);
}

/* ===== CONTAINER ===== */
.container {
    width: 450px;
    background: white;
    margin: 30px auto 60px;
    padding: 40px 30px;
    border-radius: 12px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.25);
}

/* ===== BACK BUTTON ===== */
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

/* ===== FORM HEADER ===== */
h2 {
    text-align: center;
    color: #123c7a;
    margin-bottom: 30px;
    font-size: 28px;
    font-weight: bold;
}

/* ===== FORM GROUP ===== */
.form-group {
    margin-bottom: 20px;
    display: flex;
    flex-direction: column;
}

label {
    margin-bottom: 8px;
    font-weight: 600;
    color: #123c7a;
}

input, select {
    padding: 12px 10px;
    border-radius: 6px;
    border: 1px solid #ccc;
    font-size: 14px;
    transition: 0.3s ease;
}

input:focus, select:focus {
    outline: none;
    border-color: #123c7a;
    box-shadow: 0 0 5px rgba(18, 60, 122, 0.5);
}

/* ===== SUBMIT BUTTON ===== */
input[type="submit"] {
    width: 100%;
    background-color: #123c7a;
    color: white;
    border: none;
    padding: 14px 0;
    border-radius: 8px;
    font-size: 16px;
    font-weight: bold;
    cursor: pointer;
    transition: 0.3s ease;
    margin-top: 10px;
}

input[type="submit"]:hover {
    background-color: #0a1f44;
}

/* ===== MESSAGE ===== */
.message {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    color: red;
    font-size: 15px;
}

/* RESPONSIVE */
@media (max-width: 500px) {
    .container {
        width: 90%;
        padding: 30px 20px;
    }
}
</style>
</head>

<body>

<p class="logo1">AFE JOBS</p>

<div class="container">

<div class="back">
  <button onclick="location.href='signin.php'">
    <i class="fa-solid fa-arrow-left"></i> Back
  </button>
</div>

<h2>Job Seeker Registration</h2>

<?php if (!empty($message)) { ?>
    <div class="message"><?php echo $message; ?></div>
<?php } ?>

<form method="post">

<div class="form-group">
<label>First Name</label>
<input type="text" name="first_name" placeholder="Enter your first name" required>
</div>

<div class="form-group">
<label>Middle Name</label>
<input type="text" name="middle_name" placeholder="Enter your middle name" required>
</div>

<div class="form-group">
<label>Last Name</label>
<input type="text" name="last_name" placeholder="Enter your last name" required>
</div>

<div class="form-group">
    <label for="suffix">Suffix:</label>
    <select id="suffix" name="suffix">
        <option value="">Select Suffix</option>
        <option value="none">none</option>
        <option value="Jr.">Jr.</option>
        <option value="Sr.">Sr.</option>
        <option value="III">III</option>
        <option value="IV">IV</option>
    </select>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email" placeholder="Enter your email" required>
</div>

<div class="form-group">
<label>Phone</label>
<input type="tel" name="phone" placeholder="Enter your phone number" required>
</div>

<div class="form-group">
<label>Address</label>
<input type="text" name="address" placeholder="Enter your address" required>
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" placeholder="Enter a strong password" required>
</div>

<div class="form-group">
<label>Birthdate</label>
<input type="date" name="birthdate" required>
</div>

<div class="form-group">
<label>Gender</label>
<select name="gender" required>
<option value="">Select gender</option>
<option value="Male">Male</option>
<option value="Female">Female</option>
</select>
</div>

<div class="form-group">
<label>Role</label>
<select name="role"required>
<option value="">Select Role</option>
<option value="Applicant">Applicant</option>
<option value="Employer">Employeer</option>
</select>
</div>

<input type="submit" value="Register">

</form>
</div>

</body>
</html>
