<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION["user_id"];

include "db.php";
include "notifications.php";

// Get profile information
$sql = "SELECT * FROM profile WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile = mysqli_fetch_assoc($result);
$profile_photo = $profile['profile_photo'] ?? '';

mysqli_stmt_close($stmt);
// Get user's basic information
$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);
// Get user's skill count
$sql = "SELECT COUNT(*) AS skill_count
        FROM user_skills
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$skill_count = mysqli_fetch_assoc($result)["skill_count"];

mysqli_stmt_close($stmt);
// Get user's project count
$sql = "SELECT COUNT(*) AS project_count
        FROM projects
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$project_count = mysqli_fetch_assoc($result)["project_count"];

mysqli_stmt_close($stmt);
// Get user's certificate count
$sql = "SELECT COUNT(*) AS certificate_count
        FROM certificates
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$certificate_count = mysqli_fetch_assoc($result)["certificate_count"];

mysqli_stmt_close($stmt);
// Get user's achievement count
$sql = "SELECT COUNT(*) AS achievement_count
        FROM achievements
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$achievement_count = mysqli_fetch_assoc($result)["achievement_count"];

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Profile | Digital Skill Passport</title>

    <!-- Google Font -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">

    <!-- Font Awesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

    <!-- Profile CSS -->

    <link rel="stylesheet"
          href="css/profile.css">

</head>

<body>

<div class="profile-container">

    <!--=========================
            SIDEBAR
    =========================-->

    <aside class="sidebar">

        <div class="logo">

            <i class="fa-solid fa-passport"></i>

            <h2>DSP</h2>

        </div>

        <ul class="menu">

            <li>

                <a href="dashboard.php">

                    <i class="fa-solid fa-chart-line"></i>

                    <span>Dashboard</span>

                </a>

            </li>

            <li class="active">

                <a href="profile.php">

                    <i class="fa-solid fa-user"></i>

                    <span>Profile</span>

                </a>

            </li>

            <li>

                <a href="skills.php">

                    <i class="fa-solid fa-star"></i>

                    <span>Skills</span>

                </a>

            </li>

            <li>

                <a href="projects.php">

                    <i class="fa-solid fa-briefcase"></i>

                    <span>Projects</span>

                </a>

            </li>

            <li>

                <a href="certificates.php">

                    <i class="fa-solid fa-scroll"></i>

                    <span>Certificates</span>

                </a>

            </li>

            <li>

                <a href="workshops.php">

                    <i class="fa-solid fa-graduation-cap"></i>

                    <span>Workshops</span>

                </a>

            </li>

            <li>

                <a href="internships.php">

                    <i class="fa-solid fa-building"></i>

                    <span>Internships</span>

                </a>

            </li>

            <li>

                <a href="achievements.php">

                    <i class="fa-solid fa-trophy"></i>

                    <span>Achievements</span>

                </a>

            </li>

            <li>

                <a href="settings.php">

                    <i class="fa-solid fa-gear"></i>

                    <span>Settings</span>

                </a>

            </li>

            <li>

                <a href="logout.php">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    <span>Logout</span>

                </a>

            </li>

        </ul>

    </aside>

    <!--=========================
          MAIN CONTENT
    =========================-->

    <main class="main-content">

        <!--=========================
              TOP HEADER
        =========================-->

        <header class="topbar">

            <div class="top-left">

                <button class="menu-toggle">

                    <i class="fa-solid fa-bars"></i>

                </button>

                <h2>My Profile</h2>

            </div>

            <div class="top-right">

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text"
                           placeholder="Search...">

                </div>

                <div class="notification">

    <i class="fa-regular fa-bell"></i>

    <?php if ($unread_notifications > 0): ?>

        <span class="notification-badge">
            <?= $unread_notifications ?>
        </span>

    <?php endif; ?>

    <div class="notification-popup">

        <h4>Notifications</h4>

        <?php if (count($notifications) > 0): ?>

            <?php foreach ($notifications as $notification): ?>

                <div class="notification-item">

                    <p>
                        <?= htmlspecialchars($notification["message"]) ?>
                    </p>

                    <small>
                        <?= htmlspecialchars($notification["created_at"]) ?>
                    </small>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <p class="no-notifications">
                No notifications
            </p>

        <?php endif; ?>

    </div>

</div>

                <div class="user-profile">

    <div class="user-avatar">

        <?php if (!empty($profile_photo)): ?>

            <img src="<?php echo htmlspecialchars($profile_photo); ?>"
                 alt="Profile">

        <?php else: ?>

            <i class="fa-solid fa-user"></i>

        <?php endif; ?>

    </div>

    <div>

        <h4><?= htmlspecialchars($user['full_name']) ?></h4>

         <p>
            <?= htmlspecialchars($user['account_type']) ?>
        </p>

    </div>

</div>

            </div>

        </header>

        <!--=========================
            PROFILE HEADER
        =========================-->

        <section class="profile-header">

            <div class="profile-banner">

                <div class="profile-image">

    <?php if (!empty($profile['profile_photo'])): ?>

        <img src="<?= htmlspecialchars($profile['profile_photo']) ?>"
             alt="Profile">

    <?php else: ?>

        <img src="images/profile.png"
             alt="Profile">

    <?php endif; ?>

