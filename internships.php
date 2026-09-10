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

// SUCCESS MESSAGE
$successMessage = "";

if (isset($_GET["success"])) {

    if ($_GET["success"] === "added") {
        $successMessage = "Internship added successfully!";
    }

    if ($_GET["success"] === "updated") {
        $successMessage = "Internship updated successfully!";
    }
    if ($_GET["success"] === "deleted") {
    $successMessage = "Internship deleted successfully!";
}
}


// DELETE INTERNSHIP
if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["delete_internship"]) &&
    $_POST["delete_internship"] === "1"
) {

    $delete_id = (int)($_POST["internship_id"] ?? 0);

    if ($delete_id <= 0) {
        die("Invalid internship ID.");
    }

    // Get the files belonging to this user's internship
    $sql = "SELECT certificate_file, supporting_document
            FROM internships
            WHERE internship_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $delete_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $internship = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$internship) {
        die("Internship record not found.");
    }

    $certificate_file =
        $internship["certificate_file"] ?? "";

    $supporting_document =
        $internship["supporting_document"] ?? "";

    // Delete database record
    $sql = "DELETE FROM internships
            WHERE internship_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $delete_id,
        $user_id
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Failed to delete internship: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);

    // Delete certificate file
if (
    !empty($certificate_file)
) {

    $certificatePath =
        __DIR__ . "/" . $certificate_file;

    if (file_exists($certificatePath)) {
        unlink($certificatePath);
    }
}


// Delete supporting document
if (
    !empty($supporting_document)
) {

    $supportingDocumentPath =
        __DIR__ . "/" . $supporting_document;

    if (file_exists($supportingDocumentPath)) {
        unlink($supportingDocumentPath);
    }
}

    header("Location: internships.php?success=deleted");
    exit;
}


// ========================================
// GET LOGGED-IN USER DETAILS
// ========================================

$sql = "SELECT full_name, account_type
        FROM users
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


// ========================================
// GET PROFILE PHOTO
// ========================================

$profilePhoto = "images/profile.jpg";

$sql = "SELECT profile_photo
        FROM profile
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile = mysqli_fetch_assoc($result);

if (!empty($profile["profile_photo"])) {

    $profilePhoto = $profile["profile_photo"];

}

mysqli_stmt_close($stmt);

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


// ========================================
// UNREAD NOTIFICATION COUNT
// ========================================

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


// ========================================
// INTERNSHIP SUMMARY COUNTS
// ========================================

$total_internships = 0;
$completed_internships = 0;
$ongoing_internships = 0;
$upcoming_internships = 0;

$sql = "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN status = 'Completed' THEN 1 ELSE 0 END) AS completed,
            SUM(CASE WHEN status = 'Ongoing' THEN 1 ELSE 0 END) AS ongoing,
            SUM(CASE WHEN status = 'Upcoming' THEN 1 ELSE 0 END) AS upcoming
        FROM internships
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$internshipCounts = mysqli_fetch_assoc($result);

$total_internships =
    (int)($internshipCounts["total"] ?? 0);

$completed_internships =
    (int)($internshipCounts["completed"] ?? 0);

$ongoing_internships =
    (int)($internshipCounts["ongoing"] ?? 0);

$upcoming_internships =
    (int)($internshipCounts["upcoming"] ?? 0);


mysqli_stmt_close($stmt);

// ========================================
// TOTAL SKILLS GAINED
// ========================================

$skills_gained_count = 0;

$sql = "SELECT skills_gained
        FROM internships
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($row = mysqli_fetch_assoc($result)) {

    if (!empty($row["skills_gained"])) {

        $skills = array_filter(
            array_map(
                "trim",
                explode(",", $row["skills_gained"])
            )
        );

        $skills_gained_count += count($skills);
    }
}

mysqli_stmt_close($stmt);


// ========================================
// USER DISPLAY VALUES
// ========================================

$full_name = $user["full_name"] ?? "User";

$account_type = $user["account_type"] ?? "Student";


// ========================================
// SAVE INTERNSHIP
// ========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

