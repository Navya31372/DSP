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
        GET LOGGED-IN USER DETAILS
==================================================*/

$sql = "SELECT u.full_name, u.account_type, p.profile_photo
        FROM users u
        LEFT JOIN profile p ON u.user_id = p.user_id
        WHERE u.user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$user_result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($user_result);

mysqli_stmt_close($stmt);


$full_name = $user["full_name"] ?? "User";

$account_type = $user["account_type"] ?? "Student";

$profile_photo = $user["profile_photo"] ?? "";


/*==================================================
        SAVE NEW CERTIFICATE
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $certificate_name = trim($_POST["certificate_name"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $organization = trim($_POST["issuing_organization"] ?? "");
    $certificate_level = trim($_POST["certificate_level"] ?? "");

    $issue_date = !empty($_POST["issue_date"])
        ? $_POST["issue_date"]
        : null;

    $expiry_date = !empty($_POST["expiry_date"])
        ? $_POST["expiry_date"]
        : null;

    $credential_id = trim($_POST["credential_id"] ?? "");
    $credential_url = trim($_POST["credential_url"] ?? "");
    $description = trim($_POST["description"] ?? "");

    $visibility = trim($_POST["visibility"] ?? "public");


    /*----------------------------------------------
            BASIC VALIDATION
    ----------------------------------------------*/

    if (
        $certificate_name === "" ||
        $category === "" ||
        $organization === "" ||
        $issue_date === ""
    ) {

        die("Please fill in all required certificate details.");

    }


    /*----------------------------------------------
            CERTIFICATE FILE
    ----------------------------------------------*/

    $certificate_file = null;

    if (
        isset($_FILES["certificate_file"]) &&
        $_FILES["certificate_file"]["error"] === UPLOAD_ERR_OK
    ) {

        $file = $_FILES["certificate_file"];

        $allowed_types = [
            "image/jpeg",
            "image/png",
            "image/webp",
            "application/pdf"
        ];

        if (!in_array($file["type"], $allowed_types)) {

            die("Invalid certificate file type.");

        }

        if ($file["size"] > 5 * 1024 * 1024) {

            die("Certificate file must not exceed 5 MB.");

        }


        $upload_directory = "uploads/certificates/";

        if (!is_dir($upload_directory)) {

            mkdir($upload_directory, 0777, true);

        }


        $extension = pathinfo(
            $file["name"],
            PATHINFO_EXTENSION
        );


        $new_filename =
            "certificate_" .
            $user_id . "_" .
            time() . "_" .
            uniqid() .
            "." .
            $extension;


        $target_path =
            $upload_directory . $new_filename;


        if (!move_uploaded_file(
            $file["tmp_name"],
            $target_path
        )) {

            die("Failed to upload certificate file.");

        }

        $certificate_file = $target_path;

    }


    /*==================================================
        INSERT / UPDATE CERTIFICATE
==================================================*/

$edit_id = isset($_POST["edit_id"])
    ? (int) $_POST["edit_id"]
    : 0;


/*--------------------------------------------------
        UPDATE EXISTING CERTIFICATE
--------------------------------------------------*/

if ($edit_id > 0) {

    /* Keep old file if no new file was uploaded */

    if ($certificate_file === null) {

        $sql = "SELECT certificate_file
                FROM certificates
                WHERE certificate_id = ?
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

        $old_certificate = mysqli_fetch_assoc($result);

        mysqli_stmt_close($stmt);

        $certificate_file =
            $old_certificate["certificate_file"] ?? null;
    }


    $sql = "UPDATE certificates
            SET certificate_name = ?,
                organization = ?,
                category = ?,
                certificate_level = ?,
                issue_date = ?,
                expiry_date = ?,
                credential_id = ?,
                credential_url = ?,
                description = ?,
                visibility = ?,
                certificate_file = ?
            WHERE certificate_id = ?
            AND user_id = ?";


    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssssssssii",
        $certificate_name,
        $organization,
        $category,
        $certificate_level,
        $issue_date,
        $expiry_date,
        $credential_id,
        $credential_url,
        $description,
        $visibility,
        $certificate_file,
        $edit_id,
        $user_id
    );


    if (!mysqli_stmt_execute($stmt)) {

        die(
            "Error updating certificate: " .
            mysqli_stmt_error($stmt)
        );

    }


    mysqli_stmt_close($stmt);


    header("Location: certificates.php?certificate_updated=1");

    exit;

}


