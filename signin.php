<?php
session_start(); // Start session for login

$conn = new mysqli("localhost", "root", "", "job_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        // Prepare statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT user_id, first_name, role, password FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();

        // Get result
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();

            // If passwords are hashed in DB, use password_verify
            if ($password === $user['password']) {
                // Store user info in session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['first_name'] = $user['first_name'];

                header("Location: homepage.php");
                exit;
            } else {
                $message = "";
            }
        } else {
            $message = "";
        }

        $stmt->close();
    } else {
        $message = "";
    }
}

echo $message;
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Job Portal | Login</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

<style>
body {
    margin: 0;
    padding: 0;
    background: linear-gradient(135deg, #0a1f44, #123c7a);
    font-family: "Segoe UI", Arial, sans-serif;
}

.header {
    padding: 20px;
}
.logo {
    display: inline-block;
    background-color: #ffffff;
    color: #0a1f44;
    padding: 10px 18px;
    font-weight: 700;
    letter-spacing: 2px;
    border-radius: 6px;
    font-size: 14px;
}

.container {
    background-color: #ffffff;
    width: 420px;
    margin: 30px auto;
    padding: 30px;
    border-radius: 12px;
    box-shadow: 0 15px 30px rgba(0,0,0,0.25);
}

.container h2 {
    text-align: center;
    margin-bottom: 25px;
    color: #0a1f44;
}

.form-group {
    margin-bottom: 15px;
}

.form-group label {
    display: block;
    font-size: 14px;
    margin-bottom: 5px;
    font-weight: 600;
}

.container input {
    width: 100%;
    padding: 10px 12px;
    border: 1px solid #ccc;
    border-radius: 6px;
    font-size: 14px;
    box-sizing: border-box;
}

.container input:focus {
    outline: none;
    border-color: #0a1f44;
    box-shadow: 0 0 5px rgba(10,31,68,0.3);
}

.container input[type="submit"] {
    background-color: #123c7a;
    color: #ffffff;
    border: none;
    padding: 12px;
    width: 100%;
    font-size: 15px;
    font-weight: 600;
    border-radius: 6px;
    cursor: pointer;
    margin-top: 10px;
    transition: 0.3s ease;
}

.container input[type="submit"]:hover {
    background-color: #0a1f44;
}

.message {
    text-align: center;
    font-weight: bold;
    margin-bottom: 20px;
    color: red;
    font-size: 15px;
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
</style>
</head>

<body>

<div class="header">
    <div class="logo">AFE JOBS</div>
</div>

<div class="container">

    <div class="back">
        <button onclick="location.href='intro.php'">
            <i class="fa-solid fa-arrow-left"></i> Back
        </button>
    </div>

    <h2>Job Seeker Sign In</h2>

    <?php if(!empty($message)) echo "<p class='message'>$message</p>"; ?>

    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">

        <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Enter your email" required>
        </div>

        <div class="form-group">
            <label>Password</label>
            <input type="password" name="password" placeholder="Enter your password" required>
        </div>

        <input type="submit" value="Log In">

        <p style="text-align:center; margin-top: 10px;">
            Create an Account? <a href="portal.php">Sign Up</a>
        </p>

    </form>
</div>

</body>
</html>