$internship_id = (int)($_POST["internship_id"] ?? 0);

    $internship_title = trim($_POST["internship_title"] ?? "");
    $internship_type = trim($_POST["internship_type"] ?? "");
    $company_name = trim($_POST["company_name"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $role = trim($_POST["role"] ?? "");
    $supervisor = trim($_POST["supervisor"] ?? "");
    $start_date = $_POST["start_date"] ?? "";
    $end_date = $_POST["end_date"] ?? "";
    $duration = trim($_POST["duration"] ?? "");
    $status = trim($_POST["status"] ?? "");
    $work_mode = trim($_POST["work_mode"] ?? "");
    $location = trim($_POST["location"] ?? "");
    $company_website = trim($_POST["company_website"] ?? "");
    $description = trim($_POST["description"] ?? "");
    $responsibilities = trim($_POST["responsibilities"] ?? "");
    $technologies = trim($_POST["technologies"] ?? "");
    $skills_gained = trim($_POST["skills_gained"] ?? "");

    // ----------------------------------------
    // REQUIRED FIELD CHECK
    // ----------------------------------------

    if (
        $internship_title === "" ||
        $internship_type === "" ||
        $company_name === "" ||
        $role === "" ||
        $start_date === "" ||
        $end_date === "" ||
        $status === "" ||
        $work_mode === "" ||
        $description === ""
    ) {

        die("Please fill in all required internship fields.");

    }


    // ----------------------------------------
    // DATE VALIDATION
    // ----------------------------------------

    if ($end_date < $start_date) {

        die("End date cannot be before start date.");

    }


    // ----------------------------------------
    // UPLOAD DIRECTORIES
    // ----------------------------------------

    $certificateDirectory = "uploads/internships/certificates/";
    $supportingDirectory = "uploads/internships/supporting/";

    if (!is_dir($certificateDirectory)) {
        mkdir($certificateDirectory, 0777, true);
    }

    if (!is_dir($supportingDirectory)) {
        mkdir($supportingDirectory, 0777, true);
    }


    // ----------------------------------------
    // CERTIFICATE FILE
    // ----------------------------------------

    $certificate_file = "";
$old_certificate_file = "";
$old_supporting_document = "";

if ($internship_id > 0) {

    $sql = "SELECT certificate_file, supporting_document
            FROM internships
            WHERE internship_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ii",
        $internship_id,
        $user_id
    );

    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);
    $existingInternship = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);

    if (!$existingInternship) {
        die("Internship record not found.");
    }

    $old_certificate_file =
        $existingInternship["certificate_file"] ?? "";

    $old_supporting_document =
        $existingInternship["supporting_document"] ?? "";
}

if ($internship_id > 0) {
    $certificate_file = $old_certificate_file;
}

    if (
        isset($_FILES["certificate_file"]) &&
        $_FILES["certificate_file"]["error"] === UPLOAD_ERR_OK
    ) {

        $certificateName =
            basename($_FILES["certificate_file"]["name"]);

        $certificateExtension =
            strtolower(
                pathinfo(
                    $certificateName,
                    PATHINFO_EXTENSION
                )
            );

        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp",
            "pdf"
        ];

        if (!in_array(
            $certificateExtension,
            $allowedExtensions
        )) {

            die("Invalid certificate file type.");

        }

        $newCertificateName =
            uniqid("certificate_", true) .
            "." .
            $certificateExtension;

        $certificatePath =
            $certificateDirectory .
            $newCertificateName;

        if (
            !move_uploaded_file(
                $_FILES["certificate_file"]["tmp_name"],
                $certificatePath
            )
        ) {

            die("Failed to upload certificate.");

        }

        $certificate_file = $certificatePath;

    }


    // ----------------------------------------
    // SUPPORTING DOCUMENT
    // ----------------------------------------

    $supporting_document = $old_supporting_document;

    if (
        isset($_FILES["supporting_document"]) &&
        $_FILES["supporting_document"]["error"] === UPLOAD_ERR_OK
    ) {

        $supportingName =
            basename($_FILES["supporting_document"]["name"]);

        $supportingExtension =
            strtolower(
                pathinfo(
                    $supportingName,
                    PATHINFO_EXTENSION
                )
            );

        $allowedExtensions = [
            "jpg",
            "jpeg",
            "png",
            "webp",
            "pdf"
        ];

        if (!in_array(
            $supportingExtension,
            $allowedExtensions
        )) {

            die("Invalid supporting document type.");

        }

        $newSupportingName =
            uniqid("supporting_", true) .
            "." .
            $supportingExtension;

        $supportingPath =
            $supportingDirectory .
            $newSupportingName;

        if (
            !move_uploaded_file(
                $_FILES["supporting_document"]["tmp_name"],
                $supportingPath
            )
        ) {

            die("Failed to upload supporting document.");

        }

        $supporting_document = $supportingPath;

    }


    if ($internship_id > 0) {

    // ----------------------------------------
    // UPDATE EXISTING INTERNSHIP
    // ----------------------------------------

    $sql = "UPDATE internships SET
                internship_title = ?,
                internship_type = ?,
                company_name = ?,
                department = ?,
                role = ?,
                supervisor = ?,
                start_date = ?,
                end_date = ?,
                duration = ?,
                status = ?,
                work_mode = ?,
                location = ?,
                company_website = ?,
                description = ?,
                responsibilities = ?,
                technologies = ?,
                skills_gained = ?,
                certificate_file = ?,
                supporting_document = ?
            WHERE internship_id = ?
            AND user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssssssssssssii",
        $internship_title,
        $internship_type,
        $company_name,
        $department,
        $role,
        $supervisor,
        $start_date,
        $end_date,
        $duration,
        $status,
        $work_mode,
        $location,
        $company_website,
        $description,
        $responsibilities,
        $technologies,
        $skills_gained,
        $certificate_file,
        $supporting_document,
        $internship_id,
        $user_id
    );

    if (!mysqli_stmt_execute($stmt)) {
    die("Failed to update internship: " . mysqli_stmt_error($stmt));
}

