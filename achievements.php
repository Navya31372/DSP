<?php

session_start();

require_once "db.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


/*==================================================
              LOGIN PROTECTION
==================================================*/

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}


$user_id = $_SESSION["user_id"];


/*==================================================
              USER INFORMATION
==================================================*/

$user_query = "
    SELECT
        u.full_name,
        u.account_type,
        p.profile_photo
    FROM users u
    LEFT JOIN profile p
        ON u.user_id = p.user_id
    WHERE u.user_id = ?
";

$user_stmt = mysqli_prepare($conn, $user_query);

mysqli_stmt_bind_param(
    $user_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($user_stmt);

$user_result = mysqli_stmt_get_result($user_stmt);

$user_data = mysqli_fetch_assoc($user_result);

$user_name = $user_data["full_name"] ?? "User";

$user_role = $user_data["account_type"] ?? "Student";

$profile_photo = $user_data["profile_photo"] ?? "";

mysqli_stmt_close($user_stmt);


/*==================================================
              NOTIFICATION COUNT
==================================================*/

$notification_query = "
    SELECT COUNT(*) AS notification_count
    FROM notifications
    WHERE user_id = ?
    AND is_read = 0
";

$notification_stmt = mysqli_prepare(
    $conn,
    $notification_query
);

mysqli_stmt_bind_param(
    $notification_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($notification_stmt);

$notification_result = mysqli_stmt_get_result(
    $notification_stmt
);

$notification_data = mysqli_fetch_assoc(
    $notification_result
);

$notification_count =
    (int) ($notification_data["notification_count"] ?? 0);

mysqli_stmt_close($notification_stmt);


/*==================================================
              ACHIEVEMENT FILE DIRECTORY
==================================================*/

$achievement_upload_dir =
    __DIR__ . "/uploads/achievements/";

$achievement_upload_url =
    "uploads/achievements/";


if (!is_dir($achievement_upload_dir)) {

    mkdir(
        $achievement_upload_dir,
        0777,
        true
    );

}


/*==================================================
              SUCCESS / ERROR MESSAGE
==================================================*/

$success_message =
    $_SESSION["achievement_success"] ?? "";

$error_message =
    $_SESSION["achievement_error"] ?? "";

unset($_SESSION["achievement_success"]);
unset($_SESSION["achievement_error"]);


/*==================================================
              ADD / UPDATE / DELETE
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $action = $_POST["action"] ?? "";


    /*==================================================
                    ADD ACHIEVEMENT
    ==================================================*/

    if ($action === "add") {


        $achievement_title =
            trim($_POST["achievement_title"] ?? "");

        $achievement_type =
            trim($_POST["achievement_type"] ?? "");

        $position =
            trim($_POST["position"] ?? "");

        $achievement_category =
            trim($_POST["achievement_category"] ?? "");

        $organization =
            trim($_POST["organization"] ?? "");

        $achievement_date =
            $_POST["achievement_date"] ?? "";

        $achievement_description =
            trim($_POST["achievement_description"] ?? "");

        $achievement_skills =
            trim($_POST["achievement_skills"] ?? "");

        $visibility =
            $_POST["visibility"] ?? "public";
        
        $certificate_file =
    $achievement["certificate_file"] ?? "";


        if (
            $achievement_title === "" ||
            $achievement_type === "" ||
            $organization === "" ||
            $achievement_date === "" ||
            $achievement_description === ""
        ) {

            $error_message =
                "Please fill in all required fields.";

        } else {


            $certificate_file = null;


            /*==========================================
                     FILE UPLOAD
            ==========================================*/

            if (
                isset($_FILES["achievement_proof"]) &&
                $_FILES["achievement_proof"]["error"]
                    !== UPLOAD_ERR_NO_FILE
            ) {


                $file = $_FILES["achievement_proof"];


                if ($file["error"] !== UPLOAD_ERR_OK) {

                    $error_message =
                        "There was a problem uploading the file.";

                } elseif ($file["size"] > 5 * 1024 * 1024) {

                    $error_message =
                        "File size must not exceed 5 MB.";

                } else {


                    $allowed_extensions = [
                        "jpg",
                        "jpeg",
                        "png",
                        "pdf"
                    ];


                    $extension =
                        strtolower(
                            pathinfo(
                                $file["name"],
                                PATHINFO_EXTENSION
                            )
                        );


                    if (
                        !in_array(
                            $extension,
                            $allowed_extensions,
                            true
                        )
                    ) {

                        $error_message =
                            "Only JPG, JPEG, PNG and PDF files are allowed.";

                    } else {


                        $new_file_name =
                            "achievement_" .
                            $user_id .
                            "_" .
                            time() .
                            "_" .
                            bin2hex(random_bytes(4)) .
                            "." .
                            $extension;


                        $destination =
                            $achievement_upload_dir .
                            $new_file_name;


                        if (
                            move_uploaded_file(
                                $file["tmp_name"],
                                $destination
                            )
                        ) {

                            $certificate_file =
                                $achievement_upload_url .
                                $new_file_name;

                        } else {

                            $error_message =
                                "Unable to save the uploaded file.";

                        }

                    }

                }

            }


            /*==========================================
                     INSERT ACHIEVEMENT
            ==========================================*/

            if ($error_message === "") {


                $insert_query = "
                    INSERT INTO achievements
                    (
                        user_id,
                        achievement_title,
                        achievement_type,
                        position,
                        achievement_category,
                        organization,
                        achievement_date,
                        achievement_description,
                        achievement_skills,
                        visibility,
                        certificate_file
                    )
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ";


                $insert_stmt =
                    mysqli_prepare(
                        $conn,
                        $insert_query
                    );


                mysqli_stmt_bind_param(
                    $insert_stmt,
                    "issssssssss",
                    $user_id,
                    $achievement_title,
                    $achievement_type,
                    $position,
                    $achievement_category,
                    $organization,
                    $achievement_date,
                    $achievement_description,
                    $achievement_skills,
                    $visibility,
                    $certificate_file
                );


                if (mysqli_stmt_execute($insert_stmt)) {


                    /*==================================
                           NOTIFICATION
                    ==================================*/

                    $notification_message =
                        "New achievement added: " .
                        $achievement_title;


                    $notification_insert =
                        "INSERT INTO notifications
                         (user_id, message)
                         VALUES (?, ?)";


                    $notification_stmt =
                        mysqli_prepare(
                            $conn,
                            $notification_insert
                        );


                    mysqli_stmt_bind_param(
                        $notification_stmt,
                        "is",
                        $user_id,
                        $notification_message
                    );


                    mysqli_stmt_execute(
                        $notification_stmt
                    );

                    mysqli_stmt_close(
                        $notification_stmt
                    );


                    $success_message =
                        "Achievement added successfully.";

                } else {

                    $error_message =
                        "Unable to save the achievement.";

                }


                mysqli_stmt_close(
                    $insert_stmt
                );

            }

        }

    }


    /*==================================================
                    UPDATE ACHIEVEMENT
    ==================================================*/

    elseif ($action === "update") {


        $achievement_id =
            (int) ($_POST["achievement_id"] ?? 0);

        $achievement_title =
            trim($_POST["achievement_title"] ?? "");

        $achievement_type =
            trim($_POST["achievement_type"] ?? "");

        $position =
            trim($_POST["position"] ?? "");

        $achievement_category =
            trim($_POST["achievement_category"] ?? "");

        $organization =
            trim($_POST["organization"] ?? "");

        $achievement_date =
            $_POST["achievement_date"] ?? "";

        $achievement_description =
            trim($_POST["achievement_description"] ?? "");

        $achievement_skills =
            trim($_POST["achievement_skills"] ?? "");

        $visibility =
            $_POST["visibility"] ?? "public";


        if (
            $achievement_id <= 0 ||
            $achievement_title === "" ||
            $achievement_type === "" ||
            $organization === "" ||
            $achievement_date === "" ||
            $achievement_description === ""
        ) {

            $error_message =
                "Please fill in all required fields.";

        } else {


            /*==========================================
                GET EXISTING FILE
            ==========================================*/

            $old_file = null;


            $old_file_query = "
                SELECT certificate_file
                FROM achievements
                WHERE achievement_id = ?
                AND user_id = ?
            ";


            $old_file_stmt =
                mysqli_prepare(
                    $conn,
                    $old_file_query
                );


            mysqli_stmt_bind_param(
                $old_file_stmt,
                "ii",
                $achievement_id,
                $user_id
            );


            mysqli_stmt_execute(
                $old_file_stmt
            );


            $old_file_result =
                mysqli_stmt_get_result(
                    $old_file_stmt
                );


            $old_file_data =
                mysqli_fetch_assoc(
                    $old_file_result
                );


            if ($old_file_data) {

                $old_file =
                    $old_file_data["certificate_file"];

            }


            mysqli_stmt_close(
                $old_file_stmt
            );


            $certificate_file =
                $old_file;


            /*==========================================
                     NEW FILE UPLOAD
            ==========================================*/

            if (
                isset($_FILES["achievement_proof"]) &&
                $_FILES["achievement_proof"]["error"]
                    !== UPLOAD_ERR_NO_FILE
            ) {


                $file = $_FILES["achievement_proof"];


                if ($file["error"] !== UPLOAD_ERR_OK) {

                    $error_message =
                        "There was a problem uploading the file.";

                } elseif ($file["size"] > 5 * 1024 * 1024) {

                    $error_message =
                        "File size must not exceed 5 MB.";

                } else {


                    $allowed_extensions = [
                        "jpg",
                        "jpeg",
                        "png",
                        "pdf"
                    ];


                    $extension =
                        strtolower(
                            pathinfo(
                                $file["name"],
                                PATHINFO_EXTENSION
                            )
                        );


                    if (
                        !in_array(
                            $extension,
                            $allowed_extensions,
                            true
                        )
                    ) {

                        $error_message =
                            "Only JPG, JPEG, PNG and PDF files are allowed.";

                    } else {


                        $new_file_name =
                            "achievement_" .
                            $user_id .
                            "_" .
                            time() .
                            "_" .
                            bin2hex(random_bytes(4)) .
                            "." .
                            $extension;


                        $destination =
                            $achievement_upload_dir .
                            $new_file_name;


                        if (
                            move_uploaded_file(
                                $file["tmp_name"],
                                $destination
                            )
                        ) {


                            $certificate_file =
                                $achievement_upload_url .
                                $new_file_name;


                            /*==========================
                              DELETE OLD FILE
                            ==========================*/

                            if (
                                $old_file &&
                                strpos(
                                    $old_file,
                                    $achievement_upload_url
                                ) === 0
                            ) {

                                $old_file_path =
                                    __DIR__ .
                                    "/" .
                                    $old_file;


                                if (
                                    file_exists(
                                        $old_file_path
                                    )
                                ) {

                                    unlink(
                                        $old_file_path
                                    );

                                }

                            }

                        } else {

                            $error_message =
                                "Unable to save the uploaded file.";

                        }

                    }

                }

            }


            /*==========================================
                     UPDATE DATABASE
            ==========================================*/

            if ($error_message === "") {


                $update_query = "
                    UPDATE achievements
                    SET
                        achievement_title = ?,
                        achievement_type = ?,
                        position = ?,
                        achievement_category = ?,
                        organization = ?,
                        achievement_date = ?,
                        achievement_description = ?,
                        achievement_skills = ?,
                        visibility = ?,
                        certificate_file = ?
                    WHERE achievement_id = ?
                    AND user_id = ?
                ";


                $update_stmt =
                    mysqli_prepare(
                        $conn,
                        $update_query
                    );


                mysqli_stmt_bind_param(
                    $update_stmt,
                    "ssssssssssii",
                    $achievement_title,
                    $achievement_type,
                    $position,
                    $achievement_category,
                    $organization,
                    $achievement_date,
                    $achievement_description,
                    $achievement_skills,
                    $visibility,
                    $certificate_file,
                    $achievement_id,
                    $user_id
                );


                if (
                    mysqli_stmt_execute(
                        $update_stmt
                    )
                ) {

                    $success_message =
                        "Achievement updated successfully.";

                } else {

                    $error_message =
                        "Unable to update the achievement.";

                }


                mysqli_stmt_close(
                    $update_stmt
                );

            }

        }

    }


    /*==================================================
                    DELETE ACHIEVEMENT
    ==================================================*/

    elseif ($action === "delete") {


        $achievement_id =
            (int) ($_POST["achievement_id"] ?? 0);


        if ($achievement_id <= 0) {

            $error_message =
                "Invalid achievement.";

        } else {


            /*==========================================
                     GET FILE BEFORE DELETE
            ==========================================*/

            $file_query = "
                SELECT certificate_file
                FROM achievements
                WHERE achievement_id = ?
                AND user_id = ?
            ";


            $file_stmt =
                mysqli_prepare(
                    $conn,
                    $file_query
                );


            mysqli_stmt_bind_param(
                $file_stmt,
                "ii",
                $achievement_id,
                $user_id
            );


            mysqli_stmt_execute(
                $file_stmt
            );


            $file_result =
                mysqli_stmt_get_result(
                    $file_stmt
                );


            $file_data =
                mysqli_fetch_assoc(
                    $file_result
                );


            mysqli_stmt_close(
                $file_stmt
            );


            /*==========================================
                     DELETE DATABASE RECORD
            ==========================================*/

            $delete_query = "
                DELETE FROM achievements
                WHERE achievement_id = ?
                AND user_id = ?
            ";


            $delete_stmt =
                mysqli_prepare(
                    $conn,
                    $delete_query
                );


            mysqli_stmt_bind_param(
                $delete_stmt,
                "ii",
                $achievement_id,
                $user_id
            );


            if (
                mysqli_stmt_execute(
                    $delete_stmt
                )
            ) {


                /*======================================
                         DELETE UPLOADED FILE
                ======================================*/

                if (
                    !empty(
                        $file_data["certificate_file"]
                    )
                ) {


                    $file_path =
                        __DIR__ .
                        "/" .
                        $file_data["certificate_file"];


                    if (file_exists($file_path)) {

                        unlink($file_path);

                    }

                }


                $success_message =
                    "Achievement deleted successfully.";

            } else {

                $error_message =
                    "Unable to delete the achievement.";

            }


            mysqli_stmt_close(
                $delete_stmt
            );

        }

    }

}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if ($success_message !== "") {
        $_SESSION["achievement_success"] = $success_message;
    }

    if ($error_message !== "") {
        $_SESSION["achievement_error"] = $error_message;
    }

    header("Location: achievements.php");
    exit;
}


