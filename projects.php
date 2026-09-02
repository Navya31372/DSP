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
/*==================================================
        GET PROJECT FOR EDITING
==================================================*/

$edit_project = null;

if (isset($_GET["edit_id"])) {

    $edit_id = (int) $_GET["edit_id"];

    $sql = "SELECT *
            FROM projects
            WHERE project_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $edit_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    $edit_project = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}

/*==================================================
        SAVE NEW PROJECT
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $project_name = trim($_POST["project_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $technologies = trim($_POST["technologies"] ?? "");
    $start_date = !empty($_POST["start_date"])
        ? $_POST["start_date"]
        : null;
    $end_date = !empty($_POST["end_date"])
        ? $_POST["end_date"]
        : null;
    $description = trim($_POST["description"] ?? "");
    $github_link = trim($_POST["github_link"] ?? "");
    $live_link = trim($_POST["live_link"] ?? "");


    /*----------------------------------------------
            Basic Validation
    ----------------------------------------------*/

    if (
        $project_name === "" ||
        $category === "" ||
        $status === "" ||
        $technologies === "" ||
        $description === ""
    ) {

        die("Please fill in all required project details.");

    }


        /*----------------------------------------------
            CHECK IF THIS IS AN EDIT
    ----------------------------------------------*/

    $edit_id = !empty($_POST["edit_id"])
        ? (int) $_POST["edit_id"]
        : 0;


    /*----------------------------------------------
            PROJECT IMAGE
    ----------------------------------------------*/

    $project_image = null;

    if (
        isset($_FILES["project_image"]) &&
        $_FILES["project_image"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["project_image"];

        $allowed_types = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        if (!in_array($file["type"], $allowed_types)) {

            die("Invalid project image type.");

        }

        if ($file["size"] > 5 * 1024 * 1024) {

            die("Project image must not exceed 5 MB.");

        }

        /* Create uploads folder if it does not exist */

        $upload_directory = "uploads/projects/";

        if (!is_dir($upload_directory)) {

            mkdir($upload_directory, 0777, true);

        }

        /* Create a unique filename */

        $extension = pathinfo(
            $file["name"],
            PATHINFO_EXTENSION
        );

        $new_filename =
            "project_" .
            $user_id . "_" .
            time() . "_" .
            uniqid() .
            "." .
            $extension;

        $target_path =
            $upload_directory . $new_filename;


        /* Move uploaded image */

        if (!move_uploaded_file(
            $file["tmp_name"],
            $target_path
        )) {

            die("Failed to upload project image.");

        }

        $project_image = $target_path;

    }


        /*----------------------------------------------
            UPDATE EXISTING PROJECT
    ----------------------------------------------*/

    if ($edit_id > 0) {

        /* Keep old image if no new image is uploaded */

        $project_image_sql = "";

        if ($project_image !== null) {

            $sql = "UPDATE projects
                    SET project_title = ?,
                        category = ?,
                        status = ?,
                        technologies = ?,
                        description = ?,
                        start_date = ?,
                        end_date = ?,
                        github_link = ?,
                        live_link = ?,
                        project_image = ?
                    WHERE project_id = ?
                    AND user_id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "ssssssssssii",
                $project_name,
                $category,
                $status,
                $technologies,
                $description,
                $start_date,
                $end_date,
                $github_link,
                $live_link,
                $project_image,
                $edit_id,
                $user_id
            );

        } else {

            $sql = "UPDATE projects
                    SET project_title = ?,
                        category = ?,
                        status = ?,
                        technologies = ?,
                        description = ?,
                        start_date = ?,
                        end_date = ?,
                        github_link = ?,
                        live_link = ?
                    WHERE project_id = ?
                    AND user_id = ?";

            $stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $stmt,
                "sssssssssii",
                $project_name,
                $category,
                $status,
                $technologies,
                $description,
                $start_date,
                $end_date,
                $github_link,
                $live_link,
                $edit_id,
                $user_id
            );

        }


        if (!mysqli_stmt_execute($stmt)) {

            die(
                "Error updating project: " .
                mysqli_stmt_error($stmt)
            );

        }

        mysqli_stmt_close($stmt);


        /*------------------------------------------
                REDIRECT AFTER UPDATE
        ------------------------------------------*/

        header("Location: projects.php?project_updated=1");

        exit;

    }



    /*----------------------------------------------
            INSERT NEW PROJECT
    ----------------------------------------------*/

    $sql = "INSERT INTO projects
            (
                user_id,
                project_title,
                category,
                status,
                technologies,
                description,
                start_date,
                end_date,
                github_link,
                live_link,
                project_image
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "issssssssss",
        $user_id,
        $project_name,
        $category,
        $status,
        $technologies,
        $description,
        $start_date,
        $end_date,
        $github_link,
        $live_link,
        $project_image
    );

    if (!mysqli_stmt_execute($stmt)) {

        die(
            "Error saving project: " .
            mysqli_stmt_error($stmt)
        );

    }

    mysqli_stmt_close($stmt);


    /*------------------------------------------
            REDIRECT AFTER NEW PROJECT
    ------------------------------------------*/

    header("Location: projects.php?project_added=1");

    exit;
}

