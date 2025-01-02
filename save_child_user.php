<?php
include 'db_connection.php';

$success_message = 'DATA ENTRY SUCCESSFUL!';
$error_message = 'DATA ENTRY FAILED!';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $year = date('Y');
    $zone_number = $_POST['zone_num'];

    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $birth_date = $_POST['birth_date'];
    $f_first_name = $_POST['f_first_name'];
    $f_middle_name = $_POST['f_middle_name'];
    $f_last_name = $_POST['f_last_name'];
    $m_first_name = $_POST['m_first_name'];
    $m_middle_name = $_POST['m_middle_name'];
    $m_last_name = $_POST['m_last_name'];
    $contact_num = $_POST['contact_num'];
    $zone_num = $_POST['zone_num'];

    $target_dir = "images/";
    $uploadOk = 1;
    $photo_file = null;

    if (!empty($_FILES["child_photo"]["name"])) {
        $target_file = $target_dir . basename($_FILES["child_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["child_photo"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            echo "File is not an image.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["child_photo"]["tmp_name"], $target_file)) {
                $photo_file = $target_file;
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    }

    if (!empty($_POST['photo_data'])) {
        $photo_data = $_POST['photo_data'];

        $image_parts = explode(";base64,", $photo_data);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);

        $photo_file = $target_dir . uniqid() . '.' . $image_type;

        file_put_contents($photo_file, $image_base64);
    }

    $sql = "INSERT INTO children (child_id, child_photo, first_name, middle_name, last_name, birth_date, zone_num, f_first_name, f_middle_name, f_last_name, m_first_name, m_middle_name, m_last_name, contact_num) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("ssssssisssssss", $child_id, $photo_file, $first_name, $middle_name, $last_name, $birth_date, $zone_number, $f_first_name, $f_middle_name, $f_last_name, $m_first_name, $m_middle_name, $m_last_name, $contact_num);

    if ($stmt->execute()) {

        $last_id = $conn->insert_id;

        $child_id = $year . str_pad($zone_number, 2, '0', STR_PAD_LEFT) . str_pad($last_id, 6, '0', STR_PAD_LEFT);

        $update_sql = "UPDATE children SET child_id = ? WHERE id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $child_id, $last_id);
        $update_stmt->execute();
        $update_stmt->close();

        $vaccine_ids = $_POST['vaccine_ids'];
        $schedule_ids = $_POST['schedule_ids'];
        $administered_dates = $_POST['administered_dates'];

        for ($i = 0; $i < count($vaccine_ids); $i++) {
            $vaccine_id = $vaccine_ids[$i];
            $schedule_id = $schedule_ids[$i];
            $administered_date = $administered_dates[$i];

            if (empty($administered_date)) {
                $schedule_query = "SELECT months_after_birth FROM vaccine_schedules WHERE schedule_id = ?";
                $schedule_stmt = $conn->prepare($schedule_query);
                $schedule_stmt->bind_param("i", $schedule_id);
                $schedule_stmt->execute();
                $schedule_stmt->bind_result($months_after_birth);
                $schedule_stmt->fetch();
                $schedule_stmt->close();

                $whole_months = floor($months_after_birth);
                $remaining_days = round(($months_after_birth - $whole_months) * 30.44);
                $schedule_date = date(
                    'Y-m-d',
                    strtotime("+$whole_months months +$remaining_days days", strtotime($birth_date))
                );

                $vaccine_status = 'Pending';
                $remarks = 'Scheduled';
            } else {
                $vaccine_status = 'Completed';
                $schedule_date = $administered_date;
                $remarks = 'Administered';
            }

            $record_query = "INSERT INTO vaccination_records (id, vaccine_id, schedule_id, schedule_date, administered_date, vaccine_status, remarks) 
                             VALUES (?, ?, ?, ?, ?, ?, ?)";
            $record_stmt = $conn->prepare($record_query);
            $record_stmt->bind_param("iiissss", $last_id, $vaccine_id, $schedule_id, $schedule_date, $administered_date, $vaccine_status, $remarks);
            $record_stmt->execute();
            $record_stmt->close();
        }
        
        echo "<script>alert('Child information and photo uploaded successfully!'); window.location.href = 'user.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
