<?php
session_start();
require_once "dbconnection.php";

/* ==============================
   SESSION PROTECTION
============================== */
if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit();
}

$user_id = $_SESSION['user_id'];

/*DELETE JOB_ID IN DATABASE */
if (isset($_POST['job_id'])) {
    $job_id = intval($_POST['job_id']); // sanitize

    // Prepare delete query
    $stmt = $conn->prepare("DELETE FROM jobs WHERE id = ?");
    $stmt->bind_param("i", $job_id);

    if ($stmt->execute()) {
        echo "deleted"; // MUST match your JS condition
    } else {
        echo "error";
    }
}
/* ==============================
   FETCH LOGGED-IN USER
============================== */
$stmt = $conn->prepare("SELECT first_name, middle_name, last_name, email, role, birthdate, created_at 
                        FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$user_role = strtolower($user['role']);

/* ==============================
   SEARCH JOBS
============================== */
$position_applied = $_GET['position_applied'] ?? '';
$location = $_GET['location'] ?? '';

$searchSql = "SELECT * FROM jobs WHERE 1=1";

if (!empty($position_applied)) {
    $searchSql .= " AND position_applied LIKE '%" . $conn->real_escape_string($position_applied) . "%'";
}

if (!empty($location)) {
    $searchSql .= " AND location LIKE '%" . $conn->real_escape_string($location) . "%'";
}

$searchSql .= " ORDER BY job_id DESC";

$allJobs = $conn->query($searchSql);

/* ==============================
   FETCH APPLICANTS (Employer Only)
============================== */
$applicants = null;

if ($user_role === 'employer') {

    $stmtApplicants = $conn->prepare("
        SELECT a.*, u.first_name, u.middle_name, u.last_name, u.email, u.phone
        FROM applicants a
        INNER JOIN jobs j ON a.job_id = j.job_id
        INNER JOIN users u ON a.user_id = u.user_id
        WHERE j.user_id = ?
        ORDER BY a.applicant_id DESC
    ");
    $stmtApplicants->bind_param("i", $user_id);
    $stmtApplicants->execute();
    $applicants = $stmtApplicants->get_result();
}

/* ==============================
   JOBS POSTED / APPLIED
============================== */
if ($user_role === 'employer') {

    // Employer → jobs they posted
    $stmtJobs = $conn->prepare("SELECT * FROM jobs WHERE user_id = ? ORDER BY job_id DESC");
    $stmtJobs->bind_param("i", $user_id);
    $stmtJobs->execute();
    $jobs = $stmtJobs->get_result();

} else {

    // Applicant → jobs applied
    $stmtJobs = $conn->prepare("
        SELECT j.*, a.created_at AS position_applied
        FROM applicants a
        INNER JOIN jobs j ON a.job_id = j.job_id
        WHERE a.user_id = ?
        ORDER BY a.created_at DESC
    ");
    $stmtJobs->bind_param("i", $user_id);
    $stmtJobs->execute();
    $jobs = $stmtJobs->get_result();
}

/* ==============================
   DELETE APPLICANT (Employer Only)
============================== */
if (isset($_GET['delete_id']) && $user_role === 'employer') {

    $delete_id = intval($_GET['delete_id']);

    $check = $conn->prepare("
        SELECT a.applicant_Id
        FROM applicants a
        INNER JOIN jobs j ON a.job_id = j.job_id
        WHERE a.applicant_Id = ? AND j.user_id = ?
    ");
    $check->bind_param("ii", $delete_id, $user_id);
    $check->execute();
    $resultCheck = $check->get_result();

    if ($resultCheck->num_rows > 0) {
        $delete = $conn->prepare("DELETE FROM applicants WHERE applicant_id = ?");
        $delete->bind_param("i", $delete_id);
        $delete->execute();

        header("Location: homepage.php");
        exit();
    }
}

?>



<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Job Portal | Bohol</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" 
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
/* ===== General ===== */
body {
  font-family: Arial, sans-serif;
  margin: 0;
  padding: 0;
  background:#123c7a;
  color: #fff;
}

a { text-decoration: none; color: inherit; }

/* ===== Header ===== */
.header {
  padding: 8px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  background: #123c7a;
}

.toggle-btn {
  font-size: 24px;
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
}

#linksMenu {
  display: none;
  background-color: rgba(255, 255, 255, 0.95);
  color: #000;
  padding: 10px;
  border-radius: 6px;
  position: absolute;
  top: 60px;
  left: 20px;
}

#linksMenu button {
  display: block;
  padding: 5px 10px;
  margin-bottom: 5px;
  background: lightskyblue;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.logo1 {
  background: white;
  color: darkblue;
  padding: 5px 10px;
  border-radius: 4px;
  font-weight: bold;
}

.right-side {
  display: flex;
  gap: 10px;
}

.right-side button {
  background: white;
  color: #123c7a;
  border: none;
  border-radius: 4px;
  padding: 8px;
  cursor: pointer;
}

.right-side button i {
  font-size: 16px;
}

/* ===== Search ===== */
.search-container {
  display: flex;
  justify-content: right;
  padding: 15px 20px;
  background: #fff;
  color: #000;
}

.search-container input {
  padding: 8px;
  width: 180px;
  border: 2px solid #06152c;
  border-radius: 4px 0 0 4px;
}

.search-container button {
  padding: 9px 15px;
  border: none;
  background: #1e90ff;
  color: #000000;
  border-radius: 3px;
  cursor: pointer;
  font-weight: bold;
}

/* ===== Job List ===== */
.job-list {
  background: #f4f6f8;
  max-width: 900px;
  margin: 30px auto;
  padding: 20px;
  border-radius: 6px;
  color: #000;
}

.job-item {
  background: #fff;
  padding: 15px;
  margin-bottom: 12px;
  border-left: 5px solid #1e90ff;
  border-radius: 4px;
}

.job-item h3 {
  margin: 0 0 5px 0;
  color: #123c7a;
}

.job-meta {
  color: #555;
  font-size: 14px;
}

.apply-btn {
  display: inline-block;
  margin-top: 8px;
  padding: 6px 12px;
  background: #1e90ff;
  color: #fff;
  border-radius: 4px;
  font-size: 14px;
}

.apply-btn:hover {
  background: #0b72d9;
}

.no-jobs {
  text-align: center;
  color: #123c7a;
  font-size: 18px;
  margin-top: 30px;
}
#jobordersSection {
  position: relative;
  z-index: 10;
}
.job-list h2 {
  text-align: center;
}
.job-list button{
  margin-left: 600px;
}
</style>
</head>

<body>

<!-- HEADER -->
<div class="header">
  <button id="toggle-logout">
  <i class="fa fa-sign-out"></i> Log Out 
</button>

<script>
  document.getElementById('toggle-logout').addEventListener('click', () => {
    if (confirm('Are you sure you want to log out?')) {
      window.location.href = 'intro.php';
    }
  });
  </script>

  <p class="logo1">AFE JOBS</p>

<div class="right-side">
  <button onclick="toggleAccount()">
    <i class="fa-solid fa-circle-user"></i>
  </button>

  <button onclick="toggleApplicant()">
  <i class="fa-solid fa-envelope"></i>
  </button>

<?php if ($user_role === 'employer') { ?>
    <button onclick="toggleJoborders()">
        <i class="fa-solid fa-clipboard-list"></i>
    </button>
<?php } ?>

  <button onclick="location.href='post.php'">+ Post a Job</button>
  </div>

</div>

<div class="search-container">
<form method="get" action="">
  <button type="button" onclick="window.location.href='<?= basename($_SERVER['PHP_SELF']) ?>'">
    <i class="fa-solid fa-arrow-left"></i>
</button>
    <input type="text" name="position" placeholder="Position" value="<?php echo htmlspecialchars($position_applied); ?>">
    <input type="text" name="location" placeholder="Location" value="<?php echo htmlspecialchars($location); ?>">
    <button type="submit">Search</button>
    <!-- Back/Reset button -->
</form>
</div>

</table>
</div>


<!--APPLICANT OR EMPLOYER POSTED-->
<div class="job-list" id="applicantSection" style="background:#ffffff; display:none;">

    <h2>
        <?php 
        if ($user_role === 'employer') echo "Jobs You Posted"; 
        else echo "Jobs You Applied To"; 
        ?>
    </h2>

    <?php
    if ($jobs->num_rows > 0) {
        while ($job = $jobs->fetch_assoc()) {

            echo '<div class="job-item" id="job' . $job['job_id'] . '">';

            echo '<h3>' . htmlspecialchars($job['position_applied']) . '</h3>';
            echo '<div class="job-meta">Location: ' . htmlspecialchars($job['location']) . ' | Salary: ' . htmlspecialchars($job['salary']) . '</div>';
            echo '<p>' . nl2br(htmlspecialchars($job['description'])) . '</p>';

            // 🔥 SHOW REMOVE BUTTON ONLY FOR EMPLOYER
            if ($user_role === 'employer') {
                echo '<small>Posted at: ' . date("F d, Y", strtotime($job['created_at'])) . '</small>';
                echo '<button class="delete" onclick="removeJob(' . $job['job_id'] . ')">Remove</button>';
            } else {
                echo '<small>Applied at: ' . date("F d, Y", strtotime($job['created_at'])) . '</small>';
            }

            echo '</div>';
        }
    } else {
        echo '<p class="no-jobs">No jobs found.</p>';
    }
    ?>
</div>

<script>
function removeJob(job_id) {
    if (confirm("Remove this job?")) {

    fetch("homepage.php", ) {
      method: "POST",
      headers: {"Content-Type": "application/x-www-form-urlencoded"},
      body: "job_id" + job_id
})
.then(res => res.text())
.then(data => {
  if (data.trim() === "deleted") {
    let job = document.getElementById("job" +job_id);
  }
})
    }
</script>

<div class="job-list" id="jobordersSection" style="background:#ffffff; display:none;">
<h2>Applicants For Your Jobs</h2>

<table width="100%" border="0" cellspacing="0" cellpadding="10">
<tr>
    <th>Full Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Position</th>
    <th>Resume</th>
    <th>Action</th>
</tr>

<?php if ($applicants && $applicants->num_rows > 0) { ?>
    <?php while($row = $applicants->fetch_assoc()) { ?>
        <tr id="row<?= $row['applicant_Id']; ?>">
            <td>
                <?= htmlspecialchars(trim(
                    $row['first_name'] . ' ' .
                    ($row['middle_name'] ? $row['middle_name'] . ' ' : '') .
                    $row['last_name']
                )); ?>
            </td>
            <td><?= htmlspecialchars($row['email']); ?></td>
            <td><?= htmlspecialchars($row['phone']); ?></td>
            <td><?= htmlspecialchars($row['position_applied']); ?></td>
            <td>
                <a href="<?= htmlspecialchars($row['resume_path']); ?>" target="_blank">
                    View Resume
                </a>
            </td>
            <td>
                <button class="delete"
                    onclick="confirmDelete(<?= $row['applicant_Id']; ?>, this)">
                    Remove
                </button>
            </td>
        </tr>
    <?php } ?>
<?php } else { ?>
    <tr>
        <td colspan="6" style="text-align:center;">No applicants yet.</td>
    </tr>
<?php } ?>
</table>
</div>

<script>
function confirmDelete(applicant_Id, btn) {
    if (confirm("Remove this applicant from view?")) {

        // 🔥 Hide the row instead of deleting
        let row = document.getElementById("row" + applicant_Id);
        if (row) {
            row.style.display = "none";
        }
    }
}
</script>

<!--ACCOUNT INFORMATION-->
<div class="job-list" id="accountSection" style="background:#ffffff; display:none;">
  <h2>My Account Information</h2>

  <?php if ($user_id): ?>
<p><strong>Full Name:</strong> 
<?php 
    echo htmlspecialchars($user['first_name'] . 
        (!empty($user['middle_name']) ? ' ' . $user['middle_name'] : '') . 
        ' ' . $user['last_name'] . 
        (!empty($user['suffix']) ? ', ' . $user['suffix'] : '')
    );
?>
</p>
      <p><strong>Email:</strong> <?= htmlspecialchars($user['email']); ?></p>
      <p><strong>Role:</strong> <?= htmlspecialchars($user['role']); ?></p>
      <p><strong>Birthdate:</strong> <?= htmlspecialchars($user['birthdate']); ?></p>
      <p><strong>Member Since:</strong> 
          <?= date("F d, Y", strtotime($user['created_at'])); ?>
      </p>
  <?php else: ?>
      <p>User not found.</p>
  <?php endif; ?>
</div>

<script>
function toggleAccount() {
  const section = document.getElementById("accountSection");
  section.style.display = section.style.display === "none" ? "block" : "none";
}
function toggleApplicant() {
  const section = document.getElementById("applicantSection");
  section.style.display = section.style.display === "none" ? "block" : "none";
}
function toggleJoborders() {
  const section = document.getElementById("jobordersSection");
  if (section.style.display === "none" || section.style.display === "") {
      section.style.display = "block";
  } else {
      section.style.display = "none";
  }
}
function hideAllSections() {
    document.getElementById("accountSection").style.display = "none";
    document.getElementById("applicantSection").style.display = "none";
    document.getElementById("jobordersSection").style.display = "none";
}

function toggleAccount() {
    const section = document.getElementById("accountSection");
    const isVisible = section.style.display === "block";
    hideAllSections();
    section.style.display = isVisible ? "none" : "block";
}

function toggleApplicant() {
    const section = document.getElementById("applicantSection");
    const isVisible = section.style.display === "block";
    hideAllSections();
    section.style.display = isVisible ? "none" : "block";
}

function toggleJoborders() {
    const section = document.getElementById("jobordersSection");
    const isVisible = section.style.display === "block";
    hideAllSections();
    section.style.display = isVisible ? "none" : "block";
}

function confirmDelete(id) {
    if (confirm("Are you sure you want to remove this applicant?")) {
        window.location.href = "?delete_id=" + id;
    }
}
</script>
  </script>

<!-- JOB LIST -->
<div class="job-list">
  <h2>Current Job Vacancies</h2>
  <?php
if ($allJobs && $allJobs->num_rows > 0)  {
while ($job = $allJobs->fetch_assoc()) {
          echo '<div class="job-item">';
          echo '<h3 class="job-title">' . htmlspecialchars($job['position_applied']) . '</h3>';
          echo '<div class="job-meta">Location: ' . htmlspecialchars($job['location']) . ' | Salary: ' . htmlspecialchars($job['salary']) . '</div>';
          echo '<p>' . nl2br(htmlspecialchars($job['description'])) . '</p>';

          if ($user_role === 'employer') {
              echo '<button class="apply-btn" style="background:gray; cursor:not-allowed;" disabled>Employers cannot apply</button>';
          } else {
              echo '<a href="apply.php?id=' . $job['job_id'] . '" style="margin-left: 750px;" class="apply-btn">Apply Now</a>';
          }

          echo '</div>'; // close job-item
      }
  } else {
      echo '<p class="no-jobs">No jobs found.</p>';
  }
  ?>
</div>

<div class="job-list" id="jobordersSection" style="background:#ffffff; display:none;">
<h2>Applicants For Your Jobs</h2>

<table>
<tr>
    <th>Full Name</th>
    <th>Email</th>
    <th>Phone</th>
    <th>Position</th>
    <th>Resume</th>
    <th>Action</th>
</tr>

<script>
function toggleLinks() {
  const menu = document.getElementById("linksMenu");
  menu.style.display = menu.style.display === "block" ? "none" : "block";
}

function confirmLogout() {
  if (confirm("Are you sure you want to logout?")) {
    window.location.href = "intro.php";
  }
}
</script>
 <?php   $conn->close(); ?>
</body>
</html>