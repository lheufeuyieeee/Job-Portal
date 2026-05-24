<?php
session_start();
require_once "dbconnection.php";

if (!isset($_SESSION['user_id'])) {
    die("Please login first.");
}

$user_id = $_SESSION['user_id']; // Logged-in user ID

/* ================================
   FETCH LOGGED-IN USER DATA
================================ */
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, email, role, birthdate, created_at FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

$first_name_db = $user['first_name'] ?? '';
$middle_name_db  = $user['middle_name'] ?? '';
$last_name_db  = $user['last_name'] ?? '';
$email_db      = $user['email'] ?? '';
$birthdate_db  = $user['birthdate'] ?? '';
$user_role     = strtolower($user['role'] ?? '');

$position_applied = "";
$job_id = 0;

/* ================================
   GET JOB TITLE USING ID
================================ */
if (isset($_GET['id'])) {

    $job_id = intval($_GET['id']);

    $jobQuery = $conn->prepare("SELECT position_applied FROM jobs WHERE job_id = ?");
    $jobQuery->bind_param("i", $job_id);
    $jobQuery->execute();
    $result = $jobQuery->get_result();

    if ($result->num_rows > 0) {
        $job = $result->fetch_assoc();
        $position_applied = $job['position_applied'];
    } else {
        die("Invalid Job ID.");
    }

    $jobQuery->close();
}

