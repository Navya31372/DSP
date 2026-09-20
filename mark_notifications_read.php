<?php

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit;
}

$user_id = $_SESSION["user_id"];

$update_query = "
    UPDATE notifications
    SET is_read = 1
    WHERE user_id = ?
    AND is_read = 0
";

$update_stmt = mysqli_prepare(
    $conn,
    $update_query
);

mysqli_stmt_bind_param(
    $update_stmt,
    "i",
    $user_id
);

mysqli_stmt_execute(
    $update_stmt
);

mysqli_stmt_close(
    $update_stmt
);

echo "success";