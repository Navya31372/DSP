<?php

session_start();

require_once "db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $fullname = trim($_POST["fullname"]);
    $email = trim($_POST["email"]);
    $phone = trim($_POST["phone"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Check whether passwords match
    if ($password !== $confirm_password) {

        $message = "Passwords do not match.";

    } else {

        // Hash the password before storing it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert user into database
        $sql = "INSERT INTO users (full_name, email, password, phone)
                VALUES (?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        $stmt->bind_param(
            "ssss",
            $fullname,
            $email,
            $hashed_password,
            $phone
        );

        if ($stmt->execute()) {

    // Get the ID of the newly registered user
    $user_id = $stmt->insert_id;

    // Create login session for the new user
    $_SESSION["user_id"] = $user_id;
    $_SESSION["full_name"] = $fullname;

    // Go directly to dashboard
    header("Location: dashboard.php");
    exit();

} else {

    $message = "Registration failed: " . $stmt->error;
}

        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | Digital Skill Passport</title>

    <!-- Google Font -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Font Awesome -->

    <link rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">

    <!-- CSS -->

    <link rel="stylesheet" href="css/register.css">

</head>

<body>

<div class="container">

    <!-- Left Side -->

    <div class="left-panel">

        <div class="passport-card">

            <div class="passport-top">

                <i class="fa-solid fa-id-card"></i>

            </div>

            <div class="passport-photo">

                <i class="fa-regular fa-user"></i>

            </div>

            <div class="passport-lines">

                <div class="line"></div>

                <div class="line short"></div>

                <div class="line"></div>

            </div>

        </div>

        <h2>Digital Skill Passport</h2>

        <p>

            Build your professional identity by
            creating your Digital Skill Passport.

        </p>

    </div>

    <!-- Right Side -->

    <div class="right-panel">

        <div class="register-box">

            <h1>Create Your Account</h1>

            <p>

                Fill in the details below to create your account.

            </p>
        <?php if ($message != ""): ?>

            <p>
                <?php echo htmlspecialchars($message); ?>
            </p>

        <?php endif; ?>

        <form action="register.php" method="POST">

                <div class="input-box">

                    <label>Full Name</label>

                    <input type="text"
                    name="fullname"
                    placeholder="Enter your full name"
                    required>

                </div>

                <div class="input-box">

                    <label>Email Address</label>

                    <input type="email"
                    name="email"
                    placeholder="Enter your email"
                    required>

                </div>

                <div class="input-box">

                    <label>Phone Number</label>

                    <input type="tel"
                    name="phone"
                    placeholder="Enter your phone number"
                    required>

                </div>
                                <div class="input-box">

                    <label>Password</label>

                    <div class="password-field">

                        <input type="password"
                        id="password"
                        name="password"
                        placeholder="Create a password"
                        required>

                        <i class="fa-solid fa-eye toggle-password"
                        onclick="togglePassword('password', this)"></i>

                    </div>

                </div>

                <div class="input-box">

                    <label>Confirm Password</label>

                    <div class="password-field">

                        <input type="password"
                        id="confirmPassword"
                        name="confirm_password"
                        placeholder="Confirm your password"
                        required>

                        <i class="fa-solid fa-eye toggle-password"
                        onclick="togglePassword('confirmPassword', this)"></i>

                    </div>

                </div>

                <button type="submit" class="register-btn">

                    Register

                </button>

            </form>

            <div class="login-link">

                <p>

                    Already have an account?

                    <a href="login.php">

                        Login here

                    </a>

                </p>

            </div>

        </div>

    </div>

</div>

<!-- JavaScript -->

<script src="js/register.js"></script>

</body>

</html>