mysqli_stmt_close($stmt);


// ----------------------------------------
// DELETE OLD FILES AFTER SUCCESSFUL UPDATE
// ----------------------------------------

// Delete old certificate if a new one was uploaded
if (
    !empty($old_certificate_file) &&
    !empty($certificate_file) &&
    $old_certificate_file !== $certificate_file
) {

    $oldCertificatePath =
        __DIR__ . "/" . $old_certificate_file;

    if (file_exists($oldCertificatePath)) {
        unlink($oldCertificatePath);
    }
}


// Delete old supporting document if a new one was uploaded
if (
    !empty($old_supporting_document) &&
    !empty($supporting_document) &&
    $old_supporting_document !== $supporting_document
) {

    $oldSupportingPath =
        __DIR__ . "/" . $old_supporting_document;

    if (file_exists($oldSupportingPath)) {
        unlink($oldSupportingPath);
    }
}

} else {

    // ----------------------------------------
    // INSERT NEW INTERNSHIP
    // ----------------------------------------

    $sql = "INSERT INTO internships (
                user_id,
                internship_title,
                internship_type,
                company_name,
                department,
                role,
                supervisor,
                start_date,
                end_date,
                duration,
                status,
                work_mode,
                location,
                company_website,
                description,
                responsibilities,
                technologies,
                skills_gained,
                certificate_file,
                supporting_document
            )
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "isssssssssssssssssss",
        $user_id,
        $internship_title,
        $internship_type,
        $company_name,
        $department,
        $role,
        $supervisor,
        $start_date,
        $end_date,
        $duration,
        $status,
        $work_mode,
        $location,
        $company_website,
        $description,
        $responsibilities,
        $technologies,
        $skills_gained,
        $certificate_file,
        $supporting_document
    );

    if (!mysqli_stmt_execute($stmt)) {
        die("Failed to save internship: " . mysqli_stmt_error($stmt));
    }

    mysqli_stmt_close($stmt);
}

if ($internship_id > 0) {
    header("Location: internships.php?success=updated");
} else {
    header("Location: internships.php?success=added");
}
exit;
}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Internships | Digital Skill Passport</title>


    <!-- Google Fonts -->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
          rel="stylesheet">


    <!-- Font Awesome -->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- Internship CSS -->

    <link rel="stylesheet"
          href="css/internships.css">

</head>


<body>

<?php if (!empty($successMessage)): ?>
    <div class="success-message" id="successMessage">
        <i class="fa-solid fa-circle-check"></i>
        <span><?= htmlspecialchars($successMessage) ?></span>
    </div>
