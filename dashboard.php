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

require_once "db.php";


$user_id = $_SESSION["user_id"];

$sql = "SELECT * FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

$full_name = $user["full_name"];
// Count user's skills
$sql = "SELECT COUNT(*) AS total FROM user_skills WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$skill_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);


// Count user's projects
$sql = "SELECT COUNT(*) AS total FROM projects WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$project_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);


// Count user's certificates
$sql = "SELECT COUNT(*) AS total FROM certificates WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$certificate_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);


// Count user's achievements
$sql = "SELECT COUNT(*) AS total FROM achievements WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$achievement_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);


// Count user's workshops
$sql = "SELECT COUNT(*) AS total FROM workshops WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$workshop_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);


// Count user's internships
$sql = "SELECT COUNT(*) AS total FROM internships WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$internship_count = mysqli_fetch_assoc($result)["total"];
mysqli_stmt_close($stmt);

// Get user's profile photo
$sql = "SELECT profile_photo, education FROM profile WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);

$education = $profile['education'] ?? "Not added";
// Calculate profile completion
$completed_fields = 0;
$total_fields = 6;

if (!empty($profile['profile_photo'])) {
    $completed_fields++;
}

if (!empty($profile['address'])) {
    $completed_fields++;
}

if (!empty($profile['about_me'])) {
    $completed_fields++;
}

if (!empty($profile['linkedin'])) {
    $completed_fields++;
}

if (!empty($profile['github'])) {
    $completed_fields++;
}

if (!empty($profile['education'])) {
    $completed_fields++;
}

$profile_completion = round(($completed_fields / $total_fields) * 100);

mysqli_stmt_close($stmt);

// Use default photo if no profile photo is available
$profile_photo = !empty($profile['profile_photo'])
    ? $profile['profile_photo']
    : 'images/profile.png';

// Get recent activities
$activities = [];


// 1. Skills
$sql = "SELECT sm.skill_name, us.created_at
        FROM user_skills us
        JOIN skill_master sm ON us.skill_id = sm.skill_id
        WHERE us.user_id = ?
        ORDER BY us.created_at DESC
        LIMIT 10";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $activities[] = [
        "type" => "skill",
        "text" => "Added " . $row["skill_name"] . " Skill",
        "date" => $row["created_at"]
    ];
}

mysqli_stmt_close($stmt);
// 2. Certificates
$sql = "SELECT certificate_name, issue_date
        FROM certificates
        WHERE user_id = ?
        ORDER BY issue_date DESC
        LIMIT 10";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $activities[] = [
        "type" => "certificate",
        "text" => "Added " . $row["certificate_name"] . " Certificate",
        "date" => $row["issue_date"]
    ];
}

mysqli_stmt_close($stmt);
// ==============================
// GET RECENT ACTIVITIES
// ==============================

$sql = "
    SELECT 
        'skill' AS activity_type,
        CONCAT('Added a new ', sm.skill_name, ' Skill') AS activity_text,
        us.created_at AS activity_date
    FROM user_skills us
    JOIN skill_master sm ON us.skill_id = sm.skill_id
    WHERE us.user_id = ?

    UNION ALL

    SELECT
        'certificate' AS activity_type,
        CONCAT('Uploaded ', certificate_name, ' Certificate') AS activity_text,
        issue_date AS activity_date
    FROM certificates
    WHERE user_id = ?

    UNION ALL

    SELECT
        'project' AS activity_type,
        CONCAT('Added ', project_title, ' Project') AS activity_text,
        end_date AS activity_date
    FROM projects
    WHERE user_id = ?

    UNION ALL

    SELECT
        'achievement' AS activity_type,
        CONCAT('Added ', achievement_title, ' Achievement') AS activity_text,
        achievement_date AS activity_date
    FROM achievements
    WHERE user_id = ?

    UNION ALL

    SELECT
        'workshop' AS activity_type,
        CONCAT('Added ', workshop_title, ' Workshop') AS activity_text,
        workshop_date AS activity_date
    FROM workshops
    WHERE user_id = ?

    UNION ALL

    SELECT
        'internship' AS activity_type,
        CONCAT('Added ', company_name, ' Internship') AS activity_text,
        start_date AS activity_date
    FROM internships
    WHERE user_id = ?

    ORDER BY activity_date DESC
    LIMIT 4
";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "iiiiii",
    $user_id,
    $user_id,
    $user_id,
    $user_id,
    $user_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$activities_result = mysqli_stmt_get_result($stmt);

