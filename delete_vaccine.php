<?php
include 'db_connection.php';

if (isset($_GET['vaccine_id'])) {
    $vaccine_id = $conn->real_escape_string($_GET['vaccine_id']);

    $delete_records_query = "DELETE FROM vaccination_records WHERE vaccine_id = ?";
    $stmt = $conn->prepare($delete_records_query);
    $stmt->bind_param("i", $vaccine_id);
    $stmt->execute();
    $stmt->close();

    $delete_schedules_query = "DELETE FROM vaccine_schedules WHERE vaccine_id = ?";
    $stmt = $conn->prepare($delete_schedules_query);
    $stmt->bind_param("i", $vaccine_id);
    $stmt->execute();
    $stmt->close();

    $delete_vaccine_query = "DELETE FROM vaccines WHERE vaccine_id = ?";
    $stmt = $conn->prepare($delete_vaccine_query);
    $stmt->bind_param("i", $vaccine_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Vaccine and related schedules deleted successfully!'); window.location.href = 'admin.php';</script>";
}
?>