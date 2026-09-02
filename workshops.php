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


// ========================================
// NOTIFICATIONS
// ========================================

$notifications = [];

$sql = "SELECT message, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {
    $notifications[] = $row;
}

mysqli_stmt_close($stmt);


// Count unread notifications

$sql = "SELECT COUNT(*) AS unread_count
        FROM notifications
        WHERE user_id = ?
        AND is_read = 0";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$row = mysqli_fetch_assoc($result);

$unread_notifications = $row["unread_count"];

mysqli_stmt_close($stmt);

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


// Get profile photo
$profilePhoto = "images/profile.png";

$sql = "SELECT profile_photo FROM profile WHERE user_id = ?";
$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);
$profile = mysqli_fetch_assoc($result);

if (!empty($profile["profile_photo"])) {
    $profilePhoto = $profile["profile_photo"];
}

mysqli_stmt_close($stmt);


// Workshop summary counts

$sql_total = "SELECT COUNT(*) AS total 
              FROM workshops 
              WHERE user_id = ?";

$stmt_total = mysqli_prepare($conn, $sql_total);
mysqli_stmt_bind_param($stmt_total, "i", $user_id);
mysqli_stmt_execute($stmt_total);

$result_total = mysqli_stmt_get_result($stmt_total);
$total_workshops = mysqli_fetch_assoc($result_total)["total"];


$sql_online = "SELECT COUNT(*) AS total 
               FROM workshops 
               WHERE user_id = ? AND mode = 'online'";

$stmt_online = mysqli_prepare($conn, $sql_online);
mysqli_stmt_bind_param($stmt_online, "i", $user_id);
mysqli_stmt_execute($stmt_online);

$result_online = mysqli_stmt_get_result($stmt_online);
$online_workshops = mysqli_fetch_assoc($result_online)["total"];


$sql_offline = "SELECT COUNT(*) AS total 
                FROM workshops 
                WHERE user_id = ? AND mode = 'offline'";

$stmt_offline = mysqli_prepare($conn, $sql_offline);
mysqli_stmt_bind_param($stmt_offline, "i", $user_id);
mysqli_stmt_execute($stmt_offline);

$result_offline = mysqli_stmt_get_result($stmt_offline);
$offline_workshops = mysqli_fetch_assoc($result_offline)["total"];


$sql_completed = "SELECT COUNT(*) AS total 
                  FROM workshops 
                  WHERE user_id = ? AND status = 'completed'";

$stmt_completed = mysqli_prepare($conn, $sql_completed);
mysqli_stmt_bind_param($stmt_completed, "i", $user_id);
mysqli_stmt_execute($stmt_completed);

