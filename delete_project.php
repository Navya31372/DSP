<?php

session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
header("Expires: 0");


/*=========================================
        CHECK LOGIN
=========================================*/

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");

    exit;

}


$user_id = $_SESSION["user_id"];

include "db.php";


/*=========================================
        CHECK PROJECT ID
=========================================*/

if (
    $_SERVER["REQUEST_METHOD"] !== "POST" ||
    !isset($_POST["project_id"])
) {

    header("Location: projects.php");

    exit;

}


$project_id = (int) $_POST["project_id"];


/*=========================================
        GET PROJECT IMAGE
=========================================*/

$sql = "SELECT project_image
        FROM projects
        WHERE project_id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $project_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$project = mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


/*=========================================
        PROJECT NOT FOUND
=========================================*/

if (!$project) {

    header("Location: projects.php");

    exit;

}


/*=========================================
        DELETE PROJECT IMAGE
=========================================*/

if (!empty($project["project_image"])) {

    if (file_exists($project["project_image"])) {

        unlink($project["project_image"]);

    }

}


/*=========================================
        DELETE PROJECT FROM DATABASE
=========================================*/

$sql = "DELETE FROM projects
        WHERE project_id = ?
        AND user_id = ?";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $project_id,
    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    mysqli_stmt_close($stmt);

    header("Location: projects.php?project_deleted=1");

    exit;

}


/*=========================================
        DELETE FAILED
=========================================*/

$error = mysqli_stmt_error($stmt);

mysqli_stmt_close($stmt);

die("Error deleting project: " . $error);

?>