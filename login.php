<?php

session_start();
include "db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = $_POST["email"];
    $password = $_POST["password"];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);

    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {

        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user["password"])) {

            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];

            header("Location: dashboard.php");
            exit();

        } else {

            $error = "Invalid email or password.";

        }

    } else {

        $error = "Invalid email or password.";

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

    <title>Login | Digital Skill Passport</title>

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

    <!-- Login CSS -->

    <link rel="stylesheet"
          href="css/login.css">

</head>

<body>

<div class="login-container">

    <!--=========================
            LEFT PANEL
    ==========================-->

    <div class="left-panel">

        <div class="overlay"></div>

        <div class="image-box">

            <img src="images/login-illustration.webp"
                 alt="Login Illustration"
                 class="login-image">

        </div>

        <div class="left-content">

            <h2>Digital Skill Passport</h2>

            <p>

                Build your professional digital profile,
                manage your skills, projects,
                certificates, internships and
                achievements in one secure place.

            </p>

        </div>

    </div>

    <!--=========================
            RIGHT PANEL
    ==========================-->

    <div class="right-panel">

        <div class="login-card">

            <h1>Welcome Back!</h1>

            <p class="subtitle">

                Login to continue your learning journey.

            </p>
            <?php if ($error != ""): ?>

                <p style="color: red;">
                    <?php echo $error; ?>
                </p>

            <?php endif; ?>

            <form action="login.php" method="POST">

                <!-- Email -->

                <div class="input-group">

                    <label>Email Address</label>

                    <div class="input-box">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your email"
                            required>

                    </div>

                </div>

                <!-- Password -->

                <div class="input-group">

                    <label>Password</label>

                    <div class="input-box">

                        <i class="fa-solid fa-lock"></i>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required>

                        <span
                            class="toggle-password"
                            onclick="togglePassword()">

                            <i id="eyeIcon"
                               class="fa-solid fa-eye"></i>

                        </span>

                    </div>

                </div>
                                <!-- Remember & Forgot Password -->

                <div class="form-options">

                    <label class="remember">

                        <input
                            type="checkbox"
                            name="remember">

                        <span>Remember Me</span>

                    </label>

                    <a href="forgot_password.php" 
   class="forgot-link">

    Forgot Password?

</a>

                </div>

                <!-- Login Button -->

                <button
                    type="submit"
                    class="login-btn">

                    <i class="fa-solid fa-right-to-bracket"></i>

                    Login

                </button>

            </form>

            <!-- Divider -->

            <div class="divider">

                <span>OR</span>

            </div>

            <!-- Register Link -->

            <div class="register-text">

                <p>

                    Don't have an account?

                    <a href="register.php">

                        Create Account

                    </a>

                </p>

            </div>

        </div>

    </div>

</div>

<!-- Login JavaScript -->

<script src="js/login.js"></script>

</body>

</html>