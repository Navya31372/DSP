<?php

/*==================================================
        NOTIFICATION SYSTEM
==================================================*/

$unread_notifications = 0;
$notifications = [];


/*==================================================
        GET UNREAD NOTIFICATION COUNT
==================================================*/

$sql = "SELECT COUNT(*) AS unread_count
        FROM notifications
        WHERE user_id = ?
        AND is_read = 0";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

$notification_data = mysqli_fetch_assoc($result);

$unread_notifications = $notification_data["unread_count"];

mysqli_stmt_close($stmt);


/*==================================================
        GET NOTIFICATIONS
==================================================*/

$sql = "SELECT notification_id, message, is_read, created_at
        FROM notifications
        WHERE user_id = ?
        ORDER BY notification_id DESC";

$stmt = mysqli_prepare($conn, $sql);

mysqli_stmt_bind_param($stmt, "i", $user_id);

mysqli_stmt_execute($stmt);

$result = mysqli_stmt_get_result($stmt);

while ($notification = mysqli_fetch_assoc($result)) {

    $notifications[] = $notification;

}

mysqli_stmt_close($stmt);

?>