$result_completed = mysqli_stmt_get_result($stmt_completed);
$completed_workshops = mysqli_fetch_assoc($result_completed)["total"];


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $user_id = $_SESSION["user_id"];

    $workshop_id = !empty($_POST["workshop_id"])
        ? $_POST["workshop_id"]
        : null;

    $workshop_title = $_POST["workshop_title"];
    $category = $_POST["category"];
    $organization = $_POST["organization"];
    $trainer = $_POST["trainer"];
    $workshop_date = $_POST["workshop_date"];
    $end_date = !empty($_POST["end_date"])
        ? $_POST["end_date"]
        : null;
    $duration = $_POST["duration"];
    $status = $_POST["status"];
    $mode = $_POST["mode"];
    $location = $_POST["location"];
    $description = $_POST["description"];
    $skills_learned = $_POST["skills_learned"];


    /* ========================================
       FILE UPLOADS
    ======================================== */

    $uploadFolder = "uploads/workshops/";

    $certificate_file = null;
    $workshop_image = null;


    /* Certificate */

    if (
        isset($_FILES["certificate_file"]) &&
        $_FILES["certificate_file"]["error"] === UPLOAD_ERR_OK
    ) {

        if ($_FILES["certificate_file"]["size"] > 5 * 1024 * 1024) {
            die("Certificate must be less than 5 MB.");
        }

        $allowedCertificateTypes = [
            "image/jpeg",
            "image/png",
            "image/webp",
            "application/pdf"
        ];

        $certificateType = mime_content_type(
            $_FILES["certificate_file"]["tmp_name"]
        );

        if (!in_array($certificateType, $allowedCertificateTypes)) {
            die("Invalid certificate file type.");
        }

        $extension = pathinfo(
            $_FILES["certificate_file"]["name"],
            PATHINFO_EXTENSION
        );

        $newName =
            "certificate_" .
            uniqid() .
            "." .
            strtolower($extension);

        $certificate_file =
            $uploadFolder . $newName;

        if (!move_uploaded_file(
            $_FILES["certificate_file"]["tmp_name"],
            $certificate_file
        )) {
            die("Failed to upload certificate.");
        }
    }


    /* Workshop Image */

    if (
        isset($_FILES["workshop_image"]) &&
        $_FILES["workshop_image"]["error"] === UPLOAD_ERR_OK
    ) {

        if ($_FILES["workshop_image"]["size"] > 5 * 1024 * 1024) {
            die("Workshop image must be less than 5 MB.");
        }

        $allowedImageTypes = [
            "image/jpeg",
            "image/png",
            "image/webp"
        ];

        $imageType = mime_content_type(
            $_FILES["workshop_image"]["tmp_name"]
        );

        if (!in_array($imageType, $allowedImageTypes)) {
            die("Invalid workshop image type.");
        }

        $extension = pathinfo(
            $_FILES["workshop_image"]["name"],
            PATHINFO_EXTENSION
        );

        $newName =
            "workshop_" .
            uniqid() .
            "." .
            strtolower($extension);

        $workshop_image =
            $uploadFolder . $newName;

        if (!move_uploaded_file(
            $_FILES["workshop_image"]["tmp_name"],
            $workshop_image
        )) {
            die("Failed to upload workshop image.");
        }
    }


    /* ========================================
       UPDATE EXISTING WORKSHOP
    ======================================== */

    if ($workshop_id) {

        $sql = "UPDATE workshops SET
                workshop_title = ?,
                category = ?,
                organization = ?,
                trainer = ?,
                workshop_date = ?,
                end_date = ?,
                duration = ?,
                status = ?,
                mode = ?,
                location = ?,
                description = ?,
                skills_learned = ?,
                certificate_file = COALESCE(?, certificate_file),
                workshop_image = COALESCE(?, workshop_image)
                WHERE workshop_id = ?
                AND user_id = ?";


        $stmt = mysqli_prepare($conn, $sql);


        mysqli_stmt_bind_param(
            $stmt,
            "ssssssssssssssii",
            $workshop_title,
            $category,
            $organization,
            $trainer,
            $workshop_date,
            $end_date,
            $duration,
            $status,
            $mode,
            $location,
            $description,
            $skills_learned,
            $certificate_file,
            $workshop_image,
            $workshop_id,
            $user_id
        );


        $successMessage = "Workshop updated successfully!";

    }


    /* ========================================
       ADD NEW WORKSHOP
    ======================================== */

    else {

        $sql = "INSERT INTO workshops
                (
                    user_id,
                    workshop_title,
                    category,
                    organization,
                    trainer,
                    workshop_date,
                    end_date,
                    duration,
                    status,
                    mode,
                    location,
                    description,
                    skills_learned,
                    certificate_file,
                    workshop_image
                )
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


        $stmt = mysqli_prepare($conn, $sql);


        mysqli_stmt_bind_param(
            $stmt,
            "issssssssssssss",
            $user_id,
            $workshop_title,
            $category,
            $organization,
            $trainer,
            $workshop_date,
            $end_date,
            $duration,
            $status,
            $mode,
            $location,
            $description,
            $skills_learned,
            $certificate_file,
            $workshop_image
        );


        $successMessage = "Workshop saved successfully!";

    }


    /* ========================================
       EXECUTE
    ======================================== */

    if (mysqli_stmt_execute($stmt)) {

        echo "<script>
                alert('" . $successMessage . "');
                window.location.href = 'workshops.php';
              </script>";

        exit;

    } else {

        echo "Error saving workshop: " .
             mysqli_error($conn);

    }


    mysqli_stmt_close($stmt);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Workshops | Digital Skill Passport</title>


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
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- Workshops CSS -->

    <link rel="stylesheet"
          href="css/workshops.css">

