<?php

session_start();
require_once "db.php";

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION["user_id"];


/* Get user information */

$sql = "SELECT * FROM users WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$user = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/* Store user values */

$userName = $user['full_name'] ?? '';

$userEmail = $user['email'] ?? '';

$userPhone = $user['phone'] ?? '';
$userAccountType = $user['account_type'] ?? '';


/* Get profile information */

$sql = "SELECT * FROM profile WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile = mysqli_fetch_assoc($result);
$github = $profile['github'] ?? '';
$linkedin = $profile['linkedin'] ?? '';
$portfolio = $profile['portfolio'] ?? '';
$resume = $profile['resume'] ?? '';
$profile_photo = $profile['profile_photo'] ?? '';

mysqli_stmt_close($stmt);

?>
<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Settings | Digital Skill Passport</title>


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
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">


    <!-- Settings CSS -->

    <link rel="stylesheet"
          href="css/settings.css">

</head>


<body>


<!--==================================================
                 PAGE CONTAINER
===================================================-->

<div class="settings-container">



    <!--==================================================
                         SIDEBAR
    ===================================================-->

    <aside class="sidebar">


        <!-- Logo -->

        <div class="logo">

            <i class="fa-solid fa-id-card"></i>

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

                    <i class="fa-solid fa-scroll"></i>

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

            <li>

                <a href="achievements.php">

                    <i class="fa-solid fa-trophy"></i>

                    <span>

                        Achievements

                    </span>

                </a>

            </li>



            <!-- Settings -->

            <li class="active">

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


            <div class="top-left">


                <!-- Mobile menu -->

                <button type="button"
                        class="menu-toggle"
                        id="menuToggle">

                    <i class="fa-solid fa-bars"></i>

                </button>


                <h2>

                    Settings

                </h2>


            </div>



            <div class="top-right">


                <!-- Search -->

                <div class="search-box">

                    <i class="fa-solid fa-magnifying-glass"></i>

                    <input type="text"
                           placeholder="Search...">

                </div>



                <!-- Notification -->

                <button type="button"
                        class="notification">

                    <i class="fa-regular fa-bell"></i>

                    <span>

                        2

                    </span>

                </button>



                <!-- User -->

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

                            <?php echo htmlspecialchars($userName); ?>

                        </h4>

                    </div>


                </div>


            </div>


        </header>



        <!--==================================================
                        SETTINGS HEADER
        ===================================================-->

        <section class="settings-header">


            <div class="settings-header-icon">

                <i class="fa-solid fa-gear"></i>

            </div>


            <div>

                <span>

                    ACCOUNT CONTROL

                </span>


                <h1>

                    Settings

                </h1>


                <p>

                    Manage your account, privacy,
                    notifications and security preferences.

                </p>

            </div>


        </section>



        <!--==================================================
                     SETTINGS CONTENT
        ===================================================-->

        <section class="settings-content">


            <!--==================================================
                     SETTINGS NAVIGATION
            ===================================================-->

            <div class="settings-navigation">


                <button type="button"
                        class="settings-nav-item active"
                        data-section="account">


                    <i class="fa-solid fa-user"></i>


                    <span>

                        Account

                    </span>


                </button>



                <button type="button"
                        class="settings-nav-item"
                        data-section="security">


                    <i class="fa-solid fa-shield-halved"></i>


                    <span>

                        Security

                    </span>


                </button>



                <button type="button"
                        class="settings-nav-item"
                        data-section="notifications">


                    <i class="fa-solid fa-bell"></i>


                    <span>

                        Notifications

                    </span>


                </button>



                <button type="button"
                        class="settings-nav-item"
                        data-section="privacy">


                    <i class="fa-solid fa-lock"></i>


                    <span>

                        Privacy

                    </span>


                </button>



                <button type="button"
                        class="settings-nav-item"
                        data-section="danger">


                    <i class="fa-solid fa-triangle-exclamation"></i>


                    <span>

                        Danger Zone

                    </span>


                </button>


            </div>



            <!--==================================================
                       SETTINGS PANELS
            ===================================================-->

            <div class="settings-panels">



                <!--==================================================
                           ACCOUNT SETTINGS
                ===================================================-->

                <div class="settings-panel active"
                     id="account">


                    <div class="panel-heading">


                        <div>

                            <h2>

                                Account Information

                            </h2>


                            <p>

                                Manage your basic account information.

                            </p>

                        </div>


                        <div class="panel-icon account-icon">

                            <i class="fa-solid fa-user"></i>

                        </div>


                    </div>



                    <form id="accountSettingsForm"
                          enctype="multipart/form-data">



                        <!-- Name -->

                        <div class="settings-form-grid">


                            <div class="settings-form-group">


                                <label for="settingsName">

                                    Full Name

                                </label>


                                <div class="settings-input">

                                    <i class="fa-solid fa-user"></i>

                                    <input type="text"
                                           id="settingsName"
                                           name="full_name"
                                           value="<?php echo htmlspecialchars($userName); ?>"
                                           required>

                                </div>


                            </div>



                            <!-- Email -->

                            <div class="settings-form-group">


                                <label for="settingsEmail">

                                    Email Address

                                </label>


                                <div class="settings-input">

                                    <i class="fa-solid fa-envelope"></i>

                                    <input type="email"
                                           id="settingsEmail"
                                           name="email"
                                           value="<?php echo htmlspecialchars($userEmail); ?>"
                                           required>

                                </div>


                            </div>  



                            <!-- Phone -->

                            <div class="settings-form-group">


                                <label for="phone">

                                    Phone Number

                                </label>


                                <div class="settings-input">

                                    <i class="fa-solid fa-phone"></i>

                                    <input type="tel"
                                           id="phone"
                                           name="phone"
                                           value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                           placeholder="Enter phone number">

                                </div>


                            </div>


                        </div>
                        <!-- GitHub -->

                        <div class="settings-form-group">

                            <label for="github">
                                GitHub
                            </label>

                        <div class="settings-input">

                            <i class="fa-brands fa-github"></i>

                            <input type="url"
                                    id="github"
                                    name="github"
                                    value="<?php echo htmlspecialchars($profile['github'] ?? ''); ?>"
                                    placeholder="Enter GitHub profile URL">

                        </div>

                        </div>
                        <!-- LinkedIn -->

                        <div class="settings-form-group">

                            <label for="linkedin">
                                LinkedIn
                            </label>

                            <div class="settings-input">

                                <i class="fa-brands fa-linkedin"></i>

                                <input type="url"
                                        id="linkedin"
                                        name="linkedin"
                                        value="<?php echo htmlspecialchars($profile['linkedin'] ?? ''); ?>"
                                        placeholder="Enter LinkedIn profile URL">

                                </div>

                        </div>
                        <!-- Portfolio -->

                        <div class="settings-form-group">

                            <label for="portfolio">
                                Portfolio
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-globe"></i>

                                <input type="url"
                                        id="portfolio"
                                        name="portfolio"
                                        value="<?php echo htmlspecialchars($profile['portfolio'] ?? ''); ?>"
                                        placeholder="Enter portfolio URL">

                            </div>

                        </div>
                        <!-- Resume -->

                        <div class="settings-form-group">

                            <label for="resume">
                                Resume
                            </label>

                            <div class="settings-input">

                                <i class="fa-solid fa-file-pdf"></i>

                                <input type="file"
                                        id="resume"
                                        name="resume"
                                        accept=".pdf">

                            </div>

                        </div>
                        <div class="settings-form-group">

    <label for="profile_photo">
        Profile Photo
    </label>

    <div class="settings-input">

        <i class="fa-solid fa-image"></i>

        <input type="file"
               id="profile_photo"
               name="profile_photo"
               accept="image/*">

    </div>

