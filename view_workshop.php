<?php

session_start();

if (!isset($_SESSION["user_id"])) {
    header("Location: login.php");
    exit;
}

require_once "db.php";

$user_id = $_SESSION["user_id"];

$workshop_id = $_GET["id"] ?? 0;

$sql = "SELECT * FROM workshops
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

$workshop = mysqli_fetch_assoc($result);

if (!$workshop) {
    die("Workshop not found.");
}

?>

<!DOCTYPE html>
<html>

<head>

    <title>View Workshop | Digital Skill Passport</title>

    <link rel="stylesheet" href="css/workshops.css">

</head>

<body>

    <div style="padding: 40px;">

        <h1><?= htmlspecialchars($workshop["workshop_title"]) ?></h1>

        <p>
            <strong>Category:</strong>
            <?= htmlspecialchars($workshop["category"]) ?>
        </p>

        <p>
            <strong>Organization:</strong>
            <?= htmlspecialchars($workshop["organization"]) ?>
        </p>

        <p>
            <strong>Trainer:</strong>
            <?= htmlspecialchars($workshop["trainer"]) ?>
        </p>

        <p>
            <strong>Date:</strong>
            <?= htmlspecialchars($workshop["workshop_date"]) ?>
        </p>

        <p>
            <strong>Duration:</strong>
            <?= htmlspecialchars($workshop["duration"]) ?>
        </p>

        <p>
            <strong>Status:</strong>
            <?= htmlspecialchars($workshop["status"]) ?>
        </p>

        <p>
            <strong>Mode:</strong>
            <?= htmlspecialchars($workshop["mode"]) ?>
        </p>

        <p>
            <strong>Location:</strong>
            <?= htmlspecialchars($workshop["location"]) ?>
        </p>

        <p>
            <strong>Description:</strong><br>
            <?= nl2br(htmlspecialchars($workshop["description"])) ?>
        </p>

        <p>
            <strong>Skills Learned:</strong>
            <?= htmlspecialchars($workshop["skills_learned"]) ?>
        </p>

        <br>

        <a href="workshops.php">
            ← Back to Workshops
        </a>

    </div>

</body>

</html>