/* ================================
   HANDLE FORM SUBMISSION
================================ */
if (isset($_POST['submit'])) {

    $first_name = $_POST['first_name'] ?? '';
    $middle_name = $_POST['middle_name'] ?? '';
    $last_name = $_POST['last_name'] ?? '';
    $suffix = $_POST['suffix'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $birthdate = $_POST['birthdate'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $address = $_POST['address'] ?? '';
    $experience = $_POST['experience'] ?? '';

    $uploadDir = "uploads/applicants/";
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    /* ================================
       RESUME UPLOAD (IMAGE ONLY)
    ================================= */
    $resume_path = "";
    if (!empty($_FILES['resume']['name']) && $_FILES['resume']['error'] === 0) {

        $resume_ext = strtolower(pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png'];

        if (!in_array($resume_ext, $allowed)) {
            die("Resume must be JPG, JPEG, or PNG.");
        }

        $resume_name = uniqid("resume_", true) . "." . $resume_ext;
        $resume_path = $uploadDir . $resume_name;

        if (!move_uploaded_file($_FILES['resume']['tmp_name'], $resume_path)) {
            die("Failed to upload resume.");
        }
    }

    /* ================================
       PHOTO UPLOAD (IMAGE ONLY)
    ================================= */
    $photo_path = "";
    if (!empty($_FILES['photo']['name']) && $_FILES['photo']['error'] === 0) {

        $photo_ext = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png'];

        if (!in_array($photo_ext, $allowed)) {
            die("Photo must be JPG, JPEG or PNG.");
        }

        $photo_name = uniqid("photo_", true) . "." . $photo_ext;
        $photo_path = $uploadDir . $photo_name;

        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            die("Failed to upload photo.");
        }
    }

    /* ================================
       INSERT INTO DATABASE
    ================================= */
    $stmt = $conn->prepare("INSERT INTO applicants 
(user_id, first_name, middle_name, last_name, suffix, gender, birthdate,
 email, phone, address, experience, position_applied, job_id, resume_path, photo_path)
VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt === false) {
        die("Prepare failed: " . $conn->error);
    }

    // FIXED: 15 variables → 15 characters in type string
$stmt->bind_param(
    "issssssssssssis",
    $user_id,
    $first_name,
    $middle_name,
    $last_name,
    $suffix,
    $gender,
    $birthdate,
    $email,
    $phone,
    $address,
    $experience,
    $position_applied,
    $job_id,
    $resume_path,
    $photo_path
);

    if ($stmt->execute()) {
        $successMessage = "
        <h3>Application Submitted Successfully!</h3>
        <p>Your application has been received. The employer will contact you soon.</p>";
    } else {
        $errorMessage = "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Apply Job | AFE JOBS</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { margin:0; padding:0; background: linear-gradient(135deg, #0a1f44, #123c7a); font-family:"Segoe UI", Arial, sans-serif; }
.container { width:450px; background:white; margin:30px auto 60px; padding:40px 30px; border-radius:12px; box-shadow:0 15px 40px rgba(0,0,0,0.25); }
h2 { text-align:center; margin-bottom:20px; }
label { font-weight:bold; }
input, textarea, select { width:100%; padding:8px; margin-top:5px; margin-bottom:15px; border-radius:5px; border:1px solid #ccc; }
textarea { resize:none; }
button { padding:8px 15px; background:#4facfe; border:none; color:white; font-size:16px; border-radius:5px; cursor:pointer; }
button:hover { background:#007bff; }
.success { background:#e6ffed; padding:15px; border:1px solid #28a745; color:#155724; border-radius:8px; margin-bottom:15px; }
.error { background:#ffe6e6; padding:15px; border:1px solid red; color:darkred; border-radius:8px; margin-bottom:15px; }
.logo1 { background:white; color:#123c7a; width:80px; font-weight:bold; border-radius:8px; padding:8px 15px; margin-left:40px; font-size:18px; box-shadow:0 4px 6px rgba(0,0,0,0.2); }
.back { margin-bottom:25px; text-align:left; }
.back button { background-color:#123c7a; color:white; border:none; padding:10px 18px; border-radius:6px; font-size:14px; cursor:pointer; display:inline-flex; align-items:center; gap:8px; }
.back button:hover { background-color:#0a1f44; }
.submit { width:100px; margin-left:175px; }
</style>
</head>
<body>

<p class="logo1">AFE JOBS</p>

<div class="container">
<div class="back">
  <button onclick="location.href='homepage.php'">
    <i class="fa-solid fa-arrow-left"></i> Back
  </button>
</div>

<h2>Applicants Application Form</h2>

<?php if(isset($successMessage)) { ?>
    <div class="success"><?php echo $successMessage; ?></div>
<?php } ?>

<?php if(isset($errorMessage)) { ?>
    <div class="error"><?php echo $errorMessage; ?></div>
<?php } ?>

<?php if(!isset($successMessage)) { ?>
<form method="POST" enctype="multipart/form-data">

    <label>Position Applied:</label>
    <input type="text" name="position_applied" value="<?php echo htmlspecialchars($position_applied); ?>" readonly required>

    <label>First Name:</label>
    <input type="text" name="first_name"
    value="<?php echo htmlspecialchars($first_name_db); ?>" readonly>

    <label>Middle Name:</label>
    <input type="text" name="middle_name"
    value="<?php echo htmlspecialchars($middle_name_db); ?>" readonly>

    <label>Last Name:</label>
    <input type="text" name="last_name" 
     value="<?php echo htmlspecialchars($last_name_db); ?>" readonly>


     <label for="suffix">Suffix:</label>
    <select id="suffix" name="suffix">
        <option value="">Select Suffix</option>
        <option value="none">none</option>
        <option value="Jr.">Jr.</option>
        <option value="Sr.">Sr.</option>
        <option value="III">III</option>
        <option value="IV">IV</option>
    </select>

    <label>Email:</label>
    <input type="email" name="email"
    value="<?php echo htmlspecialchars($email_db); ?>" readonly>

    <label>Gender:</label>
    <select name="gender" required>
        <option value="">Select gender</option>
        <option value="Male">Male</option>
        <option value="Female">Female</option> 
    </select>

    <label>Birthdate:</label>
    <input type="date" name="birthdate"
    value="<?php echo htmlspecialchars($birthdate_db); ?>" readonly>

    <label>Phone:</label>
    <input type="tel" name="phone" required>

    <label>Address:</label>
    <textarea name="address" required></textarea>

    <label>Experience:</label>
    <textarea name="experience" required></textarea>

    <label>Resume (JPG, PNG only):</label>
    <input type="file" name="resume" accept="image/*" required>

    <label>Photo (JPG, PNG):</label>
    <input type="file" name="photo" accept="image/*" required>

    <div class="submit">
        <button type="submit" name="submit">Submit</button>
    </div>
</form>
<?php } ?>

</div>
</body>
</html>