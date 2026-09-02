<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

include "db.php";

$user_id = $_SESSION["user_id"];

$certificate_id = $_POST["certificate_id"] ?? 0;

$sql = "DELETE FROM certificates
        WHERE certificate_id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $certificate_id,
    $user_id
);

mysqli_stmt_execute($stmt);

mysqli_stmt_close($stmt);

header("Location: certificates.php?certificate_deleted=1");
exit;

?>