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

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);



/*==================================================
        DELETE SKILL
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_skill"])) {

    $user_skill_id = (int) $_POST["user_skill_id"];

    $sql = "DELETE FROM user_skills
            WHERE user_skill_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $user_skill_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    header("Location: skills.php");

    exit;
}


/*==================================================
        EDIT SKILL
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["edit_skill"])) {

    $user_skill_id = (int) $_POST["user_skill_id"];

    $skill_level = trim($_POST["level"]);
    $category = trim($_POST["category"]);
    $experience = trim($_POST["experience"]);
    $description = trim($_POST["description"]);

    $sql = "UPDATE user_skills
            SET category = ?,
                skill_level = ?,
                experience = ?,
                description = ?
            WHERE user_skill_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssii",
        $category,
        $skill_level,
        $experience,
        $description,
        $user_skill_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);

    header("Location: skills.php");

    exit;
}

/*==================================================
        ADD NEW SKILL
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_skill"])) {

    $skill_name = trim($_POST["skill_name"]);
    $category = trim($_POST["category"]);
    $skill_level = trim($_POST["level"]);
    $experience = trim($_POST["experience"]);
    $description = trim($_POST["description"]);

    /*----------------------------------------------
        STEP 1: Check if skill already exists
    ----------------------------------------------*/

    $sql = "SELECT skill_id
            FROM skill_master
            WHERE skill_name = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $skill_name);

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $existing_skill = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    /*----------------------------------------------
        STEP 2: Add skill to skill_master if needed
    ----------------------------------------------*/

    if ($existing_skill) {

        $skill_id = $existing_skill["skill_id"];

    } else {

        $sql = "INSERT INTO skill_master (skill_name)
                VALUES (?)";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $skill_name);

        mysqli_stmt_execute($stmt);

        $skill_id = mysqli_insert_id($conn);

        mysqli_stmt_close($stmt);
    }


    /*----------------------------------------------
        STEP 3: Add skill for this user
    ----------------------------------------------*/

    $sql = "INSERT INTO user_skills
            (user_id, skill_id, skill_level, category, experience, description)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "iissss",
        $user_id,
        $skill_id,
        $skill_level,
        $category,
        $experience,
        $description
    );

    mysqli_stmt_execute($stmt);

    mysqli_stmt_close($stmt);


    /*----------------------------------------------
        STEP 4: Refresh the page
    ----------------------------------------------*/

    header("Location: skills.php");

    exit;
}

/*==================================================
        GET SKILL FOR EDIT
==================================================*/

$edit_skill = null;

if (isset($_GET["edit_id"])) {

    $edit_id = (int) $_GET["edit_id"];

    $sql = "SELECT 
                us.user_skill_id,
                sm.skill_name,
                us.category,
                us.skill_level,
                us.experience,
                us.description
            FROM user_skills us
            INNER JOIN skill_master sm
                ON us.skill_id = sm.skill_id
            WHERE us.user_skill_id = ?
            AND us.user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $edit_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $edit_skill = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

/*==================================================
        UPDATE SKILL
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["update_skill"])) {

    $user_skill_id = (int) $_POST["user_skill_id"];

    $skill_name = trim($_POST["skill_name"]);
    $category = trim($_POST["category"]);
    $skill_level = trim($_POST["level"]);
    $experience = trim($_POST["experience"]);
    $description = trim($_POST["description"]);


    /*----------------------------------------------
        STEP 1: Get skill_id
    ----------------------------------------------*/

    $sql = "SELECT skill_id
            FROM user_skills
            WHERE user_skill_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $user_skill_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $skill_data = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);


    if ($skill_data) {

        $skill_id = $skill_data["skill_id"];


        /*------------------------------------------
            STEP 2: Update skill_master
        ------------------------------------------*/

        $sql = "UPDATE skill_master
                SET skill_name = ?
                WHERE skill_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "si",
            $skill_name,
            $skill_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);


        /*------------------------------------------
            STEP 3: Update user_skills
        ------------------------------------------*/

        $sql = "UPDATE user_skills
                SET category = ?,
                    skill_level = ?,
                    experience = ?,
                    description = ?
                WHERE user_skill_id = ?
                AND user_id = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param(
            $stmt,
            "ssssii",
            $category,
            $skill_level,
            $experience,
            $description,
            $user_skill_id,
            $user_id
        );

        mysqli_stmt_execute($stmt);

        mysqli_stmt_close($stmt);

    }


    /*----------------------------------------------
        STEP 4: Return to skills page
    ----------------------------------------------*/

    header("Location: skills.php");

    exit;
}

