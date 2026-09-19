<?php

session_start();

require_once "db.php";

if (!isset($_SESSION["user_id"])) {
    http_response_code(401);
    exit("Unauthorized");
}

$user_id = $_SESSION["user_id"];

$achievement_id =
    (int) ($_GET["id"] ?? 0);

if ($achievement_id <= 0) {
    http_response_code(400);
    exit("Invalid achievement.");
}


/*==========================================
        GET CERTIFICATE
==========================================*/

$query = "
    SELECT certificate_file
    FROM achievements
    WHERE achievement_id = ?
    AND user_id = ?
";

$stmt = mysqli_prepare(
    $conn,
    $query
);

mysqli_stmt_bind_param(
    $stmt,
    "ii",
    $achievement_id,
    $user_id
);

mysqli_stmt_execute($stmt);

$result =
    mysqli_stmt_get_result($stmt);

$data =
    mysqli_fetch_assoc($result);

mysqli_stmt_close($stmt);


if (
    !$data ||
    empty($data["certificate_file"])
) {
    http_response_code(404);
    exit("Certificate not found.");
}


/*==========================================
        BUILD FILE PATH
==========================================*/

$file_url =
    $data["certificate_file"];

$file_path =
    __DIR__ . "/" . $file_url;


if (!is_file($file_path)) {
    http_response_code(404);
    exit("Certificate file not found.");
}


/*==========================================
        SEND FILE
==========================================*/

$extension =
    strtolower(
        pathinfo(
            $file_path,
            PATHINFO_EXTENSION
        )
    );

$content_types = [
    "pdf"  => "application/pdf",
    "jpg"  => "image/jpeg",
    "jpeg" => "image/jpeg",
    "png"  => "image/png"
];

$content_type =
    $content_types[$extension]
    ?? "application/octet-stream";

header(
    "Content-Type: " . $content_type
);

header(
    "Content-Disposition: inline; filename=\"" .
    basename($file_path) .
    "\""
);

readfile($file_path);

exit;