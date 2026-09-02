<?php

session_start();

require_once "db.php";


/* Make sure the user is logged in */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "User not logged in."
    ]);

    exit;

}


$user_id = $_SESSION["user_id"];


/* Get data sent from settings.js */

$full_name = trim($_POST["full_name"] ?? '');
$email = trim($_POST["email"] ?? '');
$phone = trim($_POST["phone"] ?? '');
$account_type = trim($_POST["account_type"] ?? '');

$github = trim($_POST["github"] ?? '');
$linkedin = trim($_POST["linkedin"] ?? '');
$portfolio = trim($_POST["portfolio"] ?? '');

/* Get the user's existing resume */

$old_resume = '';

$sql = "SELECT resume FROM profile WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "i",
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$profile_data = mysqli_fetch_assoc($result);

if ($profile_data) {
    $old_resume = $profile_data['resume'] ?? '';
}

mysqli_stmt_close($stmt);

$resume_path = '';

if (isset($_FILES["resume"]) && $_FILES["resume"]["error"] === UPLOAD_ERR_OK) {

    $file_name = $_FILES["resume"]["name"];
    $tmp_name = $_FILES["resume"]["tmp_name"];

    $file_extension = strtolower(
        pathinfo($file_name, PATHINFO_EXTENSION)
    );

    if ($file_extension !== "pdf") {

        echo json_encode([
            "success" => false,
            "message" => "Only PDF files are allowed."
        ]);

        exit;
    }

    $new_file_name = "resume_" . $user_id . "_" . time() . ".pdf";

    $upload_path = "uploads/" . $new_file_name;

    if (!move_uploaded_file($tmp_name, $upload_path)) {

        echo json_encode([
            "success" => false,
            "message" => "Failed to upload resume."
        ]);

        exit;
    }

    $resume_path = $upload_path;
    /* Delete the old resume */

    if ($old_resume !== '' && file_exists($old_resume)) {

        unlink($old_resume);

    }
}
/* Profile photo upload */

$profile_photo_path = '';

if (isset($_FILES["profile_photo"]) &&
    $_FILES["profile_photo"]["error"] === UPLOAD_ERR_OK) {

    $file_name = $_FILES["profile_photo"]["name"];
    $tmp_name = $_FILES["profile_photo"]["tmp_name"];

    $file_extension = strtolower(
        pathinfo($file_name, PATHINFO_EXTENSION)
    );

    $allowed_extensions = ["jpg", "jpeg", "png", "webp"];

    if (!in_array($file_extension, $allowed_extensions)) {

        echo json_encode([
            "success" => false,
            "message" => "Only JPG, JPEG, PNG and WEBP images are allowed."
        ]);

        exit;
    }

    $new_file_name =
        "profile_" . $user_id . "_" . time() . "." . $file_extension;

    $upload_path = "uploads/" . $new_file_name;

    if (!move_uploaded_file($tmp_name, $upload_path)) {

        echo json_encode([
            "success" => false,
            "message" => "Failed to upload profile photo."
        ]);

        exit;
    }

    $profile_photo_path = $upload_path;
}


/* Update users table */

$sql = "UPDATE users
        SET full_name = ?,
            email = ?,
            phone = ?,
            account_type = ?
        WHERE user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ssssi",
    $full_name,
    $email,
    $phone,
    $account_type,
    $user_id
);

if (!mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => false,
        "message" => "Failed to update user information."
    ]);

    mysqli_stmt_close($stmt);
    exit;

}

mysqli_stmt_close($stmt);


/* Update profile table */

if ($resume_path !== '' && $profile_photo_path !== '') {

    $sql = "UPDATE profile
            SET github = ?,
                linkedin = ?,
                portfolio = ?,
                resume = ?,
                profile_photo = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssssi",
        $github,
        $linkedin,
        $portfolio,
        $resume_path,
        $profile_photo_path,
        $user_id
    );

} elseif ($resume_path !== '') {

    $sql = "UPDATE profile
            SET github = ?,
                linkedin = ?,
                portfolio = ?,
                resume = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $github,
        $linkedin,
        $portfolio,
        $resume_path,
        $user_id
    );

} elseif ($profile_photo_path !== '') {

    $sql = "UPDATE profile
            SET github = ?,
                linkedin = ?,
                portfolio = ?,
                profile_photo = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "ssssi",
        $github,
        $linkedin,
        $portfolio,
        $profile_photo_path,
        $user_id
    );

} else {

    $sql = "UPDATE profile
            SET github = ?,
                linkedin = ?,
                portfolio = ?
            WHERE user_id = ?";

    $stmt = mysqli_prepare($conn, $sql);

    mysqli_stmt_bind_param(
        $stmt,
        "sssi",
        $github,
        $linkedin,
        $portfolio,
        $user_id
    );
}


if (!mysqli_stmt_execute($stmt)) {

    echo json_encode([
        "success" => false,
        "message" => "User information updated, but profile information could not be updated."
    ]);

    mysqli_stmt_close($stmt);
    exit;
}


mysqli_stmt_close($stmt);


/* Success */

echo json_encode([
    "success" => true,
    "message" => "Account information saved successfully."
]);

?>