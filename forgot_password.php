<?php

session_start();

include "db.php";

require "vendor/autoload.php";
require "mail_config.php";

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");

    if ($email === "") {

        $error = "Please enter your email address.";

    } else {

        /* Check whether email exists */

        $sql = "SELECT user_id, full_name
                FROM users
                WHERE email = ?";

        $stmt = mysqli_prepare($conn, $sql);

        mysqli_stmt_bind_param($stmt, "s", $email);

        mysqli_stmt_execute($stmt);

        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) === 1) {

            $user = mysqli_fetch_assoc($result);

            /* Generate secure reset token */

            $token = bin2hex(random_bytes(32));

            $expires = date(
                "Y-m-d H:i:s",
                time() + 3600
            );

            /* Save token in database */

            $sql = "UPDATE users
                    SET reset_token = ?,
                        reset_expires = ?
                    WHERE user_id = ?";

            $update_stmt = mysqli_prepare($conn, $sql);

            mysqli_stmt_bind_param(
                $update_stmt,
                "ssi",
                $token,
                $expires,
                $user["user_id"]
            );

            mysqli_stmt_execute($update_stmt);

            mysqli_stmt_close($update_stmt);

            

            $reset_link =
    "http://localhost/Digital%20Skill%20Passport/reset_password.php?token="
    . urlencode($token);


$mail = new PHPMailer(true);

try {

    $mail->isSMTP();

    $mail->Host = "smtp.gmail.com";
    $mail->SMTPAuth = true;

    $mail->Username = MAIL_USERNAME;
    $mail->Password = MAIL_PASSWORD;

    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom(
        MAIL_USERNAME,
        MAIL_FROM_NAME
    );

    $mail->addAddress(
        $email,
        $user["full_name"]
    );

    $mail->isHTML(true);

    $mail->Subject =
        "Digital Skill Passport - Password Reset";

    $mail->Body =
        "<h2>Password Reset</h2>"
        . "<p>Hello "
        . htmlspecialchars($user["full_name"])
        . ",</p>"
        . "<p>Click the button below to reset your password:</p>"
        . "<p>"
        . "<a href='" . htmlspecialchars($reset_link) . "'>"
        . "Reset My Password"
        . "</a>"
        . "</p>"
        . "<p>This link will expire in 1 hour.</p>"
        . "<p>If you did not request a password reset, you can ignore this email.</p>";

    $mail->send();

    $message =
    "If an account exists for this email address, a password reset link has been sent.";

} catch (Exception $e) {

    $error =
        "Unable to send the reset email. Please try again.";

}

        } else {

    $message =
        "If an account exists for this email address, a password reset link has been sent.";

}

        mysqli_stmt_close($stmt);
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Forgot Password | Digital Skill Passport</title>

    <link rel="stylesheet"
          href="css/login.css">

</head>

<body>

<div class="login-container">

    <div class="right-panel">

        <div class="login-card">

            <h1>Forgot Password?</h1>

            <p class="subtitle">
                Enter your registered email address.
            </p>

            <?php if ($error !== ""): ?>

                <p style="color: red;">
                    <?= htmlspecialchars($error) ?>
                </p>

            <?php endif; ?>


            <?php if ($message !== ""): ?>

                <p style="color: green;">
                    <?= $message ?>
                </p>

            <?php endif; ?>


            <form
                action="forgot_password.php"
                method="POST">

                <div class="input-group">

                    <label>Email Address</label>

                    <div class="input-box">

                        <i class="fa-solid fa-envelope"></i>

                        <input
                            type="email"
                            name="email"
                            placeholder="Enter your registered email"
                            required>

                    </div>

                </div>


                <button
                    type="submit"
                    class="login-btn">

                    <i class="fa-solid fa-paper-plane"></i>

                    Send Reset Link

                </button>

            </form>


            <div class="register-text">

                <p>

                    Remember your password?

                    <a href="login.php">
                        Back to Login
                    </a>

                </p>

            </div>

        </div>

    </div>

</div>

</body>

</html>