/*==================================================
        GET USER INFORMATION
==================================================*/

$sql = "SELECT full_name, account_type
        FROM users 
        WHERE user_id = ?"; 
 
$stmt = mysqli_prepare($conn, $sql); 
 
mysqli_stmt_bind_param($stmt, "i", $user_id); 
 
mysqli_stmt_execute($stmt); 
 
$result = mysqli_stmt_get_result($stmt); 
 
$user = mysqli_fetch_assoc($result); 
 
mysqli_stmt_close($stmt);


/*==================================================
        GET PROFILE PHOTO
==================================================*/

$sql = "SELECT profile_photo
        FROM profile
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);

$profile_photo = $profile["profile_photo"] ?? "";


/*==================================================
        GET USER SKILLS
==================================================*/

$sql = "SELECT 
            us.user_skill_id,
            sm.skill_name,
            us.category,
            us.skill_level,
            us.experience,
            us.description
        FROM user_skills us
        INNER JOIN skill_master sm
            ON us.skill_id = sm.skill_id
        WHERE us.user_id = ?
        ORDER BY us.user_skill_id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$skills_result = mysqli_stmt_get_result($stmt);


/*==================================================
        SKILL SUMMARY
==================================================*/

$total_skills = 0;
$technical_skills = 0;
$soft_skills = 0;
$total_level = 0;


/*
    Store skills temporarily so we can use them
    in both the summary and table.
*/

$skills = [];

while ($skill = mysqli_fetch_assoc($skills_result)) {

    $skills[] = $skill;

    $total_skills++;

    if (strtolower($skill["category"]) === "soft skills") {

        $soft_skills++;

    } else {

        $technical_skills++;

    }


    /* Convert skill level into percentage */

    switch ($skill["skill_level"]) {

        case "Beginner":
            $total_level += 25;
            break;

        case "Intermediate":
            $total_level += 50;
            break;

        case "Advanced":
            $total_level += 75;
            break;

        case "Expert":
            $total_level += 100;
            break;

    }

}


mysqli_stmt_close($stmt);


/* Calculate average skill level */

$average_level = $total_skills > 0
    ? round($total_level / $total_skills)
    : 0;

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Skills | Digital Skill Passport</title>

    <!-- Google Fonts -->

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

    <!-- Skills CSS -->

    <link rel="stylesheet"
          href="css/skills.css">

</head>

<body>

<div class="skills-container">

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

            <li>

                <a href="profile.php">

                    <i class="fa-solid fa-user"></i>

                    <span>Profile</span>

                </a>

            </li>

            <li class="active">

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
              TOP BAR
        =========================-->

        <header class="topbar">

            <div class="top-left">

                <button class="menu-toggle">

                    <i class="fa-solid fa-bars"></i>

                </button>

                <h2>Skills Management</h2>

            </div>

            <div class="top-right">

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text"
                           placeholder="Search skills...">

                </div>

                <div class="notification">

    <i class="fa-solid fa-bell"></i>

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
                 alt="Profile Photo">

        <?php else: ?>

            <i class="fa-solid fa-user"></i>

        <?php endif; ?>

    </div>

    <div>

        <h4>
            <?php echo htmlspecialchars($user['full_name']); ?>
        </h4>

        <p><?= htmlspecialchars($user['account_type']) ?></p>

    </div>

