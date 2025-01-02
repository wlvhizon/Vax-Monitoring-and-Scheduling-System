<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $notification_id = intval($_POST['notification_id']);

    $sql = "UPDATE notifications SET is_read = 1 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        die('Prepare failed: ' . $conn->error);
    }

    $stmt->bind_param("i", $notification_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: admin.php");
    exit;
}
?>