</head>


<body>


<div class="workshops-container">


    <!--========================================
                    SIDEBAR
    =========================================-->

    <aside class="sidebar">


        <!-- Logo -->

        <div class="logo">

            <i class="fa-solid fa-id-card"></i>

            <h2>SkillPass</h2>

        </div>


        <!-- Navigation -->

        <ul class="menu">


            <li>

                <a href="dashboard.php">

                    <i class="fa-solid fa-chart-pie"></i>

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


            <li>

                <a href="projects.php">

                    <i class="fa-solid fa-briefcase"></i>

                    <span>Projects</span>

                </a>

            </li>


            <li>

                <a href="certificates.php">

                    <i class="fa-solid fa-file-certificate"></i>

                    <span>Certificates</span>

                </a>

            </li>


            <!-- Active -->

            <li class="active">

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



    <!--========================================
                  MAIN CONTENT
    =========================================-->

    <main class="main-content">


        <!--========================================
                     TOP BAR
        =========================================-->

        <header class="topbar">


            <div class="top-left">


                <button class="menu-toggle"
                        type="button">

                    <i class="fa-solid fa-bars"></i>

                </button>


                <h2>Workshops</h2>


            </div>



            <div class="top-right">


                <!-- Search -->

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="search"
                           id="workshopSearch"
                           placeholder="Search workshops...">

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

    <img src="<?php echo htmlspecialchars($profilePhoto); ?>"
         alt="Profile">

    <div>

    <h4>
            <?= htmlspecialchars($user['full_name']) ?>
        </h4>

        <p><?= htmlspecialchars($user['account_type']) ?></p>


    </div>

</div>
                

                    

        </header>



        <!--========================================
                    PAGE HEADER
        =========================================-->

        <section class="page-header">


            <div class="header-text">


                <div class="header-icon">

                    <i class="fa-solid fa-graduation-cap"></i>

                </div>


                <div>

                    <h1>My Workshops</h1>

                    <p>

                        Keep track of workshops, training
                        programs and learning sessions that
                        helped you develop new skills.

                    </p>

                </div>


            </div>



            <div class="header-button">

                <a href="#addWorkshopForm"
                   class="add-workshop-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add Workshop

                </a>

            </div>


        </section>



        <!--========================================
                  WORKSHOP SUMMARY CARDS
        =========================================-->

        <section class="summary-cards">


            <!-- Total -->

            <div class="summary-card total-workshops">

                <div class="summary-icon">

                    <i class="fa-solid fa-graduation-cap"></i>

                </div>

                <div>

                    <h2><?php echo $total_workshops; ?></h2>
<p>Total Workshops</p>

                </div>

            </div>



            <!-- Online -->

            <div class="summary-card online-workshops">

                <div class="summary-icon">

                    <i class="fa-solid fa-laptop"></i>

                </div>

                <div>

                    <h2><?php echo $online_workshops; ?></h2>
<p>Online Workshops</p>

                </div>

            </div>



            <!-- Offline -->

            <div class="summary-card offline-workshops">

                <div class="summary-icon">

                    <i class="fa-solid fa-location-dot"></i>

                </div>

                <div>

                    <h2><?php echo $offline_workshops; ?></h2>