/*==================================================
              SUMMARY COUNTS
==================================================*/

$count_query = "
    SELECT
        COUNT(*) AS total_achievements,
        SUM(
            achievement_type = 'award'
        ) AS total_awards,
        SUM(
            achievement_type = 'competition'
        ) AS total_competitions,
        SUM(
            achievement_type = 'recognition'
        ) AS total_recognitions
    FROM achievements
    WHERE user_id = ?
";


$count_stmt =
    mysqli_prepare(
        $conn,
        $count_query
    );


mysqli_stmt_bind_param(
    $count_stmt,
    "i",
    $user_id
);


mysqli_stmt_execute(
    $count_stmt
);


$count_result =
    mysqli_stmt_get_result(
        $count_stmt
    );


$count_data =
    mysqli_fetch_assoc(
        $count_result
    );


$total_achievements =
    (int) ($count_data["total_achievements"] ?? 0);

$total_awards =
    (int) ($count_data["total_awards"] ?? 0);

$total_competitions =
    (int) ($count_data["total_competitions"] ?? 0);

$total_recognitions =
    (int) ($count_data["total_recognitions"] ?? 0);


mysqli_stmt_close(
    $count_stmt
);


/*==================================================
              LOAD NOTIFICATIONS
==================================================*/

$notifications_query = "
    SELECT
        message,
        created_at
    FROM notifications
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 10
";