<?php endif; ?>


    <!--==================================================
                         PAGE WRAPPER
    ===================================================-->

    <div class="internships-container">


        <!--==================================================
                              SIDEBAR
        ===================================================-->

        <aside class="sidebar">


            <!-- Logo -->

            <div class="logo">

                <i class="fa-solid fa-id-card-clip"></i>

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


                <li>

                    <a href="workshops.php">

                        <i class="fa-solid fa-graduation-cap"></i>

                        <span>Workshops</span>

                    </a>

                </li>


                <!-- ACTIVE -->

                <li class="active">

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



        <!--==================================================
                         MAIN CONTENT
        ===================================================-->

        <main class="main-content">


            <!--==================================================
                              TOPBAR
            ===================================================-->

            <header class="topbar">


                <div class="top-left">


                    <button class="menu-toggle"
                            type="button"
                            aria-label="Open menu">

                        <i class="fa-solid fa-bars"></i>

                    </button>


                    <h2>Internships</h2>


                </div>



                <div class="top-right">


                    <!-- Search -->

                    <div class="search-box">

                        <i class="fa-solid fa-magnifying-glass"></i>

                        <input type="search"
       id="internshipSearch"
       placeholder="Search internships..."
       aria-label="Search internships">

                    </div>



                    <!-- Notification -->

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



                    <!-- User -->

                    <div class="user-profile">

    <img src="<?php echo htmlspecialchars($profilePhoto); ?>"
         alt="Profile">

    <div>

        <h4>
            <?php echo htmlspecialchars($full_name); ?>
        </h4>

        <p>
            <?php echo htmlspecialchars($account_type); ?>
        </p>

    </div>

</div>


                </div>


            </header>



            <!--==================================================
                           PAGE HEADER
            ===================================================-->

            <section class="page-header">


                <div class="header-text">


                    <div class="header-icon">

                        <i class="fa-solid fa-building"></i>

                    </div>


                    <div>

                        <h1>

                            Internship Experience

                        </h1>


                        <p>

                            Showcase your internship experiences,
                            organizations, roles, responsibilities
                            and professional skills.

                        </p>

                    </div>


                </div>



                <div class="header-button">

                    <button type="button"
                            class="add-internship-btn"
                            id="addInternshipBtn">

                        <i class="fa-solid fa-plus"></i>

                        Add Internship

                    </button>

                </div>


            </section>



            <!--==================================================
                        INTERNSHIP SUMMARY CARDS
            ===================================================-->

            <section class="summary-cards">


                <!-- Total -->

                <div class="summary-card total-internships">


                    <div class="summary-icon">

                        <i class="fa-solid fa-building"></i>

                    </div>


                    <div>

                        <h2><?= $total_internships ?></h2>


                        <p>

                            Total Internships

                        </p>

                    </div>


                </div>



                <!-- Completed -->

                <div class="summary-card completed-internships">


                    <div class="summary-icon">

                        <i class="fa-solid fa-circle-check"></i>

                    </div>


                    <div>

                        <h2><?= $completed_internships ?></h2>


                        <p>

                            Completed

                        </p>

                    </div>


                </div>



                <!-- Ongoing -->

                <div class="summary-card ongoing-internships">


                    <div class="summary-icon">

                        <i class="fa-solid fa-spinner"></i>

                    </div>


                    <div>

                        <h2><?= $ongoing_internships ?></h2>


                        <p>

                            Ongoing

                        </p>

                    </div>


                </div>



                <!-- Skills -->

                <div class="summary-card internship-skills">


                    <div class="summary-icon">

                        <i class="fa-solid fa-lightbulb"></i>

                    </div>


                    <div>

                        <h2 id="internshipSkillsCount">

    <?= $skills_gained_count ?>

</h2>


                        <p>

                            Skills Gained

                        </p>

                    </div>


                </div>


            </section>



            

            <!--==================================================
                     ADD INTERNSHIP FORM SECTION
            ===================================================-->

            <section class="internship-form-section"
                     id="internshipFormSection">


                <div class="form-card">


                    <!-- Form Header -->

                    <div class="card-header">


                        <div class="card-header-icon">

                            <i class="fa-solid fa-building-circle-check"></i>

                        </div>


                        <div>

                            <h2 id="internshipFormTitle">

    Add Internship

</h2>


<p id="internshipFormDescription">

    Enter the details of your
    internship experience.

