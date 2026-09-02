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

                    <span>3</span>

                </button>



                <!-- User -->

                <div class="user-profile">


                    <img src="images/profile.jpg"
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

                        0

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

                        0

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

                        0

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

                        0

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

                        <h2>

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
                                class="save-achievement-btn">


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



                <!--==================================================
                         ACHIEVEMENT CARD 1
                ===================================================-->

                <article class="achievement-card"
                         data-type="competition"
                         data-category="technical"
                         data-title="Coding Competition">


                    <!-- Card Top -->

                    <div class="achievement-card-top">


                        <div class="achievement-badge">


                            <div class="badge-icon">

                                <i class="fa-solid fa-medal"></i>

                            </div>


                            <div>

                                <span class="achievement-label">

                                    COMPETITION

                                </span>


                                <span class="achievement-rank">

                                    1st Prize

                                </span>

                            </div>


                        </div>



                        <!-- Actions -->

                        <div class="card-actions">


                            <button type="button"
                                    class="card-action edit-achievement"
                                    title="Edit Achievement">


                                <i class="fa-solid fa-pen"></i>


                            </button>


                            <button type="button"
                                    class="card-action delete-achievement"
                                    title="Delete Achievement">


                                <i class="fa-solid fa-trash"></i>


                            </button>


                        </div>


                    </div>



                    <!-- Card Content -->

                    <div class="achievement-card-content">


                        <h3>

                            Coding Competition

                        </h3>


                        <div class="achievement-organization">


                            <i class="fa-solid fa-building"></i>


                            <span>

                                ABC College of Technology

                            </span>


                        </div>



                        <div class="achievement-date">


                            <i class="fa-regular fa-calendar"></i>


                            <span>

                                March 15, 2026

                            </span>


                        </div>



                        <p class="achievement-description">

                            Secured first position in the
                            inter-college coding competition by
                            developing an innovative software solution.

                        </p>



                        <!-- Skills -->

                        <div class="achievement-skills">


                            <span>Python</span>

                            <span>Problem Solving</span>

                            <span>Programming</span>


                        </div>


                    </div>



                    <!-- Card Bottom -->

                    <div class="achievement-card-bottom">


                        <span class="achievement-status">


                            <i class="fa-solid fa-circle-check"></i>

                            Verified Achievement


                        </span>



                        <button type="button"
                                class="view-achievement"
                                data-title="Coding Competition">


                            View Details

                            <i class="fa-solid fa-arrow-right"></i>


                        </button>


                    </div>


                </article>



                <!--==================================================
                         ACHIEVEMENT CARD 2
                ===================================================-->

                <article class="achievement-card"
                         data-type="award"
                         data-category="academic"
                         data-title="Academic Excellence Award">


                    <!-- Card Top -->

                    <div class="achievement-card-top">


                        <div class="achievement-badge">


                            <div class="badge-icon purple">

                                <i class="fa-solid fa-trophy"></i>

                            </div>


                            <div>

                                <span class="achievement-label">

                                    AWARD

                                </span>


                                <span class="achievement-rank">

                                    Excellence Award

                                </span>

                            </div>


                        </div>



                        <div class="card-actions">


                            <button type="button"
                                    class="card-action edit-achievement"
                                    title="Edit Achievement">


                                <i class="fa-solid fa-pen"></i>


                            </button>


                            <button type="button"
                                    class="card-action delete-achievement"
                                    title="Delete Achievement">


                                <i class="fa-solid fa-trash"></i>


                            </button>


                        </div>


                    </div>



                    <!-- Content -->

                    <div class="achievement-card-content">


                        <h3>

                            Academic Excellence Award

                        </h3>


                        <div class="achievement-organization">


                            <i class="fa-solid fa-building"></i>


                            <span>

                                ABC University

                            </span>


                        </div>


                        <div class="achievement-date">


                            <i class="fa-regular fa-calendar"></i>


                            <span>

                                January 20, 2026

                            </span>


                        </div>


                        <p class="achievement-description">

                            Received an academic excellence award
                            for maintaining outstanding academic
                            performance.

                        </p>


                        <div class="achievement-skills">


                            <span>Academic</span>

                            <span>Research</span>

                            <span>Consistency</span>


                        </div>


                    </div>



                    <!-- Bottom -->

                    <div class="achievement-card-bottom">


                        <span class="achievement-status">


                            <i class="fa-solid fa-circle-check"></i>

                            Verified Achievement


                        </span>


                        <button type="button"
                                class="view-achievement"
                                data-title="Academic Excellence Award">


                            View Details

                            <i class="fa-solid fa-arrow-right"></i>


                        </button>


                    </div>


                </article>



                <!--==================================================
                         ACHIEVEMENT CARD 3
                ===================================================-->

                <article class="achievement-card"
                         data-type="recognition"
                         data-category="professional"
                         data-title="Best Project Recognition">


                    <div class="achievement-card-top">


                        <div class="achievement-badge">


                            <div class="badge-icon cyan">

                                <i class="fa-solid fa-award"></i>

                            </div>


                            <div>

                                <span class="achievement-label">

                                    RECOGNITION

                                </span>


                                <span class="achievement-rank">

                                    Best Project

                                </span>

                            </div>


                        </div>



                        <div class="card-actions">


                            <button type="button"
                                    class="card-action edit-achievement"
                                    title="Edit Achievement">


                                <i class="fa-solid fa-pen"></i>


                            </button>


                            <button type="button"
                                    class="card-action delete-achievement"
                                    title="Delete Achievement">


                                <i class="fa-solid fa-trash"></i>


                            </button>


                        </div>


                    </div>



                    <div class="achievement-card-content">


                        <h3>

                            Best Project Recognition

                        </h3>


                        <div class="achievement-organization">


                            <i class="fa-solid fa-building"></i>


                            <span>

                                Department of Computer Science

                            </span>


                        </div>


                        <div class="achievement-date">


                            <i class="fa-regular fa-calendar"></i>


                            <span>

                                December 10, 2025

                            </span>


                        </div>


                        <p class="achievement-description">

                            Recognized for developing an innovative
                            project that demonstrated practical
                            application of computer science concepts.

                        </p>


                        <div class="achievement-skills">


                            <span>Web Development</span>

                            <span>PHP</span>

                            <span>MySQL</span>


                        </div>


                    </div>



                    <div class="achievement-card-bottom">


                        <span class="achievement-status">


                            <i class="fa-solid fa-circle-check"></i>

                            Verified Achievement


                        </span>


                        <button type="button"
                                class="view-achievement"
                                data-title="Best Project Recognition">


                            View Details

                            <i class="fa-solid fa-arrow-right"></i>


                        </button>


                    </div>


                </article>



                <!--==================================================
                         ACHIEVEMENT CARD 4
                ===================================================-->

                <article class="achievement-card"
                         data-type="leadership"
                         data-category="extracurricular"
                         data-title="Student Leadership Award">


                    <div class="achievement-card-top">


                        <div class="achievement-badge">


                            <div class="badge-icon orange">

                                <i class="fa-solid fa-star"></i>

                            </div>


                            <div>

                                <span class="achievement-label">

                                    LEADERSHIP

                                </span>


                                <span class="achievement-rank">

                                    Outstanding Leader

                                </span>

                            </div>


                        </div>



                        <div class="card-actions">


                            <button type="button"
                                    class="card-action edit-achievement"
                                    title="Edit Achievement">


                                <i class="fa-solid fa-pen"></i>


                            </button>


                            <button type="button"
                                    class="card-action delete-achievement"
                                    title="Delete Achievement">


                                <i class="fa-solid fa-trash"></i>


                            </button>


                        </div>


                    </div>



                    <div class="achievement-card-content">


                        <h3>

                            Student Leadership Award

                        </h3>


                        <div class="achievement-organization">


                            <i class="fa-solid fa-building"></i>


                            <span>

                                Computer Science Association

                            </span>


                        </div>


                        <div class="achievement-date">


                            <i class="fa-regular fa-calendar"></i>


                            <span>

                                November 05, 2025

                            </span>


                        </div>


                        <p class="achievement-description">

                            Received recognition for demonstrating
                            leadership, teamwork and effective
                            communication while coordinating student
                            activities.

                        </p>


                        <div class="achievement-skills">


                            <span>Leadership</span>

                            <span>Teamwork</span>

                            <span>Communication</span>


                        </div>


                    </div>



                    <div class="achievement-card-bottom">


                        <span class="achievement-status">


                            <i class="fa-solid fa-circle-check"></i>

                            Verified Achievement


                        </span>


                        <button type="button"
                                class="view-achievement"
                                data-title="Student Leadership Award">


                            View Details

                            <i class="fa-solid fa-arrow-right"></i>


                        </button>


                    </div>


                </article>


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