$activities = [];

while ($row = mysqli_fetch_assoc($activities_result)) {
    $activities[] = $row;
}

mysqli_stmt_close($stmt);
?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Dashboard | Digital Skill Passport</title>

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

    <!-- Dashboard CSS -->

    <link rel="stylesheet"
          href="css/dashboard.css">

</head>

<body>

<div class="dashboard-container">

    <!--==========================
            SIDEBAR
    ===========================-->

    <aside class="sidebar">

        <div class="logo">

            <i class="fa-solid fa-passport"></i>

            <h2>DSP</h2>

        </div>

        <ul class="menu">

            <li class="active">

                <a href="dashboard.php">

                    <i class="fa-solid fa-chart-line"></i>

                    Dashboard

                </a>

            </li>

            <li>

                <a href="profile.php">

                    <i class="fa-solid fa-user"></i>

                    Profile

                </a>

            </li>

            <li>

                <a href="skills.php">

                    <i class="fa-solid fa-star"></i>

                    Skills

                </a>

            </li>

            <li>

                <a href="projects.php">

                    <i class="fa-solid fa-briefcase"></i>

                    Projects

                </a>

            </li>

            <li>

                <a href="certificates.php">

                    <i class="fa-solid fa-scroll"></i>

                    Certificates

                </a>

            </li>

            <li>

                <a href="workshops.php">

                    <i class="fa-solid fa-graduation-cap"></i>

                    Workshops

                </a>

            </li>

            <li>

                <a href="internships.php">

                    <i class="fa-solid fa-building"></i>

                    Internships

                </a>

            </li>

            <li>

                <a href="achievements.php">

                    <i class="fa-solid fa-trophy"></i>

                    Achievements

                </a>

            </li>

            <li>

                <a href="settings.php">

                    <i class="fa-solid fa-gear"></i>

                    Settings

                </a>

            </li>

            <li>

                <a href="logout.php">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    Logout

                </a>

            </li>

        </ul>

    </aside>

    <!--==========================
            MAIN CONTENT
    ===========================-->

    <main class="main-content">

        <!--==========================
                TOP HEADER
        ===========================-->

        <header class="topbar">

            <div class="top-left">

                <button class="menu-toggle">

                    <i class="fa-solid fa-bars"></i>

                </button>

                <h2>

                    Dashboard

                </h2>

            </div>

            <div class="top-right">

                <div class="notification">

                    <i class="fa-regular fa-bell"></i>

                    <span>3</span>

                </div>

                <div class="user-profile">

                    <img src="<?= htmlspecialchars($profile_photo) ?>"
                         alt="Profile">

                    <div>

                        <h4><?php echo htmlspecialchars($full_name); ?></h4>

                        <p>
            <?= htmlspecialchars($user['account_type']) ?>
        </p>

                    </div>

                </div>

            </div>

        </header>
                <!--==========================
              WELCOME SECTION
        ===========================-->

        <section class="welcome-section">

            <div class="welcome-text">
                <h1>Welcome, <?php echo htmlspecialchars($full_name); ?>!</h1>
                <p>

                    Manage your Digital Skill Passport,
                    update your profile, showcase your
                    skills, projects and achievements
                    all in one place.

                </p>

            </div>

            <div class="welcome-button">

                <a href="profile.php" class="edit-btn">

                    <i class="fa-solid fa-user-pen"></i>

                    Edit Profile

                </a>

            </div>

        </section>



        <!--==========================
             DASHBOARD CARDS
        ===========================-->

        <section class="dashboard-cards">


            <!-- Skills -->

            <div class="card skills-card">

                <div class="card-icon">

                    <i class="fa-solid fa-star"></i>

                </div>

                <div class="card-details">

                    <h4>Skills</h4>

                    <h2><?= $skill_count ?></h2>

                    <p>Skills Added</p>

                </div>

            </div>



            <!-- Projects -->

            <div class="card projects-card">

                <div class="card-icon">

                    <i class="fa-solid fa-briefcase"></i>

                </div>

                <div class="card-details">

                    <h4>Projects</h4>

                    <h2><?= $project_count ?></h2>

                    <p>Completed Projects</p>

                </div>

            </div>



            <!-- Certificates -->

            <div class="card certificates-card">

                <div class="card-icon">

                    <i class="fa-solid fa-scroll"></i>

                </div>

                <div class="card-details">

                    <h4>Certificates</h4>
                    <h2><?= $certificate_count ?></h2>

                    <p>Certificates Earned</p>

                </div>

            </div>



            <!-- Achievements -->

            <div class="card achievements-card">

                <div class="card-icon">

                    <i class="fa-solid fa-trophy"></i>

                </div>

                <div class="card-details">

                    <h4>Achievements</h4>

                    <h2><?= $achievement_count ?></h2>

                    <p>Achievements Added</p>

                </div>

            </div>

        </section>



        <!--==========================
           DASHBOARD CONTENT
        ===========================-->

        <section class="dashboard-content">
                        <!--==========================
                RECENT ACTIVITIES
            ===========================-->

            <div class="activity-panel">

                <div class="panel-header">

                    <h3>Recent Activities</h3>

                </div>

                <ul class="activity-list">
                    <?php if (count($activities) > 0): ?>

                    <?php foreach ($activities as $activity): ?>

                    <?php
                    $days_ago = floor(
                    (time() - strtotime($activity['activity_date'])) / 86400
                    );

                    if ($days_ago == 0) {
                    $time_text = "Today";
                    } elseif ($days_ago == 1) {
                    $time_text = "Yesterday";
                    } elseif ($days_ago == 2) {
                    $time_text = "2 Days Ago";
                    } elseif ($days_ago < 7) {
                    $time_text = $days_ago . " Days Ago";
                    } else {
                    $time_text = date("d M Y", strtotime($activity['activity_date']));
                    }

                    if ($activity['activity_type'] == 'skill') {
                    $icon = 'fa-circle-check';
                    } elseif ($activity['activity_type'] == 'certificate') {
                    $icon = 'fa-certificate';
                    } elseif ($activity['activity_type'] == 'project') {
                    $icon = 'fa-briefcase';
                    } elseif ($activity['activity_type'] == 'achievement') {
                    $icon = 'fa-trophy';
                    } elseif ($activity['activity_type'] == 'workshop') {
                    $icon = 'fa-graduation-cap';
                    } elseif ($activity['activity_type'] == 'internship') {
                    $icon = 'fa-building';
                }
        ?>

        <li>

            <i class="fa-solid <?= $icon ?>"></i>

            <?= htmlspecialchars($activity['activity_text']) ?>

            <span><?= $time_text ?></span>

        </li>

    <?php endforeach; ?>