/*--------------------------------------------------
        INSERT NEW CERTIFICATE
--------------------------------------------------*/

$sql = "INSERT INTO certificates
        (
            user_id,
            certificate_name,
            organization,
            category,
            certificate_level,
            issue_date,
            expiry_date,
            credential_id,
            credential_url,
            description,
            visibility,
            certificate_file
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "isssssssssss",
    $user_id,
    $certificate_name,
    $organization,
    $category,
    $certificate_level,
    $issue_date,
    $expiry_date,
    $credential_id,
    $credential_url,
    $description,
    $visibility,
    $certificate_file
);


if (!mysqli_stmt_execute($stmt)) {

    die(
        "Error saving certificate: " .
        mysqli_stmt_error($stmt)
    );

}


mysqli_stmt_close($stmt);


header("Location: certificates.php?certificate_added=1");

exit;

}


/*==================================================
        EDIT CERTIFICATE
==================================================*/

$edit_certificate = null;

if (isset($_GET["edit_id"])) {

    $edit_id = (int) $_GET["edit_id"];

    $sql = "SELECT *
            FROM certificates
            WHERE certificate_id = ?
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

    $edit_certificate = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}


/*==================================================
        CERTIFICATE SUMMARY
==================================================*/

$sql = "SELECT category
        FROM certificates
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$certificates_result = mysqli_stmt_get_result($stmt);


$total_certificates = 0;
$academic_certificates = 0;
$professional_certificates = 0;
$online_certificates = 0;


while ($certificate = mysqli_fetch_assoc($certificates_result)) {

    $total_certificates++;

    if ($certificate["category"] === "academic") {

        $academic_certificates++;

    }

    if ($certificate["category"] === "professional") {

        $professional_certificates++;

    }

    if ($certificate["category"] === "online") {

        $online_certificates++;

    }

}


mysqli_stmt_close($stmt);


/*==================================================
        GET CERTIFICATES
==================================================*/

$sql = "SELECT *
        FROM certificates
        WHERE user_id = ?
        ORDER BY issue_date DESC";


$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$certificates_result = mysqli_stmt_get_result($stmt);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Certificates | Digital Skill Passport</title>


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


    <!-- Certificates CSS -->

    <link rel="stylesheet"
          href="css/certificates.css">

</head>


<body>