// Get logged-in user's profile information

$sql = "SELECT * FROM profile WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile = mysqli_fetch_assoc($result);

$profile_photo = $profile['profile_photo'] ?? '';

// Get logged-in user's name

$sql = "SELECT full_name, account_type FROM users WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

/*==================================================
        PROJECT SUMMARY
==================================================*/

$sql = "SELECT status, technologies
        FROM projects
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);


/*----------------------------------------------
        Initialize Summary Values
----------------------------------------------*/

$total_projects = 0;
$completed_projects = 0;
$ongoing_projects = 0;

$all_technologies = [];


/*----------------------------------------------
        Calculate Project Summary
----------------------------------------------*/

while ($project = mysqli_fetch_assoc($result)) {

    $total_projects++;


    /* Completed / Ongoing */

    if ($project["status"] === "completed") {

        $completed_projects++;

    }

    if ($project["status"] === "ongoing") {

        $ongoing_projects++;

    }


    /* Technologies */

    if (!empty($project["technologies"])) {

        $technologies =
            explode(",", $project["technologies"]);

        foreach ($technologies as $technology) {

            $technology = trim($technology);

            if ($technology !== "") {

                $all_technologies[] =
                    strtolower($technology);

            }

        }

    }

}


/*----------------------------------------------
        Count Unique Technologies
----------------------------------------------*/

$all_technologies =
    array_unique($all_technologies);

$total_technologies =
    count($all_technologies);


mysqli_stmt_close($stmt);


/*==================================================
        GET USER PROJECTS
==================================================*/

$sql = "SELECT *
        FROM projects
        WHERE user_id = ?
        ORDER BY project_id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$projects_result = mysqli_stmt_get_result($stmt);


?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Projects | Digital Skill Passport</title>


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


    <!-- Projects CSS -->

    <link rel="stylesheet"
          href="css/projects.css">

</head>


<body>


