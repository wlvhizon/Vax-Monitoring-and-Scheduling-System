<?php
include 'db_connection.php';

if (isset ($_POST['childId'])) {
    $childId = $_POST ['childId'];

    $infoQuery = "SELECT * FROM children WHERE id = ?";
    $infoStmt = $conn->prepare($infoQuery);
    $infoStmt->bind_param("i",$childId);
    $infoStmt->execute();
    $childInfo = $infoStmt->get_result()->fetch_assoc();
    $infoStmt->close();

    $childInfoHtml = "<h2>Child Information</h2>";
    $childInfoHtml = "<h2>Child Information</h2>";
    $childInfoHtml .= "<img src='" . htmlspecialchars($childInfo['child_photo']) . "' alt='Child Photo' style='width:150px;height:150px;'><br>";
    $childInfoHtml .= "<p>ID Number: " . htmlspecialchars($childInfo['child_id']) . "</p>";
    $childInfoHtml .= "<p>Name: ".htmlspecialchars($childInfo['first_name']) . " " . htmlspecialchars($childInfo['last_name']) . "</p>";
    $childInfoHtml .= "<p>Birth Date: " . htmlspecialchars($childInfo['birth_date']) . "</p>";
    $childInfoHtml .= "<p>Zone: " . htmlspecialchars($childInfo['zone_num']) . "</p>";
    $childInfoHtml .= "<h3>Father's Information</h3>";
    $childInfoHtml .= "<p>Father's Name: " . htmlspecialchars($childInfo['f_first_name']) . " " . 
                                        htmlspecialchars($childInfo['f_middle_name']) . " " . 
                                        htmlspecialchars($childInfo['f_last_name']) . "</p>";
    $childInfoHtml .= "<h3>Mother's Information</h3>";
    $childInfoHtml .= "<p>Mother's Name: " . htmlspecialchars($childInfo['m_first_name']) . " " . 
                                        htmlspecialchars($childInfo['m_middle_name']) . " " . 
                                        htmlspecialchars($childInfo['m_last_name']) . "</p>";
    $childInfoHtml .= "<p>Contact Number: " . htmlspecialchars($childInfo['contact_num']) . "</p>";
    $childInfoHtml .= "<style>
                        button {
                            padding: 10px 20px;
                            font-size: 16px;
                            border-radius: 12px;
                            cursor: pointer;
                            border: none;
                            transition: background-color 0.3s ease;
                        }

                        button.edit-btn {
                            background-color: #28a745; /* Green background */
                            color: white;
                        }

                        button.delete-btn {
                            background-color: #dc3545; /* Red background */
                            color: white;
                        }

                        button:hover {
                            opacity: 0.8;
                        }
                    </style>";
    $childInfoHtml .= "<a href='edit_child_info.php?childId=" . $childInfo['id'] . "'><button class='edit-btn'>Edit Info</button></a>";
    $childInfoHtml .= "<a href='delete_child.php?childId=" . $childInfo['id'] . "'><button class='delete-btn'>Delete</button></a>";

    $recordQuery = "SELECT vr.record_id, v.vaccine_name, vr.schedule_date, vr.administered_date FROM vaccination_records vr JOIN vaccines v ON vr.vaccine_id = v.vaccine_id WHERE vr.id = ?";
    $recordStmt = $conn->prepare($recordQuery);
    $recordStmt->bind_param("i",$childId);
    $recordStmt->execute();
    $records = $recordStmt->get_result();
    $recordStmt->close();

    $recordHtml = "<h2>Vaccination Records</h2>";
    $recordHtml .= "<table border='1'><tr><th>Vaccine</th><th>Scheduled Date</th><th>Administered Date</th><th>Actions</th></tr>";

    while ($record = $records->fetch_assoc()) {
        $recordHtml .= "<tr>
                        <td>" . htmlspecialchars($record['vaccine_name']) . "</td>
                        <td>" . htmlspecialchars($record['schedule_date']) . "</td>
                        <td>
                            <input type='date' class='administered-date' value='" . htmlspecialchars($record['administered_date']) . "'>
                        </td>
                        <td>
                            <button class='update-record' data-record-id='" . $record['record_id'] . "'>Update</button>
                        </td>
                    </tr>";

    }
    $recordHtml .= "</table>";

    echo json_encode([
        'childInfo' => $childInfoHtml,
        'vaccinationRecords' => $recordHtml
    ]);
}
?>