$notifications_stmt =
    mysqli_prepare(
        $conn,
        $notifications_query
    );

mysqli_stmt_bind_param(
    $notifications_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $notifications_stmt
);

$notifications_result =
    mysqli_stmt_get_result(
        $notifications_stmt
    );

$notifications = [];

while (
    $notification =
        mysqli_fetch_assoc(
            $notifications_result
        )
) {

    $notifications[] = $notification;

}

mysqli_stmt_close(
    $notifications_stmt
);


/*==================================================
              LOAD ACHIEVEMENTS
==================================================*/

$achievements_query = "
    SELECT
        achievement_id,
        achievement_title,
        achievement_type,
        position,
        achievement_category,
        organization,
        achievement_date,
        achievement_description,
        achievement_skills,
        visibility,
        certificate_file
    FROM achievements
    WHERE user_id = ?
    ORDER BY achievement_date DESC, achievement_id DESC
";


$achievements_stmt =
    mysqli_prepare(
        $conn,
        $achievements_query
    );


mysqli_stmt_bind_param(
    $achievements_stmt,
    "i",
    $user_id
);


mysqli_stmt_execute(
    $achievements_stmt
);


$achievements_result =
    mysqli_stmt_get_result(
        $achievements_stmt
    );

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <meta name="description"
          content="Digital Skill Passport - Achievements">

    <title>Achievements | Digital Skill Passport</title>


    <!--==================================================
                        GOOGLE FONT
    ===================================================-->

    <link rel="preconnect"
          href="https://fonts.googleapis.com">

    <link rel="preconnect"
          href="https://fonts.gstatic.com"
          crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
          rel="stylesheet">


    <!--==================================================
                        FONT AWESOME
    ===================================================-->

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!--==================================================
                     ACHIEVEMENTS CSS
    ===================================================-->

    <link rel="stylesheet"
          href="css/achievements.css">