</p>

                        </div>


                    </div>



                    

                    



                    <!-- Form -->

                    <form id="internshipForm"
                          action=""
                          method="POST"
                          enctype="multipart/form-data">

                        <input type="hidden"
       name="internship_id"
       id="editingInternshipId"
       value="">


                        <!-- Part 1B continues here -->
                <!--========================================
                         INTERNSHIP INFORMATION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        Internship Information

                    </div>


                    <div class="form-grid">


                        <!-- Internship Title -->

                        <div class="form-group">

                            <label for="internshipTitle">

                                Internship Title

                                <span>*</span>

                            </label>

                            <input type="text"
                                   id="internshipTitle"
                                   name="internship_title"
                                   placeholder="e.g. Data Science Intern"
                                   required>

                        </div>



                        <!-- Internship Type -->

                        <div class="form-group">

                            <label for="internshipType">

                                Internship Type

                                <span>*</span>

                            </label>

                            <select id="internshipType"
                                    name="internship_type"
                                    required>

                                <option value="">

                                    Select Internship Type

                                </option>

                                <option value="technical">

                                    Technical

                                </option>

                                <option value="research">

                                    Research

                                </option>

                                <option value="development">

                                    Development

                                </option>

                                <option value="data-science">

                                    Data Science

                                </option>

                                <option value="ai-ml">

                                    AI / Machine Learning

                                </option>

                                <option value="web-development">

                                    Web Development

                                </option>

                                <option value="other">

                                    Other

                                </option>

                            </select>

                        </div>



                        <!-- Company -->

                        <div class="form-group">

                            <label for="companyName">

                                Company / Organization

                                <span>*</span>

                            </label>

                            <input type="text"
                                   id="companyName"
                                   name="company_name"
                                   placeholder="e.g. ABC Technologies"
                                   required>

                        </div>



                        <!-- Department -->

                        <div class="form-group">

                            <label for="department">

                                Department

                            </label>

                            <input type="text"
                                   id="department"
                                   name="department"
                                   placeholder="e.g. Data Science Department">

                        </div>



                        <!-- Role -->

                        <div class="form-group">

                            <label for="internshipRole">

                                Role / Position

                                <span>*</span>

                            </label>

                            <input type="text"
                                   id="internshipRole"
                                   name="role"
                                   placeholder="e.g. Machine Learning Intern"
                                   required>

                        </div>



                        <!-- Supervisor -->

                        <div class="form-group">

                            <label for="supervisor">

                                Supervisor / Mentor

                            </label>

                            <input type="text"
                                   id="supervisor"
                                   name="supervisor"
                                   placeholder="Enter supervisor or mentor name">

                        </div>


                    </div>



                    <!--========================================
                         DURATION & DATES
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-calendar-days"></i>

                        Internship Duration

                    </div>


                    <div class="form-grid">


                        <!-- Start Date -->

                        <div class="form-group">

                            <label for="internshipStartDate">

                                Start Date

                                <span>*</span>

                            </label>

                            <input type="date"
                                   id="internshipStartDate"
                                   name="start_date"
                                   required>

                        </div>



                        <!-- End Date -->

                        <div class="form-group">

                            <label for="internshipEndDate">

                                End Date

                                <span>*</span>

                            </label>

                            <input type="date"
                                   id="internshipEndDate"
                                   name="end_date"
                                   required>

                        </div>



                        <!-- Duration -->

                        <div class="form-group">

                            <label for="internshipDuration">

                                Duration

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-regular fa-clock"></i>

                                <input type="text"
                                       id="internshipDuration"
                                       name="duration"
                                       placeholder="e.g. 2 Months / 8 Weeks">

                            </div>

                        </div>



                        <!-- Status -->

                        <div class="form-group">

                            <label for="internshipStatus">

                                Internship Status

                                <span>*</span>

                            </label>

                            <select id="internshipStatus"
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
                         WORK MODE & LOCATION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-location-dot"></i>

                        Work Mode & Location

                    </div>


                    <div class="form-grid">


                        <!-- Work Mode -->

                        <div class="form-group">

                            <label for="workMode">

                                Work Mode

                                <span>*</span>

                            </label>

                            <select id="workMode"
                                    name="work_mode"
                                    required>

                                <option value="">

                                    Select Work Mode

                                </option>

                                <option value="remote">

                                    Remote

                                </option>

                                <option value="onsite">

                                    On-site

                                </option>

                                <option value="hybrid">

                                    Hybrid

                                </option>

                            </select>

                        </div>

<!-- Company Website -->

<div class="form-group">

    <label for="companyWebsite">

        Company Website

    </label>

    <div class="input-with-icon">

        <i class="fa-solid fa-globe"></i>

        <input type="url"
               id="companyWebsite"
               name="company_website"
               placeholder="https://example.com">

    </div>

