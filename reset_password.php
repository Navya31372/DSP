<?php

session_start();

include "db.php";

$error = "";
$success = "";

$token = $_GET["token"] ?? "";


/*==================================================
        CHECK RESET TOKEN
==================================================*/


if (
    $token === "" &&
    $_SERVER["REQUEST_METHOD"] !== "POST"
) {

    $error = "Invalid password reset link.";

}


/*==================================================
        RESET PASSWORD
==================================================*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["token"] ?? "";

    $new_password = $_POST["new_password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* Check password fields */

    if ($new_password === "" || $confirm_password === "") {

        $error =
            "Please enter and confirm your new password.";

    }

    elseif ($new_password !== $confirm_password) {

        $error =
            "Passwords do not match.";

    }

    elseif (strlen($new_password) < 6) {

        $error =
            "Password must be at least 6 characters.";

    }

    else {

        /* Find the reset token */

        $sql = "SELECT user_id, reset_expires
                FROM users
                WHERE reset_token = ?";

        $stmt = mysqli_prepare(
            $conn,
            $sql
        );

        mysqli_stmt_bind_param(
            $stmt,
            "s",
            $token
        );

        mysqli_stmt_execute($stmt);

        $result =
            mysqli_stmt_get_result($stmt);


        /* Check whether token exists */

        if (mysqli_num_rows($result) === 1) {

            $user =
                mysqli_fetch_assoc($result);


            /* Check whether token has expired */

            if (
                empty($user["reset_expires"]) ||
                strtotime($user["reset_expires"]) < time()
            ) {

                $error =
                    "This password reset link has expired.";

            }

            else {

                $user_id =
                    $user["user_id"];


                /* Hash new password */

                $hashed_password =
                    password_hash(
                        $new_password,
                        PASSWORD_DEFAULT
                    );


                /* Update password */

                $sql = "UPDATE users
                        SET password = ?,
                            reset_token = NULL,
                            reset_expires = NULL
                        WHERE user_id = ?";

                $update_stmt =
                    mysqli_prepare(
                        $conn,
                        $sql
                    );

                mysqli_stmt_bind_param(
                    $update_stmt,
                    "si",
                    $hashed_password,
                    $user_id
                );

                mysqli_stmt_execute(
                    $update_stmt
                );

                mysqli_stmt_close(
                    $update_stmt
                );


                $success =
                    "Your password has been reset successfully.";

            }

        }

        else {

            $error =
                "This password reset link is invalid.";

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

    <title>Reset Password | Digital Skill Passport</title>

    <link rel="stylesheet"
          href="css/login.css">

</head>


<body>

<div class="login-container">

    <div class="right-panel">

        <div class="login-card">

            <h1>Reset Password</h1>

            <p class="subtitle">

                Enter your new password below.

            </p>


            <?php if ($error !== ""): ?>

                <p style="color: red;">

                    <?= htmlspecialchars($error) ?>

                </p>

            <?php endif; ?>


            <?php if ($success !== ""): ?>

                <p style="color: green;">

                    <?= htmlspecialchars($success) ?>

                </p>

                <p>

                    <a href="login.php">

                        Go to Login

                    </a>

                </p>

            <?php endif; ?>


            <?php if ($success === "" && $token !== ""): ?>

                <form
                    action="reset_password.php"
                    method="POST">

                    <input
                        type="hidden"
                        name="token"
                        value="<?= htmlspecialchars($token) ?>">


                    <!-- New Password -->

                    <div class="input-group">

                        <label>New Password</label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>

                            <input
                                type="password"
                                name="new_password"
                                placeholder="Enter new password"
                                required>

                        </div>

                    </div>


                    <!-- Confirm Password -->

                    <div class="input-group">

                        <label>Confirm New Password</label>

                        <div class="input-box">

                            <i class="fa-solid fa-lock"></i>

                            <input
                                type="password"
                                name="confirm_password"
                                placeholder="Confirm new password"
                                required>

                        </div>

                    </div>


                    <button
                        type="submit"
                        class="login-btn">

                        <i class="fa-solid fa-key"></i>

                        Reset Password

                    </button>

                </form>

            <?php endif; ?>

        </div>

    </div>

</div>

</body>

</html>