</head>


<body>



<?php if (!empty($success_message)): ?>
    <div class="success-message">
        <?php echo htmlspecialchars($success_message); ?>
    </div>
<?php endif; ?>

<?php if (!empty($error_message)): ?>
    <div class="error-message">
        <?php echo htmlspecialchars($error_message); ?>
    </div>
<?php endif; ?>




<!--==================================================
                 MAIN PAGE CONTAINER
===================================================-->

<div class="achievements-container">



    <!--==================================================
                         SIDEBAR
    ===================================================-->

    <aside class="sidebar"
           id="sidebar">


        <!-- Logo -->

        <div class="logo">

            <i class="fa-solid fa-passport"></i>

            <h2>

                Skill Passport

            </h2>

        </div>



        <!-- Navigation -->

        <ul class="menu">


            <!-- Dashboard -->

            <li>

                <a href="dashboard.php">

                    <i class="fa-solid fa-chart-pie"></i>

                    <span>

                        Dashboard

                    </span>

                </a>

            </li>



            <!-- Profile -->

            <li>

                <a href="profile.php">

                    <i class="fa-solid fa-user"></i>

                    <span>

                        Profile

                    </span>

                </a>

            </li>



            <!-- Skills -->

            <li>

                <a href="skills.php">

                    <i class="fa-solid fa-star"></i>

                    <span>

                        Skills

                    </span>

                </a>

            </li>



            <!-- Projects -->

            <li>

                <a href="projects.php">

                    <i class="fa-solid fa-briefcase"></i>

                    <span>

                        Projects

                    </span>

                </a>

            </li>



            <!-- Certificates -->

            <li>

                <a href="certificates.php">

                    <i class="fa-solid fa-certificate"></i>

                    <span>

                        Certificates

                    </span>

                </a>

            </li>



            <!-- Workshops -->

            <li>

                <a href="workshops.php">

                    <i class="fa-solid fa-graduation-cap"></i>

                    <span>

                        Workshops

                    </span>

                </a>

            </li>



            <!-- Internships -->

            <li>

                <a href="internships.php">

                    <i class="fa-solid fa-building"></i>

                    <span>

                        Internships

                    </span>

                </a>

            </li>



            <!-- Achievements -->

            <li class="active">

                <a href="achievements.php">

                    <i class="fa-solid fa-trophy"></i>

                    <span>

                        Achievements

                    </span>

                </a>

            </li>



            <!-- Settings -->

            <li>

                <a href="settings.php">

                    <i class="fa-solid fa-gear"></i>

                    <span>

                        Settings

                    </span>

                </a>

            </li>



            <!-- Logout -->

            <li class="logout-link">

                <a href="logout.php">

                    <i class="fa-solid fa-right-from-bracket"></i>

                    <span>

                        Logout

                    </span>

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


            <!-- Left -->

            <div class="top-left">


                <!-- Mobile Menu -->

                <button type="button"
                        class="menu-toggle"
                        id="menuToggle"
                        aria-label="Open menu">

                    <i class="fa-solid fa-bars"></i>

                </button>


                <h2>

                    Achievements

                </h2>


            </div>



            <!-- Right -->

            <div class="top-right">


                <!-- Search -->

                <div class="search-box">


                    <i class="fa-solid fa-magnifying-glass"></i>


                    <input type="text"
                           id="achievementSearch"
                           placeholder="Search achievements...">


                </div>



                <!-- Notification -->

<button type="button" 
        class="notification" 
        aria-label="Notifications"> 

    <i class="fa-regular fa-bell"></i> 

    <span>
        <?php echo $notification_count; ?>
    </span> 

</button> 
<div class="notification-popup">

    <div class="notification-popup-header">
        <h3>Notifications</h3>
    </div>

    <div class="notification-list">

    <?php if (!empty($notifications)): ?>

        <?php foreach ($notifications as $notification): ?>

            <div class="notification-item">

                <?php echo htmlspecialchars(
                    $notification["message"]
                ); ?>

            </div>

        <?php endforeach; ?>

    <?php else: ?>

        <div class="notification-empty">
            No new notifications.
        </div>

    <?php endif; ?>

</div>

</div>


<!-- User --> 