</div>

                        <!-- Location -->

                        <div class="form-group">

                            <label for="internshipLocation">

                                Location

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-solid fa-location-dot"></i>

                                <input type="text"
                                       id="internshipLocation"
                                       name="location"
                                       placeholder="e.g. Kochi, Kerala">

                            </div>

                        </div>


                    </div>



                    <!--========================================
                         INTERNSHIP DESCRIPTION
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        Internship Description

                    </div>


                    <div class="form-group">

                        <label for="internshipDescription">

                            Description

                            <span>*</span>

                        </label>

                        <textarea id="internshipDescription"
                                  name="description"
                                  rows="5"
                                  maxlength="1000"
                                  placeholder="Briefly describe your internship experience, the work you performed and what you learned..."
                                  required></textarea>

                        <small class="field-note">

                            Write a short summary of your internship experience.

                        </small>

                    </div>



                    <!--========================================
                         RESPONSIBILITIES
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-list-check"></i>

                        Responsibilities & Work Done

                    </div>


                    <div class="form-group">

                        <label for="responsibilities">

                            Key Responsibilities

                        </label>

                        <textarea id="responsibilities"
                                  name="responsibilities"
                                  rows="5"
                                  maxlength="1200"
                                  placeholder="e.g. Developed Python programs, cleaned datasets, performed data analysis..."></textarea>

                        <small class="field-note">

                            Mention the major tasks and responsibilities
                            you handled during the internship.

                        </small>

                    </div>



                    <!--========================================
                         SKILLS & TECHNOLOGIES
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-code"></i>

                        Skills & Technologies

                    </div>


                    <div class="form-grid">


                        <!-- Technologies -->

                        <div class="form-group">

                            <label for="technologies">

                                Technologies Used

                            </label>

                            <input type="text"
                                   id="technologies"
                                   name="technologies"
                                   placeholder="e.g. Python, SQL, HTML, JavaScript">

                            <small class="field-note">

                                Separate multiple technologies using commas.

                            </small>

                        </div>



                        <!-- Skills -->

                        <div class="form-group">

                            <label for="skillsGained">

                                Skills Gained

                            </label>

                            <input type="text"
                                   id="skillsGained"
                                   name="skills_gained"
                                   placeholder="e.g. Data Analysis, Communication, Teamwork">

                            <small class="field-note">

                                Separate multiple skills using commas.

                            </small>

                        </div>


                    </div>



                    <!--========================================
                         CERTIFICATE UPLOAD
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-certificate"></i>

                        Internship Certificate

                    </div>


                    <div class="internship-upload-area"
                         id="internshipUploadArea">


                        <div class="upload-icon">

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                        </div>


                        <h3>

                            Upload Internship Certificate

                        </h3>


                        <p>

                            Add the certificate or completion document
                            you received from the organization.

                        </p>


                        <label for="internshipCertificate"
                               class="upload-btn">

                            <i class="fa-solid fa-upload"></i>

                            Choose Certificate

                        </label>


                        <input type="file"
                               id="internshipCertificate"
                               name="certificate_file"
                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                               hidden>


                        <p class="file-info">

                            Supported formats:
                            JPG, JPEG, PNG, WEBP and PDF

                            <br>

                            Maximum file size: 5 MB

                        </p>


                        <!-- Certificate Preview -->

                        <div class="certificate-preview"
                             id="certificatePreview">

                        </div>


                    </div>



                    <!--========================================
                         INTERNSHIP PROOF / DOCUMENT
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-file-lines"></i>

                        Supporting Document

                    </div>


                    <div class="form-group">

                        <label for="supportingDocument">

                            Offer Letter / Experience Letter

                        </label>

                        <input type="file"
                               id="supportingDocument"
                               name="supporting_document"
                               accept=".jpg,.jpeg,.png,.webp,.pdf">

                        <small class="field-note">

                            Optional. You may upload an offer letter,
                            experience letter or other supporting document.

                        </small>

                    </div>


                    <!--========================================
                         FORM BUTTONS
                    =========================================-->

                    <div class="button-group">


                        <button type="submit"
                                class="save-internship-btn">

                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Internship

                        </button>



                        <button type="reset"
        id="resetInternshipBtn"
        class="reset-internship-btn">
            </button>


                    </div>


                </form>

            </div>

        </section>
                    <!--==================================================
                    SAVED INTERNSHIPS SECTION
            ===================================================-->

            <section class="saved-internships-section">


                <!-- Section Header -->

                <div class="section-heading">


                    <div>

                        <span class="section-label">

                            MY EXPERIENCE

                        </span>


                        <h2>

                            My Internships

                        </h2>


                        <p>

                            View and manage your internship experiences.

                        </p>

                    </div>



                    <!-- Filter -->

                    <div class="internship-filter">


                        <label for="internshipFilter">

                            <i class="fa-solid fa-filter"></i>

                            Filter

                        </label>


                        <select id="internshipFilter">

                            <option value="all">

                                All Internships

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



                <!--==================================================
                         INTERNSHIPS GRID
                ===================================================-->

                <div class="internships-grid"
                     id="internshipsGrid">


