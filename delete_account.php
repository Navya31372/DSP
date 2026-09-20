<?php

session_start();

require_once "db.php";

header("Content-Type: application/json");


/* Check login */

if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "success" => false,
        "message" => "You are not logged in."
    ]);

    exit;
}


$user_id =
    $_SESSION["user_id"];

$current_password =
    $_POST["current_password"] ?? "";


/* Check password */

if ($current_password === "") {

    echo json_encode([
        "success" => false,
        "message" => "Current password is required."
    ]);

    exit;
}


/* Get stored password */

$sql = "
    SELECT password
    FROM users
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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


/* Verify password */

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

/*
--------------------------------------------------
Collect user's uploaded files
--------------------------------------------------
*/

$files_to_delete = [];


/* Profile files */

$sql = "
    SELECT profile_photo, resume
    FROM profile
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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

if ($profile = mysqli_fetch_assoc($result)) {

    if (!empty($profile["profile_photo"])) {

        $files_to_delete[] =
            $profile["profile_photo"];

    }

    if (!empty($profile["resume"])) {

        $files_to_delete[] =
            $profile["resume"];

    }

}

mysqli_stmt_close($stmt);


/* Achievement certificates */

$sql = "
    SELECT certificate_file
    FROM achievements
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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

while ($row = mysqli_fetch_assoc($result)) {

    if (!empty($row["certificate_file"])) {

        $files_to_delete[] =
            $row["certificate_file"];

    }

}

mysqli_stmt_close($stmt);


/* Certificates */

$sql = "
    SELECT certificate_file
    FROM certificates
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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

while ($row = mysqli_fetch_assoc($result)) {

    if (!empty($row["certificate_file"])) {

        $files_to_delete[] =
            $row["certificate_file"];

    }

}

mysqli_stmt_close($stmt);


/* Internship files */

$sql = "
    SELECT
        certificate_file,
        supporting_document
    FROM internships
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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

while ($row = mysqli_fetch_assoc($result)) {

    if (!empty($row["certificate_file"])) {

        $files_to_delete[] =
            $row["certificate_file"];

    }

    if (!empty($row["supporting_document"])) {

        $files_to_delete[] =
            $row["supporting_document"];

    }

}

mysqli_stmt_close($stmt);


/* Workshop certificates */

$sql = "
    SELECT certificate_file
    FROM workshops
    WHERE user_id = ?
";

$stmt =
    mysqli_prepare(
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

while ($row = mysqli_fetch_assoc($result)) {

    if (!empty($row["certificate_file"])) {

        $files_to_delete[] =
            $row["certificate_file"];

    }

}

mysqli_stmt_close($stmt);



/*
--------------------------------------------------
Delete user-related database records
--------------------------------------------------
*/

mysqli_begin_transaction($conn);

try {

    /* Notifications */

    $tables = [
        "notifications",
        "user_settings",
        "user_skills",
        "projects",
        "certificates",
        "workshops",
        "internships",
        "achievements",
        "profile"
    ];


    foreach ($tables as $table) {

        $delete_sql =
            "DELETE FROM " .
            $table .
            " WHERE user_id = ?";

        $delete_stmt =
            mysqli_prepare(
                $conn,
                $delete_sql
            );

        if (!$delete_stmt) {

            throw new Exception(
                "Unable to prepare deletion."
            );
        }


        mysqli_stmt_bind_param(
            $delete_stmt,
            "i",
            $user_id
        );


        if (
            !mysqli_stmt_execute(
                $delete_stmt
            )
        ) {

            mysqli_stmt_close(
                $delete_stmt
            );

            throw new Exception(
                "Unable to delete user data."
            );
        }


        mysqli_stmt_close(
            $delete_stmt
        );
    }


    /* Finally delete the user account */

    $delete_user_sql = "
        DELETE FROM users
        WHERE user_id = ?
    ";

    $delete_user_stmt =
        mysqli_prepare(
            $conn,
            $delete_user_sql
        );

    if (!$delete_user_stmt) {

        throw new Exception(
            "Unable to delete account."
        );
    }


    mysqli_stmt_bind_param(
        $delete_user_stmt,
        "i",
        $user_id
    );


    if (
        !mysqli_stmt_execute(
            $delete_user_stmt
        )
    ) {

        mysqli_stmt_close(
            $delete_user_stmt
        );

        throw new Exception(
            "Unable to delete account."
        );
    }


    mysqli_stmt_close(
        $delete_user_stmt
    );


    /* Commit everything */

    mysqli_commit($conn);


    /*
--------------------------------------------------
Delete collected uploaded files
--------------------------------------------------
*/

foreach ($files_to_delete as $file_url) {

    $file_path =
        __DIR__ . "/" .
        ltrim(
            str_replace(
                "\\",
                "/",
                $file_url
            ),
            "/"
        );

    if (
        is_file($file_path)
    ) {

        unlink($file_path);

    }

}


    /* Destroy login session */

    $_SESSION = [];

    if (ini_get("session.use_cookies")) {

        $params =
            session_get_cookie_params();

        setcookie(
            session_name(),
            "",
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }


    session_destroy();


    echo json_encode([
        "success" => true,
        "message" =>
            "Your account has been permanently deleted."
    ]);

}
catch (Exception $e) {

    mysqli_rollback($conn);


    echo json_encode([
        "success" => false,
        "message" =>
            "Account deletion failed. No data was deleted."
    ]);
}

?>