</div>

                <div class="profile-info">

                    <h1><?= htmlspecialchars($user['full_name']) ?></h1>

                    <h3>B.Sc Computer Science Student</h3>

                    <p>

                        Passionate about Web Development,
                        Artificial Intelligence and Software Engineering.

                    </p>

                </div>

                <div class="profile-actions">

                    <a href="#" class="edit-btn">

                        <i class="fa-solid fa-user-pen"></i>

                        Edit Profile

                    </a>

                </div>

            </div>

        </section>
                <!--=========================
          PROFILE INFORMATION
        =========================-->

        <section class="profile-content">

            <!-- Personal Information -->

            <div class="info-card">

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-address-card"></i>

                        Personal Information

                    </h2>

                </div>

                <div class="info-grid">

                    <div class="info-item">

                        <label>Full Name</label>

                        <?= htmlspecialchars($user['full_name']) ?>

                    </div>

                    <div class="info-item">

                        <label>Email</label>

                        <?= htmlspecialchars($user['email']) ?>

                    </div>

                    <div class="info-item">

                        <label>Phone</label>

                        <?= htmlspecialchars($user['phone'] ?? 'Not provided') ?>

                    </div>

                    <div class="info-item">

                        <label>Date of Birth</label>

                        <p>
                            <?= !empty($user['dob']) ? date('d F Y', strtotime($user['dob'])) : 'Not provided' ?>
                        </p>

                    </div>

                    <div class="info-item">

                        <label>Gender</label>

                        <p><?= htmlspecialchars($user['gender'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>Address</label>

                        <p><?= htmlspecialchars($profile['address'] ?? 'Not provided') ?></p>

                    </div>

                </div>

            </div>



            <!-- Academic Information -->

            <div class="info-card">

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-graduation-cap"></i>

                        Academic Information

                    </h2>

                </div>

                <div class="info-grid">

                    <div class="info-item">

                        <label>Degree</label>

                        <p><?= htmlspecialchars($profile['education'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>College</label>

                        <p><?= htmlspecialchars($profile['college'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>University</label>

                        <p><?= htmlspecialchars($profile['university'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>Semester</label>

                        <p><?= htmlspecialchars($profile['semester'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>CGPA</label>

                        <p><?= htmlspecialchars($profile['cgpa'] ?? 'Not provided') ?></p>

                    </div>

                    <div class="info-item">

                        <label>Batch</label>

                        <p><?= htmlspecialchars($profile['batch'] ?? 'Not provided') ?></p>

                    </div>

                </div>

            </div>



            <!-- Professional Links -->

            <div class="info-card">

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-link"></i>

                        Professional Links

                    </h2>

                </div>

                <div class="links-grid">

                    <a href="<?= htmlspecialchars($profile['github'] ?? '#') ?>" class="link-card" target="_blank" rel="noopener noreferrer">

                        <i class="fa-brands fa-github"></i>

                        GitHub

                    </a>

                    <a href="<?= htmlspecialchars($profile['linkedin'] ?? '#') ?>" class="link-card" target="_blank" rel="noopener noreferrer">

                        <i class="fa-brands fa-linkedin"></i>

                        LinkedIn

                    </a>

                    <a href="<?= htmlspecialchars($profile['portfolio'] ?? '#') ?>" class="link-card" target="_blank" rel="noopener noreferrer">

                        <i class="fa-solid fa-globe"></i>

                        Portfolio

                    </a>

                    <a href="<?= htmlspecialchars($profile['resume'] ?? '#') ?>" class="link-card" target="_blank" rel="noopener noreferrer">

                        <i class="fa-solid fa-file-arrow-down"></i>

                        Resume

                    </a>

                </div>

            </div>

            <!--=========================
                PROFILE STATISTICS
            =========================-->

            <section class="profile-stats">

                <div class="stat-card skills">

                    <i class="fa-solid fa-star"></i>

                    <h2><?= $skill_count ?></h2>

                    <p>Skills</p>

                </div>

                <div class="stat-card projects">

                    <i class="fa-solid fa-briefcase"></i>

                    <h2><?= $project_count ?></h2>

                    <p>Projects</p>

                </div>

                <div class="stat-card certificates">

                    <i class="fa-solid fa-scroll"></i>

                    <h2><?= $certificate_count ?></h2>

                    <p>Certificates</p>

                </div>

                <div class="stat-card achievements">

                    <i class="fa-solid fa-trophy"></i>

                    <h2><?= $achievement_count ?></h2>

                    <p>Achievements</p>

                </div>

            </section>


            <!--=========================
                QUICK ACTIONS
            =========================-->

            <section class="quick-actions">

                <h2>

                    Quick Actions

                </h2>

                <div class="action-buttons">

                    <a href="skills.php"
                       class="action-btn add-skill">

                        <i class="fa-solid fa-star"></i>

                        Add Skill

                    </a>

                    <a href="projects.php"
                       class="action-btn add-project">

                        <i class="fa-solid fa-briefcase"></i>

                        Add Project

                    </a>

                    <a href="certificates.php"
                       class="action-btn upload-certificate">

                        <i class="fa-solid fa-scroll"></i>

                        Upload Certificate

                    </a>

                    <a href="settings.php#account"
                       class="action-btn upload-resume">

                        <i class="fa-solid fa-file-arrow-up"></i>

                        Upload Resume

                    </a>

                </div>

            </section>



        </section>



        <!--=========================
                FOOTER
        =========================-->

        <footer class="profile-footer">

            <p>

                © 2026 Digital Skill Passport |
                Student Portfolio Management System

            </p>

        </footer>

    </main>

</div>



<!--=========================
        PROFILE JAVASCRIPT
==========================-->

<script src="js/profile.js"></script>

</body>

</html>