<div class="projects-container">


    <!--================================
              SIDEBAR
    =================================-->

    <aside class="sidebar">


        <!-- Logo -->

        <div class="logo">

            <i class="fa-solid fa-passport"></i>

            <h2>DSP</h2>

        </div>


        <!-- Navigation -->

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


            <li>

                <a href="skills.php">

                    <i class="fa-solid fa-star"></i>

                    <span>Skills</span>

                </a>

            </li>


            <li class="active">

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



    <!--================================
             MAIN CONTENT
    =================================-->

    <main class="main-content">

    <?php if (isset($_GET["project_added"])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Project added successfully.
    </div>

<?php elseif (isset($_GET["project_updated"])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Project updated successfully.
    </div>

<?php elseif (isset($_GET["project_deleted"])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Project deleted successfully.
    </div>

<?php endif; ?>


        <!--================================
                 TOP BAR
        =================================-->

        <header class="topbar">


            <div class="top-left">

                <button class="menu-toggle">

                    <i class="fa-solid fa-bars"></i>

                </button>

                <h2>Projects Management</h2>

            </div>


            <div class="top-right">


                <!-- Search -->

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text"
                           placeholder="Search projects...">

                </div>


                <!-- Notification -->

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


                <!-- User -->

                <div class="user-profile">

    <?php if (!empty($profile_photo)): ?>

        <img src="<?php echo htmlspecialchars($profile_photo); ?>"
             alt="Profile">

    <?php else: ?>

        <div class="user-avatar">
            <i class="fa-solid fa-user"></i>
        </div>

    <?php endif; ?>

    <div>

        <h4>
            <?= htmlspecialchars($user['full_name']) ?>
        </h4>

        <p><?= htmlspecialchars($user['account_type']) ?></p>

    </div>

</div>

        </header>



        <!--================================
                PAGE HEADER
        =================================-->

        <section class="page-header">


            <div class="header-text">

                <h1>

                    My Projects

                </h1>

                <p>

                    Showcase your academic, personal and
                    professional projects in one organized place.

                </p>

            </div>


            <div class="header-button">

                <a href="#addProjectForm"
                   class="add-project-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add New Project

                </a>

            </div>


        </section>



        <!--================================ 
      PROJECT SUMMARY CARDS 
=================================-->

<section class="summary-cards">


    <!-- Total Projects -->

    <div class="summary-card total">

        <i class="fa-solid fa-folder-open"></i>

        <h2><?= $total_projects ?></h2>

        <p>Total Projects</p>

    </div>


    <!-- Completed -->

    <div class="summary-card completed">

        <i class="fa-solid fa-circle-check"></i>

        <h2><?= $completed_projects ?></h2>

        <p>Completed</p>

    </div>


    <!-- Ongoing -->

    <div class="summary-card ongoing">

        <i class="fa-solid fa-spinner"></i>

        <h2><?= $ongoing_projects ?></h2>

        <p>Ongoing</p>

    </div>


    <!-- Technologies -->

    <div class="summary-card technologies">

        <i class="fa-solid fa-code"></i>

        <h2><?= $total_technologies ?></h2>

        <p>Technologies Used</p>

    </div>


</section>


                <!--================================
              ADD PROJECT SECTION
        =================================-->

        <section class="project-form-section"
                 id="addProjectForm">

            <div class="form-card">


                <!-- Form Header -->

                <div class="card-header">

                    <h2>

                        <i class="fa-solid fa-folder-plus"></i>
    <?= $edit_project ? 'Edit Project' : 'Add New Project' ?>

                    </h2>

                    <p>

                        Add details about your project to showcase
                        it in your Digital Skill Passport.

                    </p>

                </div>



                <!-- Project Form -->

                <form action="projects.php"
      method="POST"
      enctype="multipart/form-data">
      <?php if ($edit_project): ?>

    <input type="hidden"
           name="edit_id"
           value="<?= $edit_project['project_id'] ?>">

<?php endif; ?>


                    <!--================================
                         BASIC PROJECT INFORMATION
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        Basic Project Information

                    </div>


                    <div class="form-grid">


                        <!-- Project Name -->

                        <div class="form-group">

                            <label for="projectName">

                                Project Name

                                <span>*</span>

                            </label>

                            <input type="text"
       id="projectName"
       name="project_name"
       placeholder="Enter project name"
       value="<?= htmlspecialchars($edit_project['project_title'] ?? '') ?>"
       required>

                        </div>



                        <!-- Category -->

                        <div class="form-group">

                            <label for="projectCategory">

                                Project Category

                                <span>*</span>

                            </label>

                            <select id="projectCategory"
        name="category"
        required>

    <option value="">Select Category</option>

    <option value="academic"
        <?= (($edit_project['category'] ?? '') === 'academic') ? 'selected' : '' ?>>
        Academic Project
    </option>

    <option value="personal"
        <?= (($edit_project['category'] ?? '') === 'personal') ? 'selected' : '' ?>>
        Personal Project
    </option>

    <option value="web"
        <?= (($edit_project['category'] ?? '') === 'web') ? 'selected' : '' ?>>
        Web Development
    </option>

    <option value="mobile"
        <?= (($edit_project['category'] ?? '') === 'mobile') ? 'selected' : '' ?>>
        Mobile Application
    </option>

    <option value="ai"
        <?= (($edit_project['category'] ?? '') === 'ai') ? 'selected' : '' ?>>
        Artificial Intelligence
    </option>

    <option value="data-science"
        <?= (($edit_project['category'] ?? '') === 'data-science') ? 'selected' : '' ?>>
        Data Science
    </option>

    <option value="machine-learning"
        <?= (($edit_project['category'] ?? '') === 'machine-learning') ? 'selected' : '' ?>>
        Machine Learning
    </option>

    <option value="other"
        <?= (($edit_project['category'] ?? '') === 'other') ? 'selected' : '' ?>>
        Other
    </option>

</select>

                        </div>



                        <!-- Status -->

                        <div class="form-group">

                            <label for="projectStatus">

                                Project Status

                                <span>*</span>

                            </label>

                            <select id="projectStatus"
        name="status"
        required>

    <option value="">Select Status</option>

    <option value="completed"
        <?= (($edit_project['status'] ?? '') === 'completed') ? 'selected' : '' ?>>
        Completed
    </option>

    <option value="ongoing"
        <?= (($edit_project['status'] ?? '') === 'ongoing') ? 'selected' : '' ?>>
        Ongoing
    </option>

    <option value="planned"
        <?= (($edit_project['status'] ?? '') === 'planned') ? 'selected' : '' ?>>
        Planned
    </option>

</select>

                        </div>



                        <!-- Technologies -->

                        <div class="form-group">

                            <label for="technologies">

                                Technologies Used

                                <span>*</span>

                            </label>

                            <input type="text"
       id="technologies"
       name="technologies"
       placeholder="e.g. HTML, CSS, JavaScript, PHP"
       value="<?= htmlspecialchars($edit_project['technologies'] ?? '') ?>"
       required>

                        </div>


                    </div>



                    <!--================================
                           PROJECT DATES
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-calendar"></i>

                        Project Duration

                    </div>


                    <div class="form-grid">


                        <!-- Start Date -->

                        <div class="form-group">

                            <label for="startDate">

                                Start Date

                            </label>

                            <input type="date"
       id="startDate"
       name="start_date"
       value="<?= htmlspecialchars($edit_project['start_date'] ?? '') ?>">

                        </div>



                        <!-- End Date -->

                        <div class="form-group">

                            <label for="endDate">

                                End Date

                            </label>

                            <input type="date"
       id="endDate"
       name="end_date"
       value="<?= htmlspecialchars($edit_project['end_date'] ?? '') ?>">

                        </div>


                    </div>



                    <!--================================
                         PROJECT DESCRIPTION
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        Project Description

                    </div>


                    <div class="form-group">

                        <label for="description">

                            Description

                            <span>*</span>

                        </label>

                        <textarea id="description"
          name="description"
          rows="6"
          placeholder="Describe your project, its purpose, features and your contribution..."
          required><?= htmlspecialchars($edit_project['description'] ?? '') ?></textarea>

                    </div>



                    <!--================================
                         PROJECT LINKS
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-link"></i>

                        Project Links

                    </div>


                    <div class="form-grid">


                        <!-- GitHub -->

                        <div class="form-group">

                            <label for="githubLink">

                                GitHub Repository

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-brands fa-github"></i>

                                <input type="url"
       id="githubLink"
       name="github_link"
       placeholder="https://github.com/username/project"
       value="<?= htmlspecialchars($edit_project['github_link'] ?? '') ?>">

                            </div>

                        </div>



                        <!-- Live Demo -->

                        <div class="form-group">

                            <label for="liveLink">

                                Live Demo / Website

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-solid fa-globe"></i>

                                <input type="url"
       id="liveLink"
       name="live_link"
       placeholder="https://example.com"
       value="<?= htmlspecialchars($edit_project['live_link'] ?? '') ?>">

                            </div>

                        </div>


                    </div>



                    <!--================================
                         PROJECT IMAGE
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-image"></i>

                        Project Image

                    </div>


                    <div class="upload-area">


                        <div class="upload-icon">

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                        </div>


                        <h3>

                            Upload Project Image

                        </h3>


                        <p>

                            Add a screenshot or cover image
                            for your project.

                        </p>


                        <label for="projectImage"
                               class="upload-btn">

                            <i class="fa-solid fa-upload"></i>

                            Choose Image

                        </label>


                        <input type="file"
                               id="projectImage"
                               name="project_image"
                               accept="image/png,image/jpeg,image/jpg,image/webp"
                               hidden>


                        <p class="file-info">

                            PNG, JPG, JPEG or WEBP
                            <br>
                            Maximum size: 5 MB

                        </p>


                        <!-- Image Preview -->

                        <div class="image-preview"
                             id="imagePreview">

                        </div>


                    </div>



                    <!--================================
                          FORM BUTTONS
                    =================================-->

                    <div class="button-group">


                        <button type="submit" class="save-project-btn">

    <i class="fa-solid fa-floppy-disk"></i>

    <?= $edit_project ? 'Update Project' : 'Save Project' ?>

</button>



                        <button type="reset"
                                class="reset-project-btn">

                            <i class="fa-solid fa-rotate-right"></i>

                            Reset

                        </button>


                    </div>


                </form>

            </div>

        </section>

                <!--================================
              PROJECTS SHOWCASE
        =================================-->

        <section class="projects-showcase">

            <div class="section-heading">

                <div>

                    <h2>

                        <i class="fa-solid fa-layer-group"></i>

                        My Projects

                    </h2>

                    <p>

                        Explore the projects added to your
                        Digital Skill Passport.

                    </p>

                </div>


                <div class="project-filter">

                    <select id="projectFilter">

                        <option value="all">
                            All Projects
                        </option>

                        <option value="completed">
                            Completed
                        </option>

                        <option value="ongoing">
                            Ongoing
                        </option>

                        <option value="planned">
                            Planned
                        </option>

                    </select>

                </div>

            </div>



            <!--================================
                 PROJECT CARDS
            =================================-->

                <div class="project-grid" id="projectGrid">
                    <?php if (mysqli_num_rows($projects_result) > 0): ?>

<?php while ($project = mysqli_fetch_assoc($projects_result)): ?>

    <article class="project-card"
             data-status="<?= htmlspecialchars($project['status']) ?>">

        <!-- PROJECT IMAGE -->

        <div class="project-image">

            <?php if (!empty($project['project_image'])): ?>

                <img src="<?= htmlspecialchars($project['project_image']) ?>"
                     alt="<?= htmlspecialchars($project['project_title']) ?>">

            <?php else: ?>

                <img src="images/project-default.webp"
                     alt="Project Image">

            <?php endif; ?>


            <span class="project-status <?= htmlspecialchars($project['status']) ?>">

                <?= ucfirst(htmlspecialchars($project['status'])) ?>

            </span>

        </div>


        <!-- PROJECT CONTENT -->

        <div class="project-content">


            <!-- CATEGORY -->

            <span class="project-category">

<?php

$category_icons = [
    "academic" => "fa-graduation-cap",
    "personal" => "fa-user",
    "web" => "fa-globe",
    "mobile" => "fa-mobile-screen",
    "ai" => "fa-brain",
    "data-science" => "fa-chart-line",
    "machine-learning" => "fa-robot",
    "other" => "fa-folder"
];

$category = $project['category'];

$icon = $category_icons[$category] ?? "fa-folder";

?>

<i class="fa-solid <?= $icon ?>"></i>

<?= ucfirst(str_replace("-", " ", htmlspecialchars($category))) ?>

</span>


            <!-- PROJECT TITLE -->

            <h3>

                <?= htmlspecialchars($project['project_title']) ?>

            </h3>


            <!-- DESCRIPTION -->

            <p>

                <?= htmlspecialchars($project['description']) ?>

            </p>


            <!-- PROJECT DATES -->

<?php if (!empty($project['start_date']) || !empty($project['end_date'])): ?>

    <div class="project-dates">

        <?php if (!empty($project['start_date'])): ?>

            <span>
                <i class="fa-regular fa-calendar"></i>

                <?= date("d M Y", strtotime($project['start_date'])) ?>

            </span>

        <?php endif; ?>


        <?php if (!empty($project['end_date'])): ?>

            <span>
                <i class="fa-solid fa-arrow-right"></i>

                <?= date("d M Y", strtotime($project['end_date'])) ?>

            </span>

        <?php endif; ?>

    </div>

<?php endif; ?>


            <!-- TECHNOLOGIES -->

            <div class="technology-list">

                <?php

                $technologies =
                    explode(",", $project['technologies']);

                foreach ($technologies as $technology):

                    $technology = trim($technology);

                    if ($technology !== ""):

                ?>

                    <span>
                        <?= htmlspecialchars($technology) ?>
                    </span>

                <?php

                    endif;

                endforeach;

                ?>

            </div>


            <!-- PROJECT LINKS -->

            <div class="project-footer">


                <?php if (!empty($project['github_link'])): ?>

                    <a href="<?= htmlspecialchars($project['github_link']) ?>"
                       class="github-btn"
                       target="_blank">

                        <i class="fa-brands fa-github"></i>

                        GitHub

                    </a>

                <?php endif; ?>


                <?php if (!empty($project['live_link'])): ?>

                    <a href="<?= htmlspecialchars($project['live_link']) ?>"
                       class="demo-btn"
                       target="_blank">

                        <i class="fa-solid fa-arrow-up-right-from-square"></i>

                        Live Demo

                    </a>

                <?php endif; ?>


            </div>


            <!-- EDIT / DELETE -->

            <button class="edit-project"
        data-project-id="<?= $project['project_id'] ?>">

    <i class="fa-solid fa-pen"></i>

    Edit

</button>


                <form action="delete_project.php"
      method="POST"
      class="delete-project-form">

    <input type="hidden"
           name="project_id"
           value="<?= $project['project_id'] ?>">

    <button type="submit"
            class="delete-project">

        <i class="fa-solid fa-trash"></i>

        Delete

    </button>

</form>

            </div>


        </div>

    </article>

<?php endwhile; ?>
<?php else: ?>

    <div class="empty-project-state">

        <div class="empty-project-icon">
            <i class="fa-solid fa-folder-open"></i>
        </div>

        <h3>No Projects Added Yet</h3>

        <p>
            You haven't added any projects to your Digital Skill Passport yet.
        </p>

        <button type="button"
                class="add-first-project-btn"
                onclick="document.getElementById('addProjectForm').scrollIntoView({ behavior: 'smooth' });">

            <i class="fa-solid fa-plus"></i>
            Add Your First Project

        </button>

    </div>

<?php endif; ?>

</div>



    



        <!--================================
                  FOOTER
        =================================-->

        <footer class="projects-footer">

            <p>

                © 2026 Digital Skill Passport |
                Projects Management Module

            </p>

        </footer>


    </main>

</div>



<!--================================
          PROJECT JAVASCRIPT
=================================-->

<script src="js/projects.js"></script>


</body>

</html>