<p>Offline Workshops</p>

                </div>

            </div>



            <!-- Completed -->

            <div class="summary-card completed-workshops">

                <div class="summary-icon">

                    <i class="fa-solid fa-circle-check"></i>

                </div>

                <div>

                    <h2><?php echo $completed_workshops; ?></h2>
<p>Completed</p>

                </div>

            </div>


        </section>



        <!--========================================
                 ADD WORKSHOP FORM
        =========================================-->

        <section class="workshop-form-section"
                 id="addWorkshopForm">


            <div class="form-card">


                <div class="card-header">


                    <div class="card-header-icon">

                        <i class="fa-solid fa-calendar-plus"></i>

                    </div>


                    <div>

                        <h2 id="workshopFormTitle">Add New Workshop</h2>

                        <p>

                            Add details about a workshop or
                            training program you attended.

                        </p>

                    </div>


                </div>


                <!-- Form starts in Part 1B -->

                <form action="workshops.php" 
      method="POST" 
      enctype="multipart/form-data">

      <input type="hidden"
       name="workshop_id"
       id="workshopId">


                <!--========================================
                         WORKSHOP INFORMATION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        Workshop Information

                    </div>


                    <div class="form-grid">


                        <!-- Workshop Title -->

                        <div class="form-group">

                            <label for="workshopTitle">

                                Workshop Title

                                <span>*</span>

                            </label>

                            <input type="text"
                                   id="workshopTitle"
                                   name="workshop_title"
                                   placeholder="e.g. Artificial Intelligence Workshop"
                                   required>

                        </div>



                        <!-- Workshop Category -->

                        <div class="form-group">

                            <label for="workshopCategory">

                                Workshop Category

                                <span>*</span>

                            </label>

                            <select id="workshopCategory"
                                    name="category"
                                    required>

                                <option value="">

                                    Select Category

                                </option>

                                <option value="artificial-intelligence">

                                    Artificial Intelligence

                                </option>

                                <option value="data-science">

                                    Data Science

                                </option>

                                <option value="programming">

                                    Programming

                                </option>

                                <option value="web-development">

                                    Web Development

                                </option>

                                <option value="cyber-security">

                                    Cyber Security

                                </option>

                                <option value="cloud-computing">

                                    Cloud Computing

                                </option>

                                <option value="other">

                                    Other

                                </option>

                            </select>

                        </div>



                        <!-- Organization -->

                        <div class="form-group">

                            <label for="workshopOrganization">

                                Organizing Organization

                                <span>*</span>

                            </label>

                            <input type="text"
                                   id="workshopOrganization"
                                   name="organization"
                                   placeholder="e.g. Google, IEEE, College"
                                   required>

                        </div>



                        <!-- Trainer -->

                        <div class="form-group">

                            <label for="trainerName">

                                Trainer / Instructor

                            </label>

                            <input type="text"
                                   id="trainerName"
                                   name="trainer"
                                   placeholder="Enter trainer or instructor name">

                        </div>


                    </div>



                    <!--========================================
                         DATE AND DURATION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-calendar-days"></i>

                        Date & Duration

                    </div>


                    <div class="form-grid">


                        <!-- Workshop Date -->

                        <div class="form-group">

                            <label for="workshopDate">

                                Workshop Date

                                <span>*</span>

                            </label>

                            <input type="date"
                                   id="workshopDate"
                                   name="workshop_date"
                                   required>

                        </div>



                        <!-- End Date -->

                        <div class="form-group">

                            <label for="workshopEndDate">

                                End Date

                            </label>

                            <input type="date"
                                   id="workshopEndDate"
                                   name="end_date">

                            <small class="field-note">

                                For a multi-day workshop.

                            </small>

                        </div>



                        <!-- Duration -->

                        <div class="form-group">

                            <label for="workshopDuration">

                                Duration

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-regular fa-clock"></i>

                                <input type="text"
                                       id="workshopDuration"
                                       name="duration"
                                       placeholder="e.g. 2 Days / 6 Hours">

                            </div>

                        </div>



                        <!-- Workshop Status -->

                        <div class="form-group">

                            <label for="workshopStatus">

                                Status

                                <span>*</span>

                            </label>

                            <select id="workshopStatus"
                                    name="status"
                                    required>

                                <option value="">

                                    Select Status

                                </option>

                                <option value="completed">

                                    Completed

                                </option>

                                <option value="ongoing">

                                    Ongoing

                                </option>

                                <option value="upcoming">

                                    Upcoming

                                </option>

                            </select>

                        </div>


                    </div>



                    <!--========================================
                         WORKSHOP MODE & LOCATION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-location-dot"></i>

                        Workshop Mode & Location

                    </div>


                    <div class="form-grid">


                        <!-- Workshop Mode -->

                        <div class="form-group">

                            <label for="workshopMode">

                                Workshop Mode

                                <span>*</span>

                            </label>

                            <select id="workshopMode"
                                    name="mode"
                                    required>

                                <option value="">

                                    Select Mode

                                </option>

                                <option value="online">

                                    Online

                                </option>

                                <option value="offline">

                                    Offline

                                </option>

                                <option value="hybrid">

                                    Hybrid

                                </option>

                            </select>

                        </div>



                        <!-- Location -->

                        <div class="form-group">

                            <label for="workshopLocation">

                                Location

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-solid fa-location-dot"></i>

                                <input type="text"
                                       id="workshopLocation"
                                       name="location"
                                       placeholder="e.g. Kochi / Online">

                            </div>

                        </div>

                    </div>



                    <!--========================================
                         WORKSHOP DESCRIPTION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        Workshop Description

                    </div>


                    <div class="form-group">

                        <label for="workshopDescription">

                            Description

                        </label>

                        <textarea id="workshopDescription"
                                  name="description"
                                  rows="5"
                                  placeholder="Write a short description about the workshop, topics covered and your experience..."></textarea>

                    </div>



                    <!--========================================
                         SKILLS LEARNED
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-lightbulb"></i>

                        Skills Learned

                    </div>


                    <div class="form-group">

                        <label for="skillsLearned">

                            Skills / Technologies Learned

                        </label>

                        <input type="text"
                               id="skillsLearned"
                               name="skills_learned"
                               placeholder="e.g. Python, Machine Learning, Data Analysis">

                        <small class="field-note">

                            Separate multiple skills using commas.

                        </small>

                    </div>



                    <!--========================================
                         CERTIFICATE UPLOAD
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                        Workshop Certificate

                    </div>


                    <div class="workshop-upload-area">


                        <div class="upload-icon">

                            <i class="fa-solid fa-file-circle-plus"></i>

                        </div>


                        <h3>

                            Upload Workshop Certificate

                        </h3>


                        <p>

                            Add the certificate you received
                            after completing this workshop.

                        </p>


                        <label for="workshopCertificate"
                               class="upload-btn">

                            <i class="fa-solid fa-upload"></i>

                            Choose Certificate

                        </label>


                        <input type="file"
                               id="workshopCertificate"
                               name="certificate_file"
                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                               hidden>


                        <p class="file-info">

                            Supported formats:
                            JPG, JPEG, PNG, WEBP and PDF

                            <br>

                            Maximum file size: 5 MB

                        </p>


                        <!-- Preview -->

                        <div class="workshop-preview"
                             id="workshopPreview">

                        </div>


                    </div>



                    <!--========================================
                         WORKSHOP IMAGE
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-image"></i>

                        Workshop Image

                    </div>


                    <div class="form-group">

                        <label for="workshopImage">

                            Workshop / Event Image

                        </label>

                        <input type="file"
                               id="workshopImage"
                               name="workshop_image"
                               accept=".jpg,.jpeg,.png,.webp">

                        <small class="field-note">

                            Optional. Add an image related to
                            the workshop or event.

                        </small>

                    </div>



                    <!--========================================
                         FORM BUTTONS
                    =========================================-->

                    <div class="button-group">


                        <button type="submit"
        class="save-workshop-btn"
        id="saveWorkshopBtn">

    <i class="fa-solid fa-floppy-disk"></i>

    <span id="saveWorkshopText">Save Workshop</span>