<div class="user-profile"> 


    <img src="<?php
        echo !empty($profile_photo)
            ? htmlspecialchars($profile_photo)
            : 'images/profile.jpg';
    ?>"
         alt="Profile picture"
         onerror="this.style.display='none';"> 


    <div> 

        <h4> 

            <?php echo htmlspecialchars($user_name); ?> 

        </h4> 

        <p> 

            <?php echo htmlspecialchars($user_role); ?> 

        </p> 

    </div> 


</div>


            </div>


        </header>



        <!--==================================================
                       PAGE HEADER
        ===================================================-->

        <section class="page-header">


            <!-- Header Text -->

            <div class="header-text">


                <div class="header-icon">

                    <i class="fa-solid fa-trophy"></i>

                </div>


                <div>

                    <h1>

                        My Achievements

                    </h1>


                    <p>

                        Showcase your awards, recognitions,
                        competitions and accomplishments.

                    </p>

                </div>


            </div>



            <!-- Add Button -->

            <div class="header-button">


                <button type="button"
                        class="add-achievement-btn"
                        id="addAchievementBtn">


                    <i class="fa-solid fa-plus"></i>


                    Add Achievement


                </button>


            </div>


        </section>



        <!--==================================================
                    ACHIEVEMENT SUMMARY CARDS
        ===================================================-->

        <section class="summary-cards">


            <!-- Total -->

            <div class="summary-card total-achievements">


                <div class="summary-icon">

                    <i class="fa-solid fa-trophy"></i>

                </div>


                <div>

                    <h2 id="totalAchievements">

                        <?php echo $total_achievements; ?>

                    </h2>


                    <p>

                        Total Achievements

                    </p>

                </div>


            </div>



            <!-- Awards -->

            <div class="summary-card award-achievements">


                <div class="summary-icon">

                    <i class="fa-solid fa-medal"></i>

                </div>


                <div>

                    <h2 id="totalAwards">

                            <?php echo $total_awards; ?>

                    </h2>


                    <p>

                        Awards & Honors

                    </p>

                </div>


            </div>



            <!-- Competitions -->

            <div class="summary-card competition-achievements">


                <div class="summary-icon">

                    <i class="fa-solid fa-ranking-star"></i>

                </div>


                <div>

                    <h2 id="totalCompetitions">

                        <?php echo $total_competitions; ?>

                    </h2>


                    <p>

                        Competitions

                    </p>

                </div>


            </div>



            <!-- Recognitions -->

            <div class="summary-card recognition-achievements">


                <div class="summary-icon">

                    <i class="fa-solid fa-award"></i>

                </div>


                <div>

                    <h2 id="totalRecognitions">

                        <?php echo $total_recognitions; ?>

                    </h2>


                    <p>

                        Recognitions

                    </p>

                </div>


            </div>


        </section>



        <!--==================================================
                 ACHIEVEMENT FORM STARTS IN PART 1B
        ===================================================-->
                <!--==================================================
                  ADD ACHIEVEMENT FORM
        ===================================================-->

        <section class="achievement-form-section"
                 id="achievementFormSection">


            <div class="form-card">


                <!--==================================================
                         FORM CARD HEADER
                ===================================================-->

                <div class="card-header">


                    <div class="card-header-icon">

                        <i class="fa-solid fa-trophy"></i>

                    </div>


                    <div>

                        <h2 id="achievementFormTitle">

    Add New Achievement