</div>

            </div>

        </header>

        <!--=========================
            PAGE HEADER
        =========================-->

        <section class="page-header">

            <div class="header-text">

                <h1>

                    My Skills

                </h1>

                <p>

                    Manage your technical and professional skills,
                    update proficiency levels, and showcase your expertise.

                </p>

            </div>

            <div class="header-button">

                <a href="#addSkillForm"
                   class="add-skill-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add New Skill

                </a>

            </div>

        </section>

        <!--=========================
          SUMMARY CARDS
        =========================-->

        <section class="summary-cards">

            <div class="summary-card total">

                <i class="fa-solid fa-star"></i>

                <h2><?= $total_skills ?></h2>

                <p>Total Skills</p>

            </div>

            <div class="summary-card technical">

                <i class="fa-solid fa-laptop-code"></i>

                <h2><?= $technical_skills ?></h2>

                <p>Technical Skills</p>

            </div>

            <div class="summary-card soft">

                <i class="fa-solid fa-users"></i>

                <h2><?= $soft_skills ?></h2>

                <p>Soft Skills</p>

            </div>

            <div class="summary-card average">

                <i class="fa-solid fa-chart-column"></i>

                <h2><?= $average_level ?>%</h2>

                <p>Average Level</p>

            </div>

        </section>

                <!--=========================
            ADD SKILL FORM
        =========================-->

        <section class="skill-form-section"
                 id="addSkillForm">

            <div class="form-card">

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-plus-circle"></i>

                        <?= isset($_GET['edit_id']) ? 'Edit Skill' : 'Add New Skill' ?>

                    </h2>

                </div>

                <form action="skills.php" 
      method="POST">

      <?php if (isset($edit_skill) && $edit_skill): ?>

    <input type="hidden"
           name="user_skill_id"
           value="<?= $edit_skill['user_skill_id'] ?>">

<?php endif; ?>

                    <div class="form-grid">

                        <!-- Skill Name -->

                        <div class="form-group">

                            <label>

                                Skill Name

                            </label>

                            <input type="text" 
                                name="skill_name" 
                                placeholder="Enter Skill Name"
                                value="<?= htmlspecialchars($edit_skill['skill_name'] ?? '') ?>"
                                required>

                        </div>

                        <!-- Category -->

                        <div class="form-group">

                            <label>

                                Category

                            </label>

                            <select name="category" required>

    <option value="">Select Category</option>

    <option value="Programming"
        <?= (($edit_skill['category'] ?? '') === 'Programming') ? 'selected' : '' ?>>
        Programming
    </option>

    <option value="Frontend"
        <?= (($edit_skill['category'] ?? '') === 'Frontend') ? 'selected' : '' ?>>
        Frontend
    </option>

    <option value="Backend"
        <?= (($edit_skill['category'] ?? '') === 'Backend') ? 'selected' : '' ?>>
        Backend
    </option>

    <option value="Database"
        <?= (($edit_skill['category'] ?? '') === 'Database') ? 'selected' : '' ?>>
        Database
    </option>

    <option value="Cloud Computing"
        <?= (($edit_skill['category'] ?? '') === 'Cloud Computing') ? 'selected' : '' ?>>
        Cloud Computing
    </option>

    <option value="Artificial Intelligence"
        <?= (($edit_skill['category'] ?? '') === 'Artificial Intelligence') ? 'selected' : '' ?>>
        Artificial Intelligence
    </option>

    <option value="Machine Learning"
        <?= (($edit_skill['category'] ?? '') === 'Machine Learning') ? 'selected' : '' ?>>
        Machine Learning
    </option>

    <option value="Soft Skills"
        <?= (($edit_skill['category'] ?? '') === 'Soft Skills') ? 'selected' : '' ?>>
        Soft Skills
    </option>

    <option value="Others"
        <?= (($edit_skill['category'] ?? '') === 'Others') ? 'selected' : '' ?>>
        Others
    </option>

</select>

                        </div>

                        <!-- Skill Level -->

                        <div class="form-group">

                            <label>

                                Skill Level

                            </label>

                            <select name="level" required>

    <option value="">Select Level</option>

    <option value="Beginner"
        <?= (($edit_skill['skill_level'] ?? '') === 'Beginner') ? 'selected' : '' ?>>
        Beginner
    </option>

    <option value="Intermediate"
        <?= (($edit_skill['skill_level'] ?? '') === 'Intermediate') ? 'selected' : '' ?>>
        Intermediate
    </option>

    <option value="Advanced"
        <?= (($edit_skill['skill_level'] ?? '') === 'Advanced') ? 'selected' : '' ?>>
        Advanced
    </option>

    <option value="Expert"
        <?= (($edit_skill['skill_level'] ?? '') === 'Expert') ? 'selected' : '' ?>>
        Expert
    </option>