</div>




                        <!-- Account Role -->

                        <div class="settings-form-group full-width">


                            <label for="accountRole">

                                Account Type

                            </label>


                            <div class="settings-input">

                                <i class="fa-solid fa-graduation-cap"></i>

                                <select id="accountRole" 
                                        name="account_type">

                                    <option value="student"
                                        <?php echo ($userAccountType == 'student') ? 'selected' : ''; ?>>
                                        Student
                                    </option>

                                    <option value="professional"
                                        <?php echo ($userAccountType == 'professional') ? 'selected' : ''; ?>>
                                        Professional
                                    </option>

                                    <option value="other"
                                        <?php echo ($userAccountType == 'other') ? 'selected' : ''; ?>>
                                        Other
                                    </option>

                                </select>

                            </div>


                        </div>



                        <!-- Save -->

                        <div class="settings-actions">


                            <button type="button"
                                    class="save-settings-btn"
                                    id="saveAccountBtn">


                                <i class="fa-solid fa-floppy-disk"></i>

                                Save Changes


                            </button>


                        </div>


                    </form>


                </div>



                <!--==================================================
                         SECURITY SETTINGS
                ===================================================-->

                <div class="settings-panel"
                     id="security">


                    <div class="panel-heading">


                        <div>

                            <h2>

                                Password & Security

                            </h2>


                            <p>

                                Keep your Digital Skill Passport account secure.

                            </p>

                        </div>


                        <div class="panel-icon security-icon">

                            <i class="fa-solid fa-shield-halved"></i>

                        </div>


                    </div>



                    <form id="securityForm">


                        <!-- Current password -->

                        <div class="settings-form-group">


                            <label for="currentPassword">

                                Current Password

                            </label>


                            <div class="password-input">

                                <i class="fa-solid fa-lock"></i>

                                <input type="password"
                                       id="currentPassword"
                                       name="current_password"
                                       placeholder="Enter current password">


                                <button type="button"
                                        class="toggle-password"
                                        data-target="currentPassword">

                                    <i class="fa-regular fa-eye"></i>

                                </button>

                            </div>


                        </div>



                        <!-- New password -->

                        <div class="settings-form-grid">


                            <div class="settings-form-group">


                                <label for="newPassword">

                                    New Password

                                </label>


                                <div class="password-input">

                                    <i class="fa-solid fa-key"></i>

                                    <input type="password"
                                           id="newPassword"
                                           name="new_password"
                                           placeholder="Enter new password">


                                    <button type="button"
                                            class="toggle-password"
                                            data-target="newPassword">

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </div>


                            </div>



                            <!-- Confirm -->

                            <div class="settings-form-group">


                                <label for="confirmPassword">

                                    Confirm New Password

                                </label>


                                <div class="password-input">

                                    <i class="fa-solid fa-key"></i>

                                    <input type="password"
                                           id="confirmPassword"
                                           name="confirm_password"
                                           placeholder="Confirm new password">


                                    <button type="button"
                                            class="toggle-password"
                                            data-target="confirmPassword">

                                        <i class="fa-regular fa-eye"></i>

                                    </button>

                                </div>


                            </div>


                        </div>



                        <div class="password-note">

                            <i class="fa-solid fa-circle-info"></i>

                            Use at least 8 characters with a
                            combination of letters, numbers and symbols.

                        </div>



                        <div class="settings-actions">


                            <button type="button"
                                    class="save-settings-btn"
                                    id="changePasswordBtn">


                                <i class="fa-solid fa-key"></i>

                                Update Password


                            </button>


                        </div>


                    </form>


                </div>



                <!--==================================================
                       NOTIFICATION SETTINGS
                ===================================================-->

                <div class="settings-panel"
                     id="notifications">


                    <div class="panel-heading">


                        <div>

                            <h2>

                                Notification Preferences

                            </h2>


                            <p>

                                Choose which notifications you
                                want to receive.

                            </p>

                        </div>


                        <div class="panel-icon notification-icon">

                            <i class="fa-solid fa-bell"></i>

                        </div>


                    </div>



                    <div class="toggle-settings">



                        <!-- Achievement -->

                        <div class="toggle-row">


                            <div class="toggle-info">


                                <div class="toggle-title">

                                    <i class="fa-solid fa-trophy"></i>

                                    Achievement Updates

                                </div>


                                <p>

                                    Get notifications about achievement
                                    verification and updates.

                                </p>

                            </div>


                            <label class="switch">

                                <input type="checkbox"
                                       id="achievementNotifications"
                                       checked>

                                <span class="slider"></span>

                            </label>


                        </div>



                        <!-- Certificate -->

                        <div class="toggle-row">


                            <div class="toggle-info">


                                <div class="toggle-title">

                                    <i class="fa-solid fa-scroll"></i>

                                    Certificate Notifications

                                </div>


                                <p>

                                    Receive reminders about certificates
                                    and their expiry dates.

                                </p>

                            </div>


                            <label class="switch">

                                <input type="checkbox"
                                       id="certificateNotifications"
                                       checked>

                                <span class="slider"></span>

                            </label>


                        </div>



                        <!-- Workshop -->

                        <div class="toggle-row">


                            <div class="toggle-info">


                                <div class="toggle-title">

                                    <i class="fa-solid fa-graduation-cap"></i>

                                    Workshop Updates

                                </div>


                                <p>

                                    Receive updates about workshops
                                    and learning activities.

                                </p>

                            </div>


                            <label class="switch">

                                <input type="checkbox"
                                       id="workshopNotifications"
                                       checked>

                                <span class="slider"></span>

                            </label>


                        </div>



                        <!-- Security -->

                        <div class="toggle-row">


                            <div class="toggle-info">


                                <div class="toggle-title">

                                    <i class="fa-solid fa-shield-halved"></i>

                                    Security Alerts

                                </div>


                                <p>

                                    Get important notifications about
                                    account security.

                                </p>

                            </div>


                            <label class="switch">

                                <input type="checkbox"
                                       id="securityNotifications"
                                       checked>

                                <span class="slider"></span>

                            </label>


                        </div>


                    </div>


                </div>



                <!--==================================================
                         PRIVACY SETTINGS
                ===================================================-->

                <div class="settings-panel"
                     id="privacy">


                    <div class="panel-heading">


                        <div>

                            <h2>

                                Privacy & Visibility

                            </h2>


                            <p>

                                Control how your Digital Skill Passport
                                information is displayed.

                            </p>

                        </div>


                        <div class="panel-icon privacy-icon">

                            <i class="fa-solid fa-lock"></i>

                        </div>


                    </div>



                    <!-- Profile visibility -->

                    <div class="privacy-option">


                        <div class="privacy-option-icon">

                            <i class="fa-solid fa-eye"></i>

                        </div>


                        <div class="privacy-option-content">


                            <h3>

                                Profile Visibility

                            </h3>


                            <p>

                                Choose who can view your profile.

                            </p>


                        </div>


                        <select id="profileVisibility">

                            <option value="public">

                                Public

                            </option>

                            <option value="private">

                                Private

                            </option>

                            <option value="limited">

                                Limited

                            </option>

                        </select>


                    </div>



                    <!-- Skills visibility -->

                    <div class="privacy-option">


                        <div class="privacy-option-icon">

                            <i class="fa-solid fa-star"></i>

                        </div>


                        <div class="privacy-option-content">


                            <h3>

                                Skills Visibility

                            </h3>


                            <p>

                                Control whether your skills are
                                visible to other users.

                            </p>

                        </div>


                        <label class="switch">

                            <input type="checkbox"
                                   id="skillsVisibility"
                                   checked>

                            <span class="slider"></span>

                        </label>


                    </div>



                    <!-- Contact information -->

                    <div class="privacy-option">


                        <div class="privacy-option-icon">

                            <i class="fa-solid fa-address-book"></i>

                        </div>


                        <div class="privacy-option-content">


                            <h3>

                                Contact Information

                            </h3>


                            <p>

                                Allow visitors to see your contact
                                information.

                            </p>

                        </div>


                        <label class="switch">

                            <input type="checkbox"
                                   id="contactVisibility">

                            <span class="slider"></span>

                        </label>


                    </div>


                </div>



                <!--==================================================
                         DANGER ZONE
                ===================================================-->

                <div class="settings-panel"
                     id="danger">


                    <div class="panel-heading danger-heading">


                        <div>

                            <h2>

                                Danger Zone

                            </h2>


                            <p>

                                These actions can affect your account
                                permanently.

                            </p>

                        </div>


                        <div class="panel-icon danger-icon">

                            <i class="fa-solid fa-triangle-exclamation"></i>

                        </div>


                    </div>



                    <!-- Logout -->

                    <div class="danger-option">


                        <div>


                            <h3>

                                Log Out

                            </h3>


                            <p>

                                Sign out from your Digital Skill
                                Passport account.

                            </p>

                        </div>


                        <a href="logout.php"
                           class="danger-btn secondary-danger">

                            <i class="fa-solid fa-right-from-bracket"></i>

                            Logout

                        </a>


                    </div>



                    <!-- Delete account -->

                    <div class="danger-option">


                        <div>


                            <h3>

                                Delete Account

                            </h3>


                            <p>

                                Permanently delete your account
                                and all associated information.

                            </p>

                        </div>


                        <button type="button"
                                class="danger-btn delete-btn"
                                id="deleteAccountBtn">


                            <i class="fa-solid fa-trash"></i>

                            Delete Account


                        </button>


                    </div>


                </div>


            </div>


        </section>


    </main>

</div>



<!--==================================================
                   JAVASCRIPT
===================================================-->

<script src="js/settings.js"></script>


</body>

</html>