</h2>


                        <p>

                            Add your awards, recognitions,
                            competitions and other accomplishments.

                        </p>

                    </div>


                </div>



                <!--==================================================
                           FORM
                ===================================================-->

                <form id="achievementForm" 
      method="POST" 
      action="" 
      enctype="multipart/form-data">

    <input type="hidden"
           name="action"
           value="add">

    <input type="hidden"
           name="achievement_id"
           id="achievementId"
           value="">



                    <!--==================================================
                              BASIC INFORMATION
                    ===================================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-circle-info"></i>

                        <span>

                            Achievement Information

                        </span>

                    </div>



                    <div class="form-grid">



                        <!-- Achievement Title -->

                        <div class="form-group">


                            <label for="achievementTitle">

                                Achievement Title

                                <span>*</span>

                            </label>


                            <div class="input-with-icon">

                                <i class="fa-solid fa-heading"></i>

                                <input type="text"
                                       id="achievementTitle"
                                       name="achievement_title"
                                       placeholder="e.g. First Prize in Coding Competition"
                                       required>

                            </div>


                        </div>



                        <!-- Achievement Type -->

                        <div class="form-group">


                            <label for="achievementType">

                                Achievement Type

                                <span>*</span>

                            </label>


                            <select id="achievementType"
                                    name="achievement_type"
                                    required>

                                <option value="">

                                    Select achievement type

                                </option>

                                <option value="award">

                                    Award / Honor

                                </option>

                                <option value="competition">

                                    Competition

                                </option>

                                <option value="recognition">

                                    Recognition

                                </option>

                                <option value="academic">

                                    Academic Achievement

                                </option>

                                <option value="leadership">

                                    Leadership

                                </option>

                                <option value="sports">

                                    Sports

                                </option>

                                <option value="other">

                                    Other

                                </option>

                            </select>


                        </div>



                        <!-- Organization -->

                        <div class="form-group">


                            <label for="organization">

                                Issuing Organization

                                <span>*</span>

                            </label>


                            <div class="input-with-icon">

                                <i class="fa-solid fa-building"></i>

                                <input type="text"
                                       id="organization"
                                       name="organization"
                                       placeholder="e.g. College / University / Organization"
                                       required>

                            </div>


                        </div>



                        <!-- Achievement Date -->

                        <div class="form-group">


                            <label for="achievementDate">

                                Date

                                <span>*</span>

                            </label>


                            <div class="input-with-icon">

                                <i class="fa-regular fa-calendar"></i>

                                <input type="date"
                                       id="achievementDate"
                                       name="achievement_date"
                                       required>

                            </div>


                        </div>



                        <!-- Position -->

                        <div class="form-group">


                            <label for="position">

                                Position / Result

                            </label>


                            <div class="input-with-icon">

                                <i class="fa-solid fa-ranking-star"></i>

                                <input type="text"
                                       id="position"
                                       name="position"
                                       placeholder="e.g. First Prize, Runner-up, Top 10">

                            </div>


                        </div>



                        <!-- Category -->

                        <div class="form-group">


                            <label for="achievementCategory">

                                Category

                            </label>


                            <select id="achievementCategory"
                                    name="achievement_category">

                                <option value="">

                                    Select category

                                </option>

                                <option value="academic">

                                    Academic

                                </option>

                                <option value="technical">

                                    Technical

                                </option>

                                <option value="professional">

                                    Professional

                                </option>

                                <option value="extracurricular">

                                    Extracurricular

                                </option>

                                <option value="sports">

                                    Sports

                                </option>

                                <option value="volunteering">

                                    Volunteering

                                </option>

                                <option value="other">

                                    Other

                                </option>

                            </select>


                        </div>


                    </div>



                    <!--==================================================
                         ACHIEVEMENT DESCRIPTION
                    ===================================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-align-left"></i>

                        <span>

                            Description

                        </span>

                    </div>



                    <div class="form-group">


                        <label for="achievementDescription">

                            Achievement Description

                            <span>*</span>

                        </label>


                        <textarea id="achievementDescription"
                                  name="achievement_description"
                                  rows="5"
                                  placeholder="Briefly describe the achievement, competition, award or recognition..."
                                  required></textarea>


                        <small class="field-note">

                            Give a short and clear description of
                            how you achieved it.

                        </small>


                    </div>



                    <!--==================================================
                           SKILLS / KNOWLEDGE
                    ===================================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-star"></i>

                        <span>

                            Related Skills

                        </span>

                    </div>



                    <div class="form-group">


                        <label for="achievementSkills">

                            Skills Demonstrated

                        </label>


                        <div class="input-with-icon">

                            <i class="fa-solid fa-tags"></i>

                            <input type="text"
                                   id="achievementSkills"
                                   name="achievement_skills"
                                   placeholder="e.g. Python, Leadership, Communication">

                        </div>


                        <small class="field-note">

                            Separate multiple skills using commas.

                        </small>


                    </div>



                    <!--==================================================
                           PROOF / CERTIFICATE UPLOAD
                    ===================================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-file-arrow-up"></i>

                        <span>

                            Achievement Proof

                        </span>

                    </div>



                    <div class="achievement-upload-area"
                         id="achievementUploadArea">


                        <div class="upload-icon">

                            <i class="fa-solid fa-cloud-arrow-up"></i>

                        </div>


                        <h3>

                            Upload Achievement Proof

                        </h3>


                        <p>

                            Upload certificate, award letter,
                            photo or other proof.

                        </p>



                        <!-- File input -->

                        <label for="achievementProof"
                               class="upload-btn">


                            <i class="fa-solid fa-upload"></i>

                            Choose File


                        </label>


                        <input type="file"
                               id="achievementProof"
                               name="achievement_proof"
                               accept=".jpg,.jpeg,.png,.pdf"
                               hidden>


                        <p class="file-info">

                            JPG, JPEG, PNG or PDF • Maximum 5 MB

                        </p>



                        <!-- File name -->

                        <div class="selected-file"
                             id="selectedAchievementFile">

                            <i class="fa-solid fa-file"></i>

                            <span>

                                No file selected

                            </span>

                        </div>



                        <!-- Image Preview -->

                        <div class="achievement-preview"
                             id="achievementPreview">


                            <img id="achievementPreviewImage"
                                 src=""
                                 alt="Achievement proof preview">


                        </div>


                    </div>



                    <!--==================================================
                         VISIBILITY SETTINGS
                    ===================================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-eye"></i>

                        <span>

                            Visibility

                        </span>

                    </div>



                    <div class="visibility-options">



                        <!-- Public -->

                        <label class="visibility-option">


                            <input type="radio"
                                   name="visibility"
                                   value="public"
                                   checked>


                            <span class="custom-radio"></span>


                            <span class="visibility-content">


                                <strong>

                                    Public

                                </strong>


                                <small>

                                    Visible on your digital skill passport.

                                </small>


                            </span>


                        </label>



                        <!-- Private -->

                        <label class="visibility-option">


                            <input type="radio"
                                   name="visibility"
                                   value="private">


                            <span class="custom-radio"></span>


                            <span class="visibility-content">


                                <strong>

                                    Private

                                </strong>


                                <small>

                                    Only you can view this achievement.

                                </small>


                            </span>


                        </label>


                    </div>



                    <!--==================================================
                           FORM BUTTONS
                    ===================================================-->

                    <div class="button-group">


                        <!-- Reset -->

                        <button type="reset"
                                class="reset-achievement-btn"
                                id="resetAchievementBtn">


                            <i class="fa-solid fa-rotate-left"></i>

                            Clear


                        </button>



                        <!-- Save -->

                        <button type="submit"
        class="save-achievement-btn"
        id="achievementSubmitBtn">


                            <i class="fa-solid fa-floppy-disk"></i>

                            Save Achievement


                        </button>


                    </div>


                </form>


            </div>


        </section>



        <!--==================================================
              SAVED ACHIEVEMENTS SECTION STARTS
              IN PART 1C
        ===================================================-->
                <!--==================================================
                  SAVED ACHIEVEMENTS SECTION
        ===================================================-->

        <section class="saved-achievements-section">


            <!--==================================================
                       SECTION HEADING
            ===================================================-->

            <div class="section-heading">


                <div>

                    <span class="section-label">

                        MY RECORD

                    </span>


                    <h2>

                        Saved Achievements

                    </h2>


                    <p>

                        View and manage your achievements,
                        awards and recognitions.

                    </p>

                </div>



                <!-- Filter -->

                <div class="achievement-filter">


                    <label for="achievementFilter">

                        <i class="fa-solid fa-filter"></i>

                        Filter:

                    </label>


                    <select id="achievementFilter">

                        <option value="all">

                            All Achievements

                        </option>

                        <option value="award">

                            Awards & Honors

                        </option>

                        <option value="competition">

                            Competitions

                        </option>

                        <option value="recognition">

                            Recognitions

                        </option>

                        <option value="academic">

                            Academic

                        </option>

                        <option value="leadership">

                            Leadership

                        </option>

                        <option value="sports">

                            Sports

                        </option>

                    </select>


                </div>


            </div>



            <!--==================================================
                       ACHIEVEMENT GRID
            ===================================================-->

            <div class="achievements-grid"
                 id="achievementsGrid">



                <?php

