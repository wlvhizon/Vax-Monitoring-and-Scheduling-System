<?php
include 'db_connection.php';

if (isset($_POST['record_id']) && !empty($_POST['record_id']) && isset($_POST['newDate']) && !empty($_POST['newDate'])) {
  $recordId = $_POST['record_id'];
  $newDate = $_POST['newDate'];

  $updateQuery = "UPDATE vaccination_records SET administered_date = ?, vaccine_status = 'Completed', remarks = 'Administered' WHERE record_id = ?";
  $stmt = $conn->prepare($updateQuery);
  $stmt->bind_param("si", $newDate, $recordId);

  if ($stmt->execute()) {
    echo json_encode(['message' => 'Vaccination record updated successfully.']);
  } else {
    echo json_encode(['message' => 'Failed to update vaccination record.']);
  }
  $stmt->close();
} else {
    echo json_encode(['message' => 'Invalid data provided. Please check the record ID and date.']);
}
?>