</select>

                        </div>

                        <!-- Experience -->

                        <div class="form-group">

                            <label>

                                Experience

                            </label>

                            <select name="experience" required>

    <option value="">Select Experience</option>

    <option value="Less than 6 Months"
        <?= (($edit_skill['experience'] ?? '') === 'Less than 6 Months') ? 'selected' : '' ?>>
        Less than 6 Months
    </option>

    <option value="6 Months - 1 Year"
        <?= (($edit_skill['experience'] ?? '') === '6 Months - 1 Year') ? 'selected' : '' ?>>
        6 Months - 1 Year
    </option>

    <option value="1 - 2 Years"
        <?= (($edit_skill['experience'] ?? '') === '1 - 2 Years') ? 'selected' : '' ?>>
        1 - 2 Years
    </option>

    <option value="2 - 3 Years"
        <?= (($edit_skill['experience'] ?? '') === '2 - 3 Years') ? 'selected' : '' ?>>
        2 - 3 Years
    </option>

    <option value="More than 3 Years"
        <?= (($edit_skill['experience'] ?? '') === 'More than 3 Years') ? 'selected' : '' ?>>
        More than 3 Years
    </option>

</select>

                        </div>

                    </div>

                    <!-- Description -->

                    <div class="form-group">

                        <label>

                            Description

                        </label>

                        <textarea 
    name="description" 
    rows="5" 
    placeholder="Write a short description about this skill..."
><?= htmlspecialchars($edit_skill['description'] ?? '') ?></textarea>

                    </div>

                    <!-- Buttons -->

                    <div class="button-group">

                        <button type="submit" 
        class="save-btn"
        name="<?= isset($edit_skill) && $edit_skill ? 'update_skill' : 'add_skill' ?>">

    <i class="fa-solid <?= isset($edit_skill) && $edit_skill ? 'fa-pen' : 'fa-floppy-disk' ?>"></i>

    <?= isset($edit_skill) && $edit_skill ? 'Update Skill' : 'Add Skill' ?>

</button>

                        <button type="reset"
                                class="reset-btn">

                            <i class="fa-solid fa-rotate-right"></i>

                            Reset

                        </button>

                    </div>

                </form>

            </div>

        </section>



        <!--=========================
            SKILLS TABLE
        =========================-->

        <section class="skills-table-section">

            <div class="table-card">

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-table"></i>

                        My Skills

                    </h2>

                </div>

                <table>

                    <thead>

                        <tr>

                            <th>Skill</th>

                            <th>Category</th>

                            <th>Level</th>

                            <th>Experience</th>

                            <th>Status</th>

                            <th>Edit</th>

                            <th>Delete</th>

                        </tr>

                    </thead>

                    <tbody>

<?php if (count($skills) > 0): ?>

    <?php foreach ($skills as $skill): ?>

        <tr>

            <td>
                <?= htmlspecialchars($skill['skill_name']) ?>
            </td>

            <td>
                <?= htmlspecialchars($skill['category']) ?>
            </td>

            <td>
                <?= htmlspecialchars($skill['skill_level']) ?>
            </td>

            <td>
                <?= htmlspecialchars($skill['experience']) ?>
            </td>

            <td>

                <span class="status active">
                    Active
                </span>

            </td>

            <td>

                <button class="edit"
                        type="button"
                        data-id="<?= $skill['user_skill_id'] ?>">

                    <i class="fa-solid fa-pen"></i>

                </button>

            </td>

            <td>

                <button class="delete"
                        type="button"
                        data-id="<?= $skill['user_skill_id'] ?>">

                    <i class="fa-solid fa-trash"></i>

                </button>

            </td>

        </tr>

    <?php endforeach; ?>

<?php else: ?>

    <tr>

        <td colspan="7"
            style="text-align:center; padding:30px;">

            No skills added yet.

        </td>

    </tr>

<?php endif; ?>

</tbody>

                </table>

            </div>

        </section>
            

        <!--=========================
                FOOTER
        =========================-->

        <footer class="skills-footer">

            <p>

                © 2026 Digital Skill Passport |
                Skills Management Module

            </p>

        </footer>

    </main>

</div>



<!--=========================
        SKILLS JAVASCRIPT
==========================-->

<script src="js/skills.js"></script>

</body>

</html>