while ($achievement = mysqli_fetch_assoc($achievements_result)):

    $achievement_id =
        (int) $achievement["achievement_id"];

    $achievement_title =
        $achievement["achievement_title"] ?? "";

    $achievement_type =
        $achievement["achievement_type"] ?? "";

    $position =
        $achievement["position"] ?? "";

    $achievement_category =
        $achievement["achievement_category"] ?? "";

    $organization =
        $achievement["organization"] ?? "";

    $achievement_date =
        $achievement["achievement_date"] ?? "";

    $achievement_description =
        $achievement["achievement_description"] ?? "";

    $achievement_skills =
        $achievement["achievement_skills"] ?? "";

    $visibility =
        $achievement["visibility"] ?? "public";


    /*==============================================
                 DISPLAY LABEL
    ==============================================*/

    $type_labels = [
        "award" => "AWARD",
        "competition" => "COMPETITION",
        "recognition" => "RECOGNITION",
        "academic" => "ACADEMIC",
        "leadership" => "LEADERSHIP",
        "sports" => "SPORTS",
        "other" => "OTHER"
    ];


    $type_label =
        $type_labels[$achievement_type]
        ?? strtoupper($achievement_type);


    /*==============================================
                 BADGE ICON
    ==============================================*/

    $type_icons = [
        "award" => "fa-trophy",
        "competition" => "fa-medal",
        "recognition" => "fa-award",
        "academic" => "fa-graduation-cap",
        "leadership" => "fa-star",
        "sports" => "fa-futbol",
        "other" => "fa-trophy"
    ];


    $type_icon =
        $type_icons[$achievement_type]
        ?? "fa-trophy";


    /*==============================================
                 BADGE COLOR
    ==============================================*/

    $type_colors = [
        "award" => "purple",
        "competition" => "",
        "recognition" => "cyan",
        "academic" => "purple",
        "leadership" => "orange",
        "sports" => "cyan",
        "other" => ""
    ];


    $badge_color =
        $type_colors[$achievement_type]
        ?? "";


    /*==============================================
                 DATE FORMAT
    ==============================================*/

    $formatted_date = "";

    if (!empty($achievement_date)) {

        $formatted_date =
            date(
                "F d, Y",
                strtotime($achievement_date)
            );

    }


    /*==============================================
                 SEARCH DATA
    ==============================================*/

    $search_title =
        htmlspecialchars(
            strtolower($achievement_title),
            ENT_QUOTES,
            "UTF-8"
        );

    $search_type =
        htmlspecialchars(
            strtolower($achievement_type),
            ENT_QUOTES,
            "UTF-8"
        );

    $search_category =
        htmlspecialchars(
            strtolower($achievement_category),
            ENT_QUOTES,
            "UTF-8"
        );
        ?>
        
    <article class="achievement-card"
         data-id="<?php echo $achievement_id; ?>"
         data-type="<?php echo $search_type; ?>"
         data-category="<?php echo $search_category; ?>"
         data-title="<?php echo $search_title; ?>"
         data-date="<?php echo htmlspecialchars($achievement_date, ENT_QUOTES, 'UTF-8'); ?>"
         data-visibility="<?php echo htmlspecialchars($visibility, ENT_QUOTES, 'UTF-8'); ?>"
         data-certificate="<?php echo htmlspecialchars($certificate_file, ENT_QUOTES, 'UTF-8'); ?>">

    <!--==================================================
                     ACHIEVEMENT CARD TOP
    ===================================================-->

    <div class="achievement-card-top">


        <div class="achievement-badge">

            <div class="badge-icon <?php echo $badge_color; ?>">

                <i class="fa-solid <?php echo $type_icon; ?>"></i>

            </div>


            <div>

                <span class="achievement-label">

                    <?php echo htmlspecialchars($type_label); ?>

                </span>


                <?php if ($position !== ""): ?>

                    <span class="achievement-rank">

                        <?php
                        echo htmlspecialchars($position);
                        ?>

                    </span>

                <?php endif; ?>

            </div>

        </div>


        <!-- Actions -->

        <div class="card-actions">


            <button type="button"
                    class="card-action edit-achievement"
                    title="Edit Achievement"
                    data-id="<?php echo $achievement_id; ?>">

                <i class="fa-solid fa-pen"></i>

            </button>


            <button type="button"
                    class="card-action delete-achievement"
                    title="Delete Achievement"
                    data-id="<?php echo $achievement_id; ?>">

                <i class="fa-solid fa-trash"></i>

            </button>


        </div>

    </div>


    <!--==================================================
                     CARD CONTENT
    ===================================================-->

    <div class="achievement-card-content">


        <h3>

            <?php
            echo htmlspecialchars(
                $achievement_title
            );
            ?>

        </h3>


        <div class="achievement-organization">

            <i class="fa-solid fa-building"></i>

            <span>

                <?php
                echo htmlspecialchars(
                    $organization
                );
                ?>

            </span>

        </div>


        <div class="achievement-date">

            <i class="fa-regular fa-calendar"></i>

            <span>

                <?php
                echo htmlspecialchars(
                    $formatted_date
                );
                ?>

            </span>

        </div>


        <p class="achievement-description">

            <?php
            echo htmlspecialchars(
                $achievement_description
            );
            ?>

        </p>


        <!-- Skills -->

        <?php if ($achievement_skills !== ""): ?>

            <div class="achievement-skills">

                <?php

                $skills =
                    explode(
                        ",",
                        $achievement_skills
                    );

                foreach ($skills as $skill):

                    $skill = trim($skill);

                    if ($skill === "") {
                        continue;
                    }

                ?>

                    <span>

                        <?php
                        echo htmlspecialchars(
                            $skill
                        );
                        ?>

                    </span>

                <?php endforeach; ?>

            </div>

        <?php endif; ?>


    </div>


    <!--==================================================
                     CARD BOTTOM
    ===================================================-->

    <div class="achievement-card-bottom">


        <span class="achievement-status">

            <i class="fa-solid fa-circle-check"></i>

            <?php
            echo $visibility === "public"
                ? "Public Achievement"
                : "Private Achievement";
            ?>

        </span>


        <button type="button"
                class="view-achievement"
                data-id="<?php echo $achievement_id; ?>">

            View Details

            <i class="fa-solid fa-arrow-right"></i>

        </button>


    </div>