</button>



                        <button type="reset"
                                class="reset-workshop-btn">

                            <i class="fa-solid fa-rotate-right"></i>

                            Reset

                        </button>


                    </div>


                </form>

            </div>

        </section>
                            <!--========================================
                       SAVED WORKSHOPS SECTION
                    =========================================-->

                    <section class="saved-workshops-section">


                        <!-- Section Header -->

                        <div class="section-heading">


                            <div>

                                <span class="section-label">

                                    MY LEARNING

                                </span>

                                <h2>Saved Workshops</h2>

                                <p>

                                    View and manage the workshops
                                    you have attended.

                                </p>

                            </div>


                            <!-- Filter -->

                            <div class="workshop-filter">

                                <label for="workshopFilter">

                                    <i class="fa-solid fa-filter"></i>

                                    Filter

                                </label>


                                <select id="workshopFilter">

                                    <option value="all">

                                        All Workshops

                                    </option>

                                    <option value="online">

                                        Online

                                    </option>

                                    <option value="offline">

                                        Offline

                                    </option>

                                    <option value="hybrid">

                                        Hybrid

                                    </option>

                                    <option value="completed">

                                        Completed

                                    </option>

                                    <option value="ongoing">

                                        Ongoing

                                    </option>

                                    <option value="upcoming">

                                        Upcoming

                                    </option>

                                </select>

                            </div>


                        </div>



                        <!--====================================
                             WORKSHOP CARDS
                        =====================================-->

                        <div class="workshops-grid"
                             id="workshopsGrid">


                             <?php

