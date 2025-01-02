<?php
include 'db_connection.php';

if (isset($_GET['childId'])) {
    $childId = $conn->real_escape_string($_GET['childId']);

    $conn->begin_transaction();

    try {
        $deleteVaccinationRecords = "DELETE FROM vaccination_records WHERE id = ?";
        $stmt = $conn->prepare($deleteVaccinationRecords);
        $stmt->bind_param("i", $childId);
        $stmt->execute();

        $deleteChild = "DELETE FROM children WHERE id = ?";
        $stmt = $conn->prepare($deleteChild);
        $stmt->bind_param("i", $childId);
        $stmt->execute();

        $conn->commit();

        echo "<script> alert ('Child and associated vaccination records deleted successfully!'); window.location.href = 'admin.php';</script>";
    } catch (Exception $e) {

        $conn->rollback();

        echo "<script> alert ('Error deleting child: " . $conn->error . "'); window.location.href = 'user.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script> alert ('Invalid request'); window.location.href = 'user.php';</script>";
}

$conn->close();
?>
