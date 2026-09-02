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
                               placeholder="Search internships..."
                               aria-label="Search internships">

                    </div>



                    <!-- Notification -->

                    <button class="notification"
                            type="button"
                            aria-label="Notifications">

                        <i class="fa-regular fa-bell"></i>

                        <span>3</span>

                    </button>



                    <!-- User -->

                    <div class="user-profile">

                        <img src="images/profile.jpg"
                             alt="Profile">

                        <div>

                            <h4>My Profile</h4>

                            <p>Student</p>

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

                        <h2 id="totalInternshipCount">

                            4

                        </h2>


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

                        <h2 id="completedInternshipCount">

                            3

                        </h2>


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

                        <h2 id="ongoingInternshipCount">

                            1

                        </h2>


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

                            12

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

                            <h2>

                                Add Internship

                            </h2>


                            <p>

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



                        <!-- Internship URL -->

                        <div class="form-group">

                            <label for="internshipUrl">

                                Internship / Program URL

                            </label>

                            <div class="input-with-icon">

                                <i class="fa-solid fa-link"></i>

                                <input type="url"
                                       id="internshipUrl"
                                       name="internship_url"
                                       placeholder="https://example.com/internship">

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
                         KEY ACHIEVEMENTS
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-trophy"></i>

                        Internship Achievements

                    </div>


                    <div class="form-group">

                        <label for="internshipAchievements">

                            Key Achievements

                        </label>

                        <textarea id="internshipAchievements"
                                  name="achievements"
                                  rows="4"
                                  maxlength="800"
                                  placeholder="Mention important achievements, contributions or results achieved during the internship..."></textarea>

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
                         INTERNSHIP VISIBILITY
                    =========================================-->

                    <div class="form-section-title">

                        <i class="fa-solid fa-eye"></i>

                        Internship Visibility

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

                                    Show this internship on
                                    your digital skill passport.

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

                                    Keep this internship hidden
                                    from your public profile.

                                </small>

                            </span>


                        </label>


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
                                class="reset-internship-btn">

                            <i class="fa-solid fa-rotate-right"></i>

                            Reset

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



                    <!--==============================================
                              INTERNSHIP CARD 1
                    ===============================================-->

                    <article class="internship-card"
                             data-status="completed"
                             data-type="technical">


                        <!-- Card Top -->

                        <div class="internship-card-top">


                            <div class="company-logo">

                                <i class="fa-solid fa-building"></i>

                            </div>


                            <div class="card-actions">


                                <button type="button"
                                        class="card-action edit-internship"
                                        title="Edit Internship">

                                    <i class="fa-solid fa-pen"></i>

                                </button>


                                <button type="button"
                                        class="card-action delete-internship"
                                        title="Delete Internship">

                                    <i class="fa-solid fa-trash"></i>

                                </button>


                            </div>


                        </div>



                        <!-- Internship Details -->

                        <div class="internship-card-content">


                            <span class="internship-type">

                                Technical Internship

                            </span>


                            <h3>

                                Web Development Intern

                            </h3>


                            <p class="company-name">

                                <i class="fa-solid fa-building"></i>

                                ABC Technologies

                            </p>


                            <p class="internship-role">

                                <i class="fa-solid fa-user-tie"></i>

                                Web Development Intern

                            </p>



                            <!-- Dates -->

                            <div class="internship-meta">


                                <span>

                                    <i class="fa-regular fa-calendar"></i>

                                    Jan 2026 - Mar 2026

                                </span>


                                <span>

                                    <i class="fa-regular fa-clock"></i>

                                    3 Months

                                </span>


                            </div>



                            <!-- Location -->

                            <div class="internship-location">

                                <i class="fa-solid fa-location-dot"></i>

                                Kochi, Kerala

                                <span class="mode-badge">

                                    On-site

                                </span>

                            </div>



                            <!-- Skills -->

                            <div class="internship-skills">


                                <span>HTML</span>

                                <span>CSS</span>

                                <span>JavaScript</span>

                                <span>PHP</span>


                            </div>



                        </div>



                        <!-- Card Bottom -->

                        <div class="internship-card-bottom">


                            <span class="status-badge completed">

                                <i class="fa-solid fa-circle-check"></i>

                                Completed

                            </span>


                            <button type="button"
                                    class="view-internship">

                                View Details

                                <i class="fa-solid fa-arrow-right"></i>

                            </button>


                        </div>


                    </article>



                    <!--==============================================
                              INTERNSHIP CARD 2
                    ===============================================-->

                    <article class="internship-card"
                             data-status="completed"
                             data-type="data-science">


                        <div class="internship-card-top">


                            <div class="company-logo purple">

                                <i class="fa-solid fa-chart-line"></i>

                            </div>


                            <div class="card-actions">


                                <button type="button"
                                        class="card-action edit-internship"
                                        title="Edit Internship">

                                    <i class="fa-solid fa-pen"></i>

                                </button>


                                <button type="button"
                                        class="card-action delete-internship"
                                        title="Delete Internship">

                                    <i class="fa-solid fa-trash"></i>

                                </button>


                            </div>


                        </div>



                        <div class="internship-card-content">


                            <span class="internship-type">

                                Data Science

                            </span>


                            <h3>

                                Data Science Intern

                            </h3>


                            <p class="company-name">

                                <i class="fa-solid fa-building"></i>

                                DataTech Solutions

                            </p>


                            <p class="internship-role">

                                <i class="fa-solid fa-user-tie"></i>

                                Data Science Intern

                            </p>



                            <div class="internship-meta">


                                <span>

                                    <i class="fa-regular fa-calendar"></i>

                                    Apr 2026 - Jun 2026

                                </span>


                                <span>

                                    <i class="fa-regular fa-clock"></i>

                                    3 Months

                                </span>


                            </div>



                            <div class="internship-location">

                                <i class="fa-solid fa-location-dot"></i>

                                Bengaluru, Karnataka

                                <span class="mode-badge remote">

                                    Remote

                                </span>

                            </div>



                            <div class="internship-skills">

                                <span>Python</span>

                                <span>Pandas</span>

                                <span>SQL</span>

                                <span>Machine Learning</span>

                            </div>


                        </div>



                        <div class="internship-card-bottom">


                            <span class="status-badge completed">

                                <i class="fa-solid fa-circle-check"></i>

                                Completed

                            </span>


                            <button type="button"
                                    class="view-internship">

                                View Details

                                <i class="fa-solid fa-arrow-right"></i>

                            </button>


                        </div>


                    </article>



                    <!--==============================================
                              INTERNSHIP CARD 3
                    ===============================================-->

                    <article class="internship-card"
                             data-status="ongoing"
                             data-type="ai-ml">


                        <div class="internship-card-top">


                            <div class="company-logo cyan">

                                <i class="fa-solid fa-brain"></i>

                            </div>


                            <div class="card-actions">


                                <button type="button"
                                        class="card-action edit-internship"
                                        title="Edit Internship">

                                    <i class="fa-solid fa-pen"></i>

                                </button>


                                <button type="button"
                                        class="card-action delete-internship"
                                        title="Delete Internship">

                                    <i class="fa-solid fa-trash"></i>

                                </button>


                            </div>


                        </div>



                        <div class="internship-card-content">


                            <span class="internship-type">

                                AI / Machine Learning

                            </span>


                            <h3>

                                Machine Learning Intern

                            </h3>


                            <p class="company-name">

                                <i class="fa-solid fa-building"></i>

                                AI Innovations Lab

                            </p>


                            <p class="internship-role">

                                <i class="fa-solid fa-user-tie"></i>

                                Machine Learning Intern

                            </p>



                            <div class="internship-meta">


                                <span>

                                    <i class="fa-regular fa-calendar"></i>

                                    Jul 2026 - Sep 2026

                                </span>


                                <span>

                                    <i class="fa-regular fa-clock"></i>

                                    3 Months

                                </span>


                            </div>



                            <div class="internship-location">

                                <i class="fa-solid fa-location-dot"></i>

                                Remote

                                <span class="mode-badge remote">

                                    Remote

                                </span>

                            </div>



                            <div class="internship-skills">

                                <span>Python</span>

                                <span>Scikit-learn</span>

                                <span>ML</span>

                                <span>Data Analysis</span>

                            </div>


                        </div>



                        <div class="internship-card-bottom">


                            <span class="status-badge ongoing">

                                <i class="fa-solid fa-spinner"></i>

                                Ongoing

                            </span>


                            <button type="button"
                                    class="view-internship">

                                View Details

                                <i class="fa-solid fa-arrow-right"></i>

                            </button>


                        </div>


                    </article>



                    <!--==============================================
                              INTERNSHIP CARD 4
                    ===============================================-->

                    <article class="internship-card"
                             data-status="completed"
                             data-type="research">


                        <div class="internship-card-top">


                            <div class="company-logo orange">

                                <i class="fa-solid fa-flask"></i>

                            </div>


                            <div class="card-actions">


                                <button type="button"
                                        class="card-action edit-internship"
                                        title="Edit Internship">

                                    <i class="fa-solid fa-pen"></i>

                                </button>


                                <button type="button"
                                        class="card-action delete-internship"
                                        title="Delete Internship">

                                    <i class="fa-solid fa-trash"></i>

                                </button>


                            </div>


                        </div>



                        <div class="internship-card-content">


                            <span class="internship-type">

                                Research Internship

                            </span>


                            <h3>

                                AI Research Intern

                            </h3>


                            <p class="company-name">

                                <i class="fa-solid fa-building"></i>

                                Research Institute

                            </p>


                            <p class="internship-role">

                                <i class="fa-solid fa-user-tie"></i>

                                Research Intern

                            </p>



                            <div class="internship-meta">


                                <span>

                                    <i class="fa-regular fa-calendar"></i>

                                    May 2025 - Jul 2025

                                </span>


                                <span>

                                    <i class="fa-regular fa-clock"></i>

                                    8 Weeks

                                </span>


                            </div>



                            <div class="internship-location">

                                <i class="fa-solid fa-location-dot"></i>

                                Thiruvananthapuram, Kerala

                                <span class="mode-badge hybrid">

                                    Hybrid

                                </span>

                            </div>



                            <div class="internship-skills">

                                <span>Python</span>

                                <span>AI</span>

                                <span>Research</span>

                                <span>Deep Learning</span>

                            </div>


                        </div>



                        <div class="internship-card-bottom">


                            <span class="status-badge completed">

                                <i class="fa-solid fa-circle-check"></i>

                                Completed

                            </span>


                            <button type="button"
                                    class="view-internship">

                                View Details

                                <i class="fa-solid fa-arrow-right"></i>

                            </button>


                        </div>


                    </article>



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

