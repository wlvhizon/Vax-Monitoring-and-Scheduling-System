<?php
include 'db_connection.php';

$current_date = date('Y-m-d');
$next_week_date = date('Y-m-d', strtotime('+7 days'));

// SQL to check for both upcoming and missed vaccine schedules
$sql = "
    SELECT 
        c.child_id, 
        c.first_name, 
        c.last_name, 
        v.vaccine_name, 
        r.schedule_date 
    FROM 
        vaccination_records r
    JOIN children c ON r.id = c.id  -- Make sure we join correctly with children table
    JOIN vaccines v ON r.vaccine_id = v.vaccine_id 
    WHERE 
        (r.schedule_date BETWEEN ? AND ? OR r.schedule_date < ?)  -- Check for upcoming or missed vaccines
        AND r.vaccine_status = 'Pending'
";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die('Prepare failed: ' . $conn->error);
}

$stmt->bind_param("sss", $current_date, $next_week_date, $current_date);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    // Determine whether this is a reminder for an upcoming or missed vaccine
    $status = (strtotime($row['schedule_date']) < strtotime($current_date)) ? 'MISSED' : 'UPCOMING';
    
    $message = "Reminder: Vaccine for " . $row['vaccine_name'] . " is scheduled for " . $row['schedule_date'] . 
               " for " . $row['first_name'] . " " . $row['last_name'] . ". Status: " . $status . ".";
    
    $insert_sql = "
        INSERT INTO notifications (child_id, vaccine_name, schedule_date, message)
        VALUES (?, ?, ?, ?)
    ";
    $insert_stmt = $conn->prepare($insert_sql);
    if ($insert_stmt === false) {
        die('Insert prepare failed: ' . $conn->error);
    }
    $insert_stmt->bind_param("ssss", $row['child_id'], $row['vaccine_name'], $row['schedule_date'], $message);
    $insert_stmt->execute();
    $insert_stmt->close();
}

echo "<script>alert('Vaccine schedules checked successfully!'); window.location.href = 'admin.php';</script>";

$stmt->close();
$conn->close();
?>