<?php else: ?>

    <li>
        <i class="fa-solid fa-circle-info"></i>
        No recent activities
        <span>—</span>
    </li>

<?php endif; ?>

    

                </ul>

            </div>



            <!--==========================
                PROFILE SUMMARY
            ===========================-->

            <div class="profile-summary">

                <div class="panel-header">

                    <h3>Profile Summary</h3>

                </div>

                <div class="profile-card">

                    <img src="<?= htmlspecialchars($profile_photo) ?>"
                         alt="Profile">

                    <h3><?= htmlspecialchars($full_name) ?></h3>

                    <p>B.Sc Computer Science Student</p>

                    <div class="progress-group">

                        <label>Profile Completion</label>

                        <div class="progress-bar">

                            <div class="progress-fill profile-progress"
                                 style="width: <?= $profile_completion ?>%;">

                                <?= $profile_completion ?>%

                            </div>

                        </div>

                    </div>

        </section>



        <!--==========================
             QUICK ACTIONS
        ===========================-->

        <section class="quick-actions">

            <h2>Quick Actions</h2>

            <div class="action-buttons">

                <a href="skills.php" class="action-btn">

                    <i class="fa-solid fa-star"></i>

                    Add Skill

                </a>

                <a href="projects.php" class="action-btn">

                    <i class="fa-solid fa-briefcase"></i>

                    Add Project

                </a>

                <a href="certificates.php" class="action-btn">

                    <i class="fa-solid fa-scroll"></i>

                    Upload Certificate

                </a>

                <a href="achievements.php" class="action-btn">

                    <i class="fa-solid fa-trophy"></i>

                    Add Achievement

                </a>

            </div>

        </section>



        <!--==========================
                FOOTER
        ===========================-->

        <footer class="dashboard-footer">

            <p>

                © 2026 Digital Skill Passport |
                Designed for Student Portfolio Management

            </p>

        </footer>

    </main>

</div>

<!-- Dashboard JavaScript -->

<script src="js/dashboard.js"></script>

</body>

</html>