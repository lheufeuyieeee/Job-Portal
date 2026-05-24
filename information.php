<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "job_portal");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Temporary logged in user
$logged_in_user = 1; // Change when you add login system

// Fetch user info
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $logged_in_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Account Information</title>
    <style>
        body { font-family: Arial; background: #123c7a; 
    }
        .card {
            max-width:600px;
            margin:auto;
            background:#fff;
            padding:30px;
            border-radius:8px;
            box-shadow:0 2px 10px rgba(0,0,0,0.1);
        }
        h2 { border-bottom:2px solid #007bff; padding-bottom:10px; }
        .row { margin:15px 0; }
        .label { font-weight:bold; }
        .status-active { color:green; font-weight:bold; }
        .status-suspended { color:red; font-weight:bold; }
        a { text-decoration:none; color:#007bff; }
    </style>
</head>
<body>

<div class="card">
    <h2>Account Information</h2>

    <?php if ($user): ?>

        <div class="row">
            <span class="label">Full Name:</span>
            <?= htmlspecialchars($user['fullname']); ?>
        </div>

        <div class="row">
            <span class="label">Email:</span>
            <?= htmlspecialchars($user['email']); ?>
        </div>
        
        <div class="row">
            <span class="label">Birthdate:</span>
            <?= htmlspecialchars($user['birthdate']); ?>
        </div>

          <div class="row">
            <span class="label">Role:</span>
            <?= htmlspecialchars($user['role']); ?>
        </div>

        <div class="row">
            <span class="label">Account Status:</span>

            <?php if ($user['status'] == 'active'): ?>
                <span class="status-active">Active</span>
            <?php else: ?>
                <span class="status-suspended">Suspended</span>
            <?php endif; ?>
        </div>

        <div class="row">
            <span class="label">Member Since:</span>
            <?= htmlspecialchars($user['created_at']); ?>
        </div>

    <?php else: ?>
        <p>User not found.</p>
    <?php endif; ?>

    <br>
    <a href="homepage.php">← Back to Dashboard</a>
</div>

</body>
</html>