$sql = "SELECT * FROM workshops 
        WHERE user_id = ? 
        ORDER BY workshop_date DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

?>

              <?php if (mysqli_num_rows($result) > 0): ?>

    <?php while ($workshop = mysqli_fetch_assoc($result)): ?>

                 <article class="workshop-card"
    data-id="<?= $workshop['workshop_id'] ?>"             
    data-mode="<?php echo htmlspecialchars($workshop['mode']); ?>"
    data-status="<?php echo htmlspecialchars($workshop['status']); ?>"
    data-category="<?php echo htmlspecialchars($workshop['category']); ?>"
    data-title="<?php echo htmlspecialchars($workshop['workshop_title']); ?>"
    data-organization="<?php echo htmlspecialchars($workshop['organization'] ?? ''); ?>"
    data-trainer="<?php echo htmlspecialchars($workshop['trainer'] ?? ''); ?>"
    data-date="<?php echo htmlspecialchars($workshop['workshop_date'] ?? ''); ?>"
    data-end-date="<?php echo htmlspecialchars($workshop['end_date'] ?? ''); ?>"
    data-duration="<?php echo htmlspecialchars($workshop['duration'] ?? ''); ?>"
    data-location="<?php echo htmlspecialchars($workshop['location'] ?? ''); ?>"
    data-description="<?php echo htmlspecialchars($workshop['description'] ?? ''); ?>"
    data-skills="<?php echo htmlspecialchars($workshop['skills_learned'] ?? ''); ?>"
    data-certificate="<?php echo htmlspecialchars($workshop['certificate_file'] ?? ''); ?>"
    data-image="<?php echo htmlspecialchars($workshop['workshop_image'] ?? ''); ?>">

            <div class="workshop-card-image">

                <?php if (!empty($workshop["workshop_image"])): ?>

    <img src="<?php echo htmlspecialchars($workshop["workshop_image"]); ?>"
         alt="Workshop Image"
         class="workshop-card-img">

