<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>JOB PORTAL | PORTAL</title>

<style>
body {
    margin: 0;
    font-family: Arial, sans-serif;
}

/* ===== HEADER (FULL WIDTH) ===== */
.header {
    background: linear-gradient(135deg, #0a1f44, #123c7a);
    width: 100%;
    height: 55px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 40px;
    box-sizing: border-box;
}

.logo {
    background-color: white;
    color: darkblue;
    padding: 5px 12px;
    font-weight: bold;
    border-radius: 6px;
}

.header nav a {
    color: white;
    text-decoration: none;
    margin-left: 20px;
    font-size: 15px;
}

.header nav a:hover {
    text-decoration: underline;
}

html {
    scroll-behavior: smooth;
}

/* ===== CENTERED MAIN CONTENT ===== */
.main-container {
    width: 1000px;
    margin: auto;
}

/* ===== SECTION TITLES ===== */
.top {
    color: #123c7a;
    font-size: 35px;
    font-weight: bold;
    text-align: center;
    margin-top: 40px;
}

/* ===== IMAGE SLIDER ===== */
.photo {
    text-align: center;
    margin-top: 20px;
}

.photo img {
    width: 900px;
    height: 350px;
    object-fit: cover;
}

/* ===== HOME TEXT ===== */
.h1 {
    color: darkblue;
    font-size: 35px;
    text-align: center;
    margin-top: 20px;
}

.topic {
    text-align: center;
    font-size: 18px;
    margin-top: 10px;
}

.signin {
    text-align: center;
    margin-top: 20px;
}

.signin button {
    color: white;
    background-color: darkblue;
    width: 120px;
    height: 40px;
    border-radius: 5px;
    border: none;
    cursor: pointer;
}

.signin button:hover {
    background-color: #123c7a;
}

/* ===== JOB SECTION ===== */
.explore {
    background-color: rgb(194, 197, 203);
    width: 100%;
    height: 200px;
    margin-top: 20px;
    text-align: center;
    padding-top: 30px;
}

.looking {
    color: green;
    background-color: rgb(207, 213, 22);
    width: 200px;
    margin: auto;
    font-weight: bold;
    border-radius: 5px;
}

.explore1 {
    font-size: 28px;
    margin-top: 15px;
}

.categories {
    color: #123c7a;
    font-size: 30px;
    text-align: center;
    margin-top: 30px;
    font-weight: bold;
}

/* ===== JOB CARDS ===== */
.jobs-container {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 20px;
    flex-wrap: wrap;
}
.job-card1 {
    width: 180px;
    height: 160px;
    background-color: goldenrod;
    border-radius: 8px;
    border: 2px solid #123c7a;
    padding: 10px;
    text-align: center;
}
.job-card2 {
    width: 180px;
    height: 160px;
    background-color: maroon;
    border-radius: 8px;
    border: 2px solid #123c7a;
    padding: 10px;
    text-align: center;
}
.job-card3 {
    width: 180px;
    height: 160px;
    background-color: yellow;
    border-radius: 8px;
    border: 2px solid #123c7a;
    padding: 10px;
    text-align: center;
}
.job-card4 {
    width: 180px;
    height: 160px;
    background-color: green;
    border-radius: 8px;
    border: 2px solid #123c7a;
    padding: 10px;
    text-align: center;
}
.job-card h3 {
    margin: 5px 0;
    font-size: 16px;
    color: black;
}

.job-card p {
    font-size: 11px;
    color: black;
}

/* ===== COMPANIES ===== */
.company {
    text-align: center;
    margin-top: 20px;
}

/* ===== ABOUT ===== */
.info {
    text-align: center;
    margin-top: 20px;
}

.info li {
    list-style: none;
}

</style>
</head>

<body>

<!-- 🔥 FULL WIDTH HEADER -->
<div class="header">
    <div class="logo">AFE JOBS</div>
    <nav>
        <a href="#home">Home</a>
        <a href="#jobs">Jobs</a>
        <a href="#companies">Companies</a>
        <a href="#about">About</a>
    </nav>
</div>

<!-- CENTERED CONTENT -->
<div class="main-container">

<!-- HOME -->
<div class="top" id="home">Home</div>

<div class="photo">
    <img id="slider" src="1.jpg">
</div>

<script>
const images = ["1.jpg", "2.jpg", "3.jpg"];
let i = 0;
setInterval(() => {
    i = (i + 1) % images.length;
    document.getElementById("slider").src = images[i];
}, 5000);
</script>

<p class="h1">Sign in to get started!</p>

<div class="topic">
    <p>Find jobs, create trackable resumes and enrich your applications.</p>
    <p>Carefully crafted for helping the needs of different industries.</p>
</div>

<div class="signin">
    <button onclick="location.href='signin.php'">Sign In</button>
</div>

<!-- JOBS -->
<div class="top" id="jobs">Jobs</div>

<div class="explore">
    <div class="looking">Looking for a job!</div>
    <div class="explore1">Explore More Than 70000+ Jobs</div>
</div>

<div class="categories">Most Demanded Jobs Categories</div>

<div class="jobs-container">
    <div class="job-card1">
        <h3>Administration</h3>
        <p>Organization, communication, computer skills, attention to detail.</p>
    </div>

    <div class="job-card2">
        <h3>Engineering</h3>
        <p>Technical admin, project coordination, office assistant roles.</p>
    </div>

    <div class="job-card3">
        <h3>Manufacturing</h3>
        <p>Production tracking, ERP systems, audits and documentation.</p>
    </div>

    <div class="job-card4">
        <h3>Finance</h3>
        <p>Excel skills, math accuracy, organization, confidentiality.</p>
    </div>
</div>

<!-- COMPANIES -->
<div class="top" id="companies">Companies</div>

<div class="company">
    <a href="#">
        <img src="photo.jpg" width="200">
    </a>
</div>

<!-- ABOUT -->
<div class="top" id="about">About</div>

<div class="phrase">
    <label>Our Job Portal System is an online platform designed to bridge the gap between job seekers and employers. It allows users to create profiles, upload resumes, search for jobs, and apply directly through the platform. Employers can post job listings, manage applications, and find skilled candidates efficiently — all in one place.

This system was developed by students from the BIT International College College of Engineering and Technology as part of our academic work. Our goal in building this platform was to apply what we’ve learned in software development, database systems, and web technologies to solve real-world recruitment challenges.

By creating this project, we not only enhanced our technical skills but also aimed to provide a useful tool that can help students, fresh graduates, and professionals find meaningful job opportunities with ease.</label>
</div>
<br>
<div class="info">
   <i class="fa-solid fa-phone"></i> +63 985 181 848
   <br>
   <a href="feliciaella610@gmail.com">feliciaella610@gmail.com</a>
    <li>Ella Mae A. Felicia</li>
    <li>Friztian L. Campo</li>
    <li>Andrea A. Quiño</li>
</div>

</div>

</body>
</html>