<?php

// ========================================
// GET USER'S INTERNSHIPS
// ========================================

$sql = "SELECT *
        FROM internships
        WHERE user_id = ?
        ORDER BY start_date DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$internships = [];

while ($row = mysqli_fetch_assoc($result)) {
    $internships[] = $row;
}

mysqli_stmt_close($stmt);


// ========================================
// DISPLAY INTERNSHIPS
// ========================================

foreach ($internships as $internship):

    $status = strtolower($internship["status"] ?? "");

    // Make status values consistent for CSS/filtering
    if ($status === "completed") {
        $statusClass = "completed";
    } elseif ($status === "ongoing") {
        $statusClass = "ongoing";
    } elseif ($status === "upcoming") {
        $statusClass = "upcoming";
    } else {
        $statusClass = "";
    }


    // Format dates
    $startDateDisplay = !empty($internship["start_date"])
        ? date("M Y", strtotime($internship["start_date"]))
        : "-";

    $endDateDisplay = !empty($internship["end_date"])
        ? date("M Y", strtotime($internship["end_date"]))
        : "-";


    // Technologies → skill tags
    $technologies = [];

    if (!empty($internship["technologies"])) {

        $technologies = array_filter(
            array_map(
                "trim",
                explode(",", $internship["technologies"])
            )
        );

    }

?>