</article>


<?php endwhile; ?>
</div>



            <!--==================================================
                         EMPTY STATE
            ===================================================-->

            <div class="no-achievements"
                 id="noAchievements"
                 style="display: none;">


                <div class="empty-icon">

                    <i class="fa-solid fa-trophy"></i>

                </div>


                <h3>

                    No Achievements Found

                </h3>


                <p>

                    You haven't added any achievements
                    matching your search or filter.

                </p>


                <button type="button"
                        class="empty-add-btn"
                        id="emptyAddAchievementBtn">


                    <i class="fa-solid fa-plus"></i>

                    Add Achievement


                </button>


            </div>


        </section>



        <!--==================================================
                  ACHIEVEMENT DETAILS MODAL
        ===================================================-->

        <div class="achievement-modal"
             id="achievementModal">


            <!-- Overlay -->

            <div class="modal-overlay"></div>



            <!-- Modal Container -->

            <div class="modal-container">


                <!-- Modal Header -->

                <div class="modal-header">


                    <div class="modal-title-area">


                        <div class="modal-icon">

                            <i class="fa-solid fa-trophy"></i>

                        </div>


                        <div>

                            <span>

                                ACHIEVEMENT DETAILS

                            </span>


                            <h2 id="modalAchievementTitle">

                                Achievement

                            </h2>

                        </div>


                    </div>



                    <button type="button"
                            class="close-modal"
                            id="closeAchievementModal"
                            aria-label="Close">


                        <i class="fa-solid fa-xmark"></i>


                    </button>


                </div>



                <!-- Modal Body -->

                <div class="modal-body"
                     id="modalAchievementContent">


                    <div class="modal-detail-grid">


                        <div class="modal-detail-item">

                            <i class="fa-solid fa-building"></i>

                            <strong>

                                Organization

                            </strong>

                            <span id="modalOrganization">

                                ABC College of Technology

                            </span>

                        </div>



                        <div class="modal-detail-item">

                            <i class="fa-regular fa-calendar"></i>

                            <strong>

                                Date

                            </strong>

                            <span id="modalDate">

                                March 15, 2026

                            </span>

                        </div>



                        <div class="modal-detail-item">

                            <i class="fa-solid fa-ranking-star"></i>

                            <strong>

                                Result

                            </strong>

                            <span id="modalPosition">

                                1st Prize

                            </span>

                        </div>



                        <div class="modal-detail-item">

                            <i class="fa-solid fa-tag"></i>

                            <strong>

                                Type

                            </strong>

                            <span id="modalType">

                                Competition

                            </span>

                        </div>


                    </div>



                    <!-- Description -->

                    <div class="modal-description">


                        <h3>

                            Description

                        </h3>


                        <p id="modalDescription">

                            Achievement description will
                            appear here.

                        </p>


                    </div>



                    <!-- Skills -->

                    <div class="modal-skills">


                        <h3>

                            Skills Demonstrated

                        </h3>


                        <div class="achievement-skills"
                             id="modalSkills">


                            <span>

                                Python

                            </span>


                            <span>

                                Problem Solving

                            </span>


                        </div>


                    </div>


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
                     JAVASCRIPT
===================================================-->

<script src="js/achievements.js"></script>


</body>

</html>