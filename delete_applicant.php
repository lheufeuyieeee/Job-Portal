<?php
require_once "dbconnection.php";

/* PHPMailer files */
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!isset($_GET['id'])) {
    die("Invalid request.");
}

$applicant_id = intval($_GET['id']);

/* ==========================
   GET APPLICANT INFO
========================== */
$stmt = $conn->prepare("SELECT email, first_name, position_applied 
                        FROM applicants 
                        WHERE applicant_id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    die("Applicant not found.");
}

$applicant = $result->fetch_assoc();
$email = $applicant['email'];
$name = $applicant['first_name'];
$position = $applicant['position_applied'];

$stmt->close();

/* ==========================
   SEND EMAIL USING PHPMailer
========================== */
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'feliciaella610@gmail.com'; // your gmail
    $mail->Password   = 'ucganuxvkeojdgoe';    // your 16-char app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('feliciaella610@gmail.com', 'AFE JOBS');
    $mail->addAddress($email, $name);

    $mail->isHTML(true);
    $mail->Subject = "Application Status Update";

    $mail->Body = "
        <h3>Dear $name,</h3>
        <p>We regret to inform you that your application for 
        <strong>$position</strong> was not selected.</p>
        <p>Thank you for applying to AFE JOBS.</p>
        <br>
        <p>Best regards,<br>AFE JOBS Team</p>
    ";

    $mail->send();

} catch (Exception $e) {
    echo "Email could not be sent. Mailer Error: {$mail->ErrorInfo}";
}

/* ==========================
   DELETE APPLICANT
========================== */
$delete = $conn->prepare("DELETE FROM applicants WHERE applicant_id = ?");
$delete->bind_param("i", $applicant_id);
$delete->execute();
$delete->close();

$conn->close();

header("Location: employer_dashboard.php?deleted=1");
exit();
?>