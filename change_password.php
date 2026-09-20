<?php

session_start();

require_once "db.php";

header("Content-Type: application/json");


if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You are not logged in."
    ]);

    exit;
}


$user_id = $_SESSION["user_id"];

$current_password =
    $_POST["current_password"] ?? "";

$new_password =
    $_POST["new_password"] ?? "";

$confirm_password =
    $_POST["confirm_password"] ?? "";


/* Check empty fields */

if (
    $current_password === "" ||
    $new_password === "" ||
    $confirm_password === ""
) {

    echo json_encode([
        "success" => false,
        "message" => "Please fill in all password fields."
    ]);

    exit;
}


/* Check new password confirmation */

if ($new_password !== $confirm_password) {

    echo json_encode([
        "success" => false,
        "message" => "New password and confirmation do not match."
    ]);

    exit;
}


/* Get current password */

$sql = "
    SELECT password
    FROM users
    WHERE user_id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$user =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (!$user) {

    echo json_encode([
        "success" => false,
        "message" => "User account not found."
    ]);

    exit;
}


/* Verify current password */

if (
    !password_verify(
        $current_password,
        $user["password"]
    )
) {

    echo json_encode([
        "success" => false,
        "message" => "Current password is incorrect."
    ]);

    exit;
}


/* Hash new password */

$hashed_password =
    password_hash(
        $new_password,
        PASSWORD_DEFAULT
    );


/* Update password */

$sql = "
    UPDATE users
    SET password = ?
    WHERE user_id = ?
";


$stmt = mysqli_prepare(
    $conn,
    $sql
);

mysqli_stmt_bind_param(
    $stmt,
    "si",
    $hashed_password,
    $user_id
);

$success =
    mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);


if (!$success) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update password."
    ]);

    exit;
}


echo json_encode([
    "success" => true,
    "message" => "Password updated successfully."
]);

?>