<div class="certificates-container">


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


            <li>

                <a href="projects.php">

                    <i class="fa-solid fa-briefcase"></i>

                    <span>Projects</span>

                </a>

            </li>


            <li class="active">

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

    <?php if (isset($_GET['certificate_added'])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Certificate added successfully.
    </div>

<?php elseif (isset($_GET['certificate_updated'])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Certificate updated successfully.
    </div>

<?php elseif (isset($_GET['certificate_deleted'])): ?>

    <div class="success-message">
        <i class="fa-solid fa-circle-check"></i>
        Certificate deleted successfully.
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

                <h2>Certificates Management</h2>

            </div>


            <div class="top-right">


                <!-- Search -->

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text"
                           id="certificateSearch"
                           placeholder="Search certificates...">

                </div>


                <!-- Notification -->
                 
<div class="notification" id="notificationBell">

    <i class="fa-regular fa-bell"></i>

    <?php if ($unread_notifications > 0): ?>

        <span><?= $unread_notifications ?></span>

    <?php endif; ?>


    <!-- Notification Popup -->

    <div class="notification-popup" id="notificationPopup">

        <?php if (!empty($notifications)): ?>

            <?php foreach ($notifications as $notification): ?>

                <div class="notification-item">

                    <i class="fa-solid fa-circle-info"></i>

                    <p>
                        <?= htmlspecialchars($notification['message']) ?>
                    </p>

                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="notification-item">

                <i class="fa-regular fa-bell-slash"></i>

                <p>No notifications</p>

            </div>

        <?php endif; ?>

    </div>

</div>


                <!-- User -->

                <div class="user-profile">

    <?php if (!empty($profile_photo)): ?>

        <img src="<?= htmlspecialchars($profile_photo) ?>"
             alt="Profile">

    <?php else: ?>

        <img src="images/profile.png"
             alt="Profile">

    <?php endif; ?>

    <div>

        <h4><?= htmlspecialchars($full_name) ?></h4>

        <p><?= htmlspecialchars($account_type) ?></p>

    </div>

</div>


            </div>

        </header>



        <!--================================
                PAGE HEADER
        =================================-->

        <section class="page-header">


            <div class="header-text">

                <h1>

                    My Certificates

                </h1>

                <p>

                    Store, organize and showcase your
                    professional and academic certificates
                    in one secure place.

                </p>

            </div>


            <div class="header-button">

                <a href="#addCertificateForm"
                   class="add-certificate-btn">

                    <i class="fa-solid fa-plus"></i>

                    Add Certificate

                </a>

            </div>


        </section>



        <!--================================
           CERTIFICATE SUMMARY CARDS
        =================================-->

        <section class="summary-cards">


            <!-- Total Certificates -->

            <div class="summary-card total">

                <i class="fa-solid fa-certificate"></i>

                <h2><?= $total_certificates ?></h2>

                <p>Total Certificates</p>

            </div>


            <!-- Academic -->

            <div class="summary-card academic">

                <i class="fa-solid fa-graduation-cap"></i>

                <h2><?= $academic_certificates ?></h2>

                <p>Academic</p>

            </div>


            <!-- Professional -->

            <div class="summary-card professional">

                <i class="fa-solid fa-briefcase"></i>

                <h2><?= $professional_certificates ?></h2>

                <p>Professional</p>

            </div>


            <!-- Online -->

            <div class="summary-card online">

                <i class="fa-solid fa-laptop-code"></i>

                <h2><?= $online_certificates ?></h2>

                <p>Online Courses</p>

            </div>

        </section>


                <!--================================
             ADD CERTIFICATE SECTION
        =================================-->

        <section class="certificate-form-section"
                 id="addCertificateForm">

            <div class="form-card">


                <!-- Form Header -->

                <div class="card-header">

                    <h2>

    <i class="fa-solid fa-file-circle-plus"></i>

    <?= $edit_certificate ? 'Edit Certificate' : 'Add New Certificate' ?>

</h2>

                    <p>

                        Add your academic, professional or
                        online course certificate details.

                    </p>

                </div>



                <!-- Certificate Form -->

                <form action="certificates.php"
      method="POST"
      enctype="multipart/form-data">

      <?php if (!empty($edit_certificate)): ?>

    <input type="hidden"
           name="edit_id"
           value="<?= $edit_certificate['certificate_id'] ?>">

<?php endif; ?>


                    <!--================================
                       CERTIFICATE INFORMATION
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        Certificate Information

                    </div>


                    <div class="form-grid">


                        <!-- Certificate Name -->

                        <div class="form-group">

                            <label for="certificateName">

                                Certificate Name

                                <span>*</span>

                            </label>

                            <input type="text"
       id="certificateName"
       name="certificate_name"
       placeholder="e.g. Python Programming Certificate"
       value="<?= htmlspecialchars($edit_certificate['certificate_name'] ?? '') ?>"
       required>

                        </div>



                        <!-- Category -->

                        <div class="form-group">

                            <label for="certificateCategory">

                                Certificate Category

                                <span>*</span>

                            </label>

                            <select id="certificateCategory"
                                    name="category"
                                    required>

                                <option value="">Select Category</option>

<option value="academic"
    <?= (($edit_certificate['category'] ?? '') === 'academic') ? 'selected' : '' ?>>
    Academic
</option>

<option value="professional"
    <?= (($edit_certificate['category'] ?? '') === 'professional') ? 'selected' : '' ?>>
    Professional
</option>

<option value="online"
    <?= (($edit_certificate['category'] ?? '') === 'online') ? 'selected' : '' ?>>
    Online Course
</option>

<option value="workshop"
    <?= (($edit_certificate['category'] ?? '') === 'workshop') ? 'selected' : '' ?>>
    Workshop
</option>

<option value="internship"
    <?= (($edit_certificate['category'] ?? '') === 'internship') ? 'selected' : '' ?>>
    Internship
</option>

<option value="other"
    <?= (($edit_certificate['category'] ?? '') === 'other') ? 'selected' : '' ?>>
    Other
</option>

                            </select>

                        </div>



                        <!-- Issuing Organization -->

                        <div class="form-group">

                            <label for="issuingOrganization">

                                Issuing Organization

                                <span>*</span>

                            </label>

                            <input type="text"
       id="issuingOrganization"
       name="issuing_organization"
       placeholder="e.g. Coursera, NPTEL, Google"
       value="<?= htmlspecialchars($edit_certificate['organization'] ?? '') ?>"
       required>

                        </div>



                        <!-- Certificate Level -->

                        <div class="form-group">

                            <label for="certificateLevel">

                                Certificate Level

                            </label>

                            <select id="certificateLevel"
                                    name="certificate_level">

                                <option value="">Select Level</option>

<option value="beginner"
    <?= (($edit_certificate['certificate_level'] ?? '') === 'beginner') ? 'selected' : '' ?>>
    Beginner
</option>

<option value="intermediate"
    <?= (($edit_certificate['certificate_level'] ?? '') === 'intermediate') ? 'selected' : '' ?>>
    Intermediate
</option>

<option value="advanced"
    <?= (($edit_certificate['certificate_level'] ?? '') === 'advanced') ? 'selected' : '' ?>>
    Advanced
</option>

<option value="professional"
    <?= (($edit_certificate['certificate_level'] ?? '') === 'professional') ? 'selected' : '' ?>>
    Professional
</option>

                            </select>

                        </div>


                    </div>



                    <!--================================
                    DATE AND CREDENTIAL INFORMATION
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-regular fa-calendar"></i>

                        Certificate Details

                    </div>


                    <div class="form-grid">


                        <!-- Issue Date -->

                        <div class="form-group">

                            <label for="issueDate">

                                Issue Date

                                <span>*</span>

                            </label>

                            <input type="date"
       id="issueDate"
       name="issue_date"
       value="<?= htmlspecialchars($edit_certificate['issue_date'] ?? '') ?>"
       required>

                        </div>



                        <!-- Expiry Date -->

                        <div class="form-group">

                            <label for="expiryDate">

                                Expiry Date

                            </label>

                            <input type="date"
       id="expiryDate"
       name="expiry_date"
       value="<?= htmlspecialchars($edit_certificate['expiry_date'] ?? '') ?>">

                            <small class="field-note">

                                Leave empty if the certificate
                                does not expire.

                            </small>

                        </div>



                        <!-- Credential ID -->

                        <div class="form-group">

                            <label for="credentialId">

                                Credential ID

                            </label>

                            <input type="text"
       id="credentialId"
       name="credential_id"
       placeholder="Enter credential ID"
       value="<?= htmlspecialchars($edit_certificate['credential_id'] ?? '') ?>">

                        </div>



                        <!-- Credential URL -->

                        <div class="form-group">

                            <label for="credentialUrl">

                                Credential URL

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-solid fa-link"></i>

                                <input type="url"
       id="credentialUrl"
       name="credential_url"
       placeholder="https://example.com/verify"
       value="<?= htmlspecialchars($edit_certificate['credential_url'] ?? '') ?>">

                            </div>

                        </div>


                    </div>



                    <!--================================
                    CERTIFICATE DESCRIPTION
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        Certificate Description

                    </div>


                    <div class="form-group">

                        <label for="certificateDescription">

                            Description

                        </label>

                        <textarea id="certificateDescription"
          name="description"
          rows="5"
          placeholder="Write a short description about this certificate, course or achievement..."><?= htmlspecialchars($edit_certificate['description'] ?? '') ?></textarea>

                    </div>



                    <!--================================
                       CERTIFICATE FILE UPLOAD
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-cloud-arrow-up"></i>

                        Certificate File

                    </div>


                    <div class="certificate-upload-area">


                        <div class="upload-icon">

                            <i class="fa-solid fa-file-arrow-up"></i>

                        </div>


                        <h3>

                            Upload Your Certificate

                        </h3>


                        <p>

                            Upload a certificate image or PDF
                            document.

                        </p>


                        <!-- Upload Button -->

                        <label for="certificateFile"
                               class="upload-btn">

                            <i class="fa-solid fa-upload"></i>

                            Choose Certificate

                        </label>


                        <input type="file"
                               id="certificateFile"
                               name="certificate_file"
                               accept=".jpg,.jpeg,.png,.webp,.pdf"
                               hidden>


                        <p class="file-info">

                            Supported formats:
                            JPG, JPEG, PNG, WEBP and PDF

                            <br>

                            Maximum file size: 5 MB

                        </p>


                        <!-- File Preview -->

                        <div class="certificate-preview"
                             id="certificatePreview">

                        </div>


                    </div>



                    <!--================================
                     CERTIFICATE VISIBILITY
                    =================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-eye"></i>

                        Certificate Visibility

                    </div>


                    <div class="visibility-options">


                        <label class="visibility-option">

                            <input type="radio"
       name="visibility"
       value="public"
       <?= (($edit_certificate['visibility'] ?? 'public') === 'public') ? 'checked' : '' ?>>

                            <span class="custom-radio"></span>

                            <span class="visibility-content">

                                <strong>

                                    Public

                                </strong>

                                <small>

                                    Show this certificate
                                    on your digital passport.

                                </small>

                            </span>

                        </label>



                        <label class="visibility-option">

                            <input type="radio"
       name="visibility"
       value="private"
       <?= (($edit_certificate['visibility'] ?? '') === 'private') ? 'checked' : '' ?>>

                            <span class="custom-radio"></span>

                            <span class="visibility-content">

                                <strong>

                                    Private

                                </strong>

                                <small>

                                    Keep this certificate
                                    hidden from your public profile.

                                </small>

                            </span>

                        </label>


                    </div>



                    <!--================================
                        FORM BUTTONS
                    =================================-->

                    <div class="button-group">


                        <button type="submit"
                                class="save-certificate-btn">

                            <i class="fa-solid fa-floppy-disk"></i>

                            <?= $edit_certificate ? 'Update Certificate' : 'Save Certificate' ?>

                        </button>



                        <button type="reset"
                                class="reset-certificate-btn">

                            <i class="fa-solid fa-rotate-right"></i>

                            Reset

                        </button>


                    </div>


                </form>

            </div>

        </section>
                <!--================================
          CERTIFICATE SHOWCASE SECTION
        =================================-->

        <section class="certificates-showcase">


            <!-- Section Header -->

            <div class="section-heading">

                <div>

                    <h2>

                        <i class="fa-solid fa-award"></i>

                        My Certificates

                    </h2>

                    <p>

                        View and manage all your certificates.

                    </p>

                </div>


                <!-- Certificate Filter -->

                <div class="certificate-filter">

                    <select id="certificateFilter">

                        <option value="all">

                            All Certificates

                        </option>

                        <option value="academic">

                            Academic

                        </option>

                        <option value="professional">

                            Professional

                        </option>

                        <option value="online">

                            Online Courses

                        </option>

                        <option value="workshop">

                            Workshops

                        </option>

                        <option value="internship">

                            Internships

                        </option>

                        <option value="other">

                            Other

                        </option>

                    </select>

                </div>

            </div>



            <!--================================
                 CERTIFICATE GRID
            =================================-->

            <div class="certificate-grid"
                 id="certificateGrid">

                 <?php if (mysqli_num_rows($certificates_result) === 0): ?>

    <div class="no-certificates" id="noCertificates">

        <i class="fa-solid fa-folder-open"></i>

        <h3>No Certificates Found</h3>

        <p>
            You have not added any certificates yet.
            Click "Add Certificate" to get started.
        </p>

    </div>

<?php else: ?>


                <?php while ($certificate = mysqli_fetch_assoc($certificates_result)): ?>

    <article class="certificate-card"
             data-category="<?= htmlspecialchars($certificate['category']) ?>">

        <!-- Certificate Preview -->

<div class="certificate-image">

    <?php if (!empty($certificate['certificate_file'])): ?>

        <?php
        $certificate_file = $certificate['certificate_file'];
        $file_extension = strtolower(
            pathinfo($certificate_file, PATHINFO_EXTENSION)
        );
        ?>

        <?php if (in_array($file_extension, ['jpg', 'jpeg', 'png', 'webp'])): ?>

            <img src="<?= htmlspecialchars($certificate_file) ?>"
                 alt="<?= htmlspecialchars($certificate['certificate_name']) ?>">

        <?php elseif ($file_extension === 'pdf'): ?>

            <div class="pdf-certificate-preview">

                <i class="fa-solid fa-file-pdf"></i>

                <span>PDF Certificate</span>

            </div>

        <?php else: ?>

            <img src="images/certificate-default.webp"
                 alt="Certificate">

        <?php endif; ?>

    <?php else: ?>

        <img src="images/certificate-default.webp"
             alt="Certificate">

    <?php endif; ?>


    <!-- Category -->

    <span class="certificate-category <?= htmlspecialchars($certificate['category']) ?>">

        <?= ucfirst(htmlspecialchars($certificate['category'])) ?>

    </span>


            <!-- View Button -->

            <a href="<?= htmlspecialchars($certificate['certificate_file']) ?>"
   class="view-certificate"
   title="View Certificate"
   target="_blank">

    <i class="fa-solid fa-eye"></i>

</a>

        </div>


        <!-- Certificate Content -->

        <div class="certificate-content">


            <!-- Certificate Icon -->

            <div class="certificate-icon">

                <i class="fa-solid fa-certificate"></i>

            </div>


            <!-- Certificate Name -->

            <h3>

                <?= htmlspecialchars($certificate['certificate_name']) ?>

            </h3>


            <!-- Issuing Organization -->

            <p class="issuer">

                <i class="fa-solid fa-building-columns"></i>

                <?= htmlspecialchars($certificate['organization']) ?>

            </p>


            <!-- Issue Date -->

            <div class="certificate-details">

                <div>

                    <span>Issued</span>

                    <strong>

                        <?= !empty($certificate['issue_date'])
                            ? date("F Y", strtotime($certificate['issue_date']))
                            : "Not specified" ?>

                    </strong>

                </div>

            </div>


            <!-- Certificate Actions -->

            <div class="certificate-actions">


                <?php if (!empty($certificate['credential_url'])): ?>

                    <a href="<?= htmlspecialchars($certificate['credential_url']) ?>"
                       class="credential-btn"
                       target="_blank">

                        <i class="fa-solid fa-link"></i>

                        Verify

                    </a>

                <?php endif; ?>


                <a href="certificates.php?edit_id=<?= $certificate['certificate_id'] ?>"
   class="edit-certificate">

    <i class="fa-solid fa-pen"></i>
    Edit

</a>


                <form action="delete_certificate.php"
                      method="POST"
                      class="delete-certificate-form">

                    <input type="hidden"
                           name="certificate_id"
                           value="<?= $certificate['certificate_id'] ?>">

                    <button type="submit"
                            class="delete-certificate">

                        <i class="fa-solid fa-trash"></i>

                    </button>

                </form>


            </div>

        </div>

    </article>

    

<?php endwhile; ?>
<?php endif; ?>


                <!--================================
                    EMPTY STATE
                =================================-->

                <div class="no-certificates"
                     id="noCertificates"
                     style="display: none;">

                    <i class="fa-solid fa-folder-open"></i>

                    <h3>

                        No Certificates Found

                    </h3>

                    <p>

                        No certificates match your
                        search or selected category.

                    </p>

                </div>


            </div>

        </section>



       


        <!--================================
                 FOOTER
        =================================-->

        <footer class="certificates-footer">

            <p>

                © 2026 Digital Skill Passport.
                All rights reserved.

            </p>

        </footer>


    </main>

</div>



<!--================================
          CERTIFICATE VIEW MODAL
=================================-->

<div class="certificate-modal"
     id="certificateModal">


    <div class="modal-overlay"></div>


    <div class="modal-content">


        <button type="button"
                class="close-modal"
                id="closeCertificateModal">

            <i class="fa-solid fa-xmark"></i>

        </button>


        <div class="modal-image-container"
             id="modalCertificateContent">

        </div>


    </div>

</div>



<!--================================
          CERTIFICATES JS
=================================-->

<script src="js/certificates.js"></script>


</body>

</html>