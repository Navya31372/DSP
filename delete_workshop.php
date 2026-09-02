<?php

session_start();

require_once "db.php";


if (!isset($_SESSION["user_id"])) {

    echo "error";
    exit;

}


$user_id = $_SESSION["user_id"];


if (!isset($_POST["workshop_id"])) {

    echo "error";
    exit;

}


$workshop_id = $_POST["workshop_id"];


$sql = "DELETE FROM workshops
        WHERE workshop_id = ?
        AND user_id = ?";


$stmt = mysqli_prepare($conn, $sql);


mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $workshop_id,
    $user_id
);


if (mysqli_stmt_execute($stmt)) {

    echo "success";

}

else {

    echo "error";

}


mysqli_stmt_close($stmt);

?>