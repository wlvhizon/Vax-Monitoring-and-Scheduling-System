<?php
include 'db_connection.php';

if (isset($_GET['user_id'])) {
    $user_id = $conn->real_escape_string($_GET['user_id']);

    $delete_query = "DELETE FROM users WHERE user_id = '$user_id'";
    if ($conn->query($delete_query) === true) {
        echo "<script> alert ('User deleted successfully!'); window.location.href = 'admin.php';</script>";
    } else {
        echo "<script> alert ('Error deleting user: " . $conn->error . "'); window.location.href = 'admin.php';</script>";
    }
} else {
    echo "<script> alert ('Invalid request'); window.location.href = 'admin.php';</script>";
}
$conn->close();
?>