<?php else: ?>

    <div class="workshop-image-placeholder">
        <i class="fa-solid fa-graduation-cap"></i>
    </div>

<?php endif; ?>

                <span class="workshop-status <?php echo htmlspecialchars($workshop['status']); ?>">

                    <i class="fa-solid fa-circle-check"></i>

                    <?php echo htmlspecialchars(ucfirst($workshop['status'])); ?>

                </span>

                <span class="workshop-mode">

                    <i class="fa-solid fa-laptop"></i>

                    <?php echo htmlspecialchars(ucfirst($workshop['mode'])); ?>

                </span>

            </div>


            <div class="workshop-card-content">

                <span class="workshop-category">

                    <?php echo htmlspecialchars($workshop['category']); ?>

                </span>


                <h3>

                    <?php echo htmlspecialchars($workshop['workshop_title']); ?>

                </h3>


                <p class="organization">

                    <i class="fa-solid fa-building"></i>

                    <?php echo htmlspecialchars($workshop['organization']); ?>

                </p>


                <div class="workshop-details">

                    <div>

                        <i class="fa-regular fa-calendar"></i>

                        <span>

                            <?php
                            echo date(
                                "d F Y",
                                strtotime($workshop['workshop_date'])
                            );
                            ?>

                        </span>

                    </div>


                    <div>

                        <i class="fa-regular fa-clock"></i>

                        <span>

                            <?php echo htmlspecialchars($workshop['duration']); ?>

                        </span>

                    </div>

                </div>


                <div class="skill-tags">

                    <?php

                    $skills = explode(",", $workshop['skills_learned']);

                    foreach ($skills as $skill):

                        $skill = trim($skill);

                        if ($skill != ""):

                    ?>

                        <span>
                            <?php echo htmlspecialchars($skill); ?>
                        </span>

                    <?php
                        endif;
                    endforeach;
                    ?>

                </div>


                <div class="workshop-actions">

                    <button type="button"
                            class="view-workshop"
                            title="View Workshop">

                        <i class="fa-solid fa-eye"></i>

                        View

                    </button>


                    <button type="button"
        class="edit-workshop"
        title="Edit Workshop"
        data-id="<?php echo $workshop['workshop_id']; ?>">

    <i class="fa-solid fa-pen"></i>

</button>


                    <button type="button"
                            class="delete-workshop"
                            title="Delete Workshop">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </div>

            </div>

        </article>

    <?php endwhile; ?>

<?php endif; ?>          


                        </div>



                        <!--====================================
                             NO RESULTS MESSAGE
                        =====================================-->

                        <div class="no-workshops"
                             id="noWorkshops"
                             style="display: none;">


                            <div class="empty-icon">

                                <i class="fa-solid fa-graduation-cap"></i>

                            </div>


                            <h3>No Workshops Found</h3>


                            <p>

                                No workshops match your current
                                search or filter.

                            </p>


                        </div>


                    </section>

                    
            <!--========================================
                    WORKSHOP VIEW MODAL
            =========================================-->

            <div class="workshop-modal"
                 id="workshopModal">


                <div class="modal-overlay"></div>


                <div class="modal-content">


                    <button type="button"
                            class="close-modal"
                            id="closeWorkshopModal">

                        <i class="fa-solid fa-xmark"></i>

                    </button>


                    <div id="modalWorkshopContent">

                        <!-- JavaScript will insert
                             workshop details here -->

                    </div>


                </div>

            </div>



            <!--========================================
                        FOOTER
            =========================================-->

            <footer class="footer">


                <p>

                    © 2026 Digital Skill Passport.
                    All rights reserved.

                </p>


                <p>

                    Build your skills. Showcase your journey.

                </p>


            </footer>


        </div>



    <!-- Workshops JavaScript -->

    <script src="js/workshops.js"></script>


</body>

</html>

