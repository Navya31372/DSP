<?php

session_start();

require_once "db.php";


if (!isset($_SESSION["user_id"])) {

    echo json_encode([
        "error" => "Not logged in"
    ]);

    exit;

}


$user_id = $_SESSION["user_id"];


if (!isset($_GET["id"])) {

    echo json_encode([
        "error" => "Workshop ID missing"
    ]);

    exit;

}


$workshop_id = $_GET["id"];


$sql = "SELECT *
        FROM workshops
        WHERE workshop_id = ?
        AND user_id = ?";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $workshop_id,
    $user_id
);


mysqli_stmt_execute($stmt);


$result = mysqli_stmt_get_result($stmt);


if ($workshop = mysqli_fetch_assoc($result)) {

    header("Content-Type: application/json");

    echo json_encode($workshop);

}
else {

    echo json_encode([
        "error" => "Workshop not found"
    ]);

}


mysqli_stmt_close($stmt);

?>