<div class="internship-card"
     data-status="<?= htmlspecialchars($statusClass) ?>"
     data-title="<?= htmlspecialchars($internship["internship_title"]) ?>">

    <div class="internship-card-content">

        <div class="internship-card-header">

            <span class="internship-type">
                <?= htmlspecialchars($internship["internship_type"]) ?>
            </span>

            <i class="fa-solid fa-briefcase"></i>

        </div>


        <h3>
            <?= htmlspecialchars($internship["internship_title"]) ?>
        </h3>


        <div class="company-name">

            <i class="fa-solid fa-building"></i>

            <?= htmlspecialchars($internship["company_name"]) ?>

        </div>


        <?php if (!empty($internship["role"])): ?>

            <div class="internship-role">

                <i class="fa-solid fa-user-tie"></i>

                <?= htmlspecialchars($internship["role"]) ?>

            </div>

        <?php endif; ?>


        <div class="internship-meta">

            <span>

                <i class="fa-regular fa-calendar"></i>

                <?= htmlspecialchars($startDateDisplay) ?>
                -
                <?= htmlspecialchars($endDateDisplay) ?>

            </span>


            <?php if (!empty($internship["duration"])): ?>

                <span>

                    <i class="fa-regular fa-clock"></i>

                    <?= htmlspecialchars($internship["duration"]) ?>

                </span>

            <?php endif; ?>

        </div>


        <div class="internship-location">

            <i class="fa-solid fa-location-dot"></i>

            <span>
                <?= !empty($internship["location"])
                    ? htmlspecialchars($internship["location"])
                    : "Location not specified"
                ?>
            </span>


            <?php if (!empty($internship["work_mode"])): ?>

                <span class="mode-badge <?= strtolower(htmlspecialchars($internship["work_mode"])) ?>">

                    <?= htmlspecialchars($internship["work_mode"]) ?>

                </span>

            <?php endif; ?>

        </div>


        <?php if (!empty($technologies)): ?>

            <div class="internship-skills">

                <?php foreach ($technologies as $technology): ?>

                    <span>
                        <?= htmlspecialchars($technology) ?>
                    </span>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>

    </div>


    <div class="internship-card-bottom">

    <span class="status-badge <?= htmlspecialchars($statusClass) ?>">
        <i class="fa-solid fa-circle"></i>
        <?= htmlspecialchars(ucfirst($status)) ?>
    </span>

    <div class="internship-card-actions">

        <button
    type="button"
    class="edit-internship"
    data-internship-id="<?= (int)$internship["internship_id"] ?>"
    data-title="<?= htmlspecialchars($internship["internship_title"], ENT_QUOTES, 'UTF-8') ?>"
    data-type="<?= htmlspecialchars($internship["internship_type"], ENT_QUOTES, 'UTF-8') ?>"
    data-company="<?= htmlspecialchars($internship["company_name"], ENT_QUOTES, 'UTF-8') ?>"
    data-department="<?= htmlspecialchars($internship["department"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-role="<?= htmlspecialchars($internship["role"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-supervisor="<?= htmlspecialchars($internship["supervisor"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-start-date="<?= htmlspecialchars($internship["start_date"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-end-date="<?= htmlspecialchars($internship["end_date"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-duration="<?= htmlspecialchars($internship["duration"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-status="<?= htmlspecialchars($internship["status"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-work-mode="<?= htmlspecialchars($internship["work_mode"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-location="<?= htmlspecialchars($internship["location"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-company-website="<?= htmlspecialchars($internship["company_website"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-description="<?= htmlspecialchars($internship["description"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-responsibilities="<?= htmlspecialchars($internship["responsibilities"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-technologies="<?= htmlspecialchars($internship["technologies"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-skills-gained="<?= htmlspecialchars($internship["skills_gained"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-certificate="<?= htmlspecialchars($internship["certificate_file"] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    data-supporting-document="<?= htmlspecialchars($internship["supporting_document"] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    <i class="fa-solid fa-pen"></i>
    Edit
</button>

        <button
            type="button"
            class="view-internship"
            data-internship-id="<?= (int)$internship["internship_id"] ?>">
            View Details
            <i class="fa-solid fa-arrow-right"></i>
        </button>

        <button
    type="button"
    class="delete-internship"
    data-internship-id="<?= (int)$internship["internship_id"] ?>">
    <i class="fa-solid fa-trash"></i>
    Delete
</button>

    </div>

</div>

</div>

<?php endforeach; ?>
                    

                </div>



                <!--==================================================
                         NO INTERNSHIPS MESSAGE
                ===================================================-->

                <div class="no-internships"
                     id="noInternships"
                     style="display: none;">


                    <div class="empty-icon">

                        <i class="fa-solid fa-building"></i>

                    </div>


                    <h3>

                        No Internships Found

                    </h3>


                    <p>

                        No internship matches your current filter.

                    </p>


                    <button type="button"
                            class="empty-add-btn"
                            id="emptyAddInternshipBtn">

                        <i class="fa-solid fa-plus"></i>

                        Add Internship

                    </button>


                </div>


            </section>



            <!--==================================================
                         INTERNSHIP DETAILS MODAL
            ===================================================-->

            <div class="internship-modal"
                 id="internshipModal"
                 aria-hidden="true">


                <div class="modal-overlay"></div>


                <div class="modal-container">


                    <!-- Modal Header -->

                    <div class="modal-header">


                        <div class="modal-title-area">


                            <div class="modal-icon">

                                <i class="fa-solid fa-building"></i>

                            </div>


                            <div>

                                <span>

                                    INTERNSHIP DETAILS

                                </span>


                                <h2 id="modalInternshipTitle">

                                    Internship Details

                                </h2>

                            </div>


                        </div>



                        <button type="button"
                                class="close-modal"
                                id="closeInternshipModal"
                                aria-label="Close">

                            <i class="fa-solid fa-xmark"></i>

                        </button>


                    </div>



                    <!-- Modal Content -->

                    <div class="modal-body"
                         id="modalInternshipContent">


                        <!-- JavaScript will insert
                             internship details here -->


                    </div>



                    <!-- Modal Footer -->

                    <div class="modal-footer">


                        <button type="button"
                                class="modal-close-btn"
                                id="modalCloseButton">

                            Close

                        </button>


                    </div>


                </div>


            </div>



        </main>


    </div>



    <!--==================================================
                     INTERNSHIPS JAVASCRIPT
    ===================================================-->

    <script src="js/internships.js"></script>


</body>

</html>

