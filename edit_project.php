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

if (!isset($_GET["project_id"])) {

    header("Location: projects.php");

    exit;

}


$project_id = (int) $_GET["project_id"];


/*=========================================
        GET PROJECT DETAILS
=========================================*/

$sql = "SELECT
            project_id,
            project_title,
            category,
            status,
            technologies,
            description,
            start_date,
            end_date,
            github_link,
            live_link,
            project_image
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

?>