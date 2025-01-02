<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $child_id = $_POST['child_id'];
    $vaccine_name = $_POST['vaccine_name'];
    $schedule_date = $_POST['schedule_date'];

    $contact_sql = "SELECT contact_num, first_name, middle_name, last_name FROM children WHERE child_id = ?";
    $contact_stmt = $conn->prepare($contact_sql);

    if (!$contact_stmt) {
        die("Error preparing statement: " . $conn->error);
    }

    $contact_stmt->bind_param("s", $child_id);
    $contact_stmt->execute();
    $contact_result = $contact_stmt->get_result();

    if ($contact_result->num_rows > 0) {
        $child = $contact_result->fetch_assoc();
        $contact_num = $child['contact_num'];
        $child_name = $child['first_name'] . " " . $child['middle_name'] . " " . $child['last_name'];

        if (!empty($contact_num)) {

            $current_date = date('Y-m-d');
            $check_sql = "
                SELECT id 
                FROM reminders_sent 
                WHERE child_id = ? AND vaccine_name = ? AND DATE(sent_at) = ?
            ";
            $check_stmt = $conn->prepare($check_sql);

            if (!$check_stmt) {
                die("Error preparing statement: " . $conn->error);
            }

            $check_stmt->bind_param("sss", $child_id, $vaccine_name, $current_date);
            $check_stmt->execute();
            $check_result = $check_stmt->get_result();

            if ($check_result->num_rows > 0) {
                echo "<script>alert('A reminder has already been sent for this child and vaccine today.'); window.location.href = 'admin.php';</script>";
            } else {
                // Check if the vaccine schedule has passed (missed)
                $status = (strtotime($schedule_date) < strtotime($current_date)) ? 'Missed' : 'Upcoming';

                // Custom message based on status
                if ($status == 'Missed') {
                    $message = "Reminder: Your child, $child_name, missed their vaccine '$vaccine_name' which was scheduled on $schedule_date. Please reschedule.";
                } else {
                    $message = "Reminder: Your child, $child_name, is scheduled for the vaccine '$vaccine_name' on $schedule_date.";
                }

                $username = "wlvhizon1996@gmail.com";
                $password = "5F63561B-633C-68AE-FC4D-BDAA10ADCD4E";
                $authHeader = "Basic " . base64_encode("$username:$password");
                $data = [
                    "messages" => [
                        [
                            "source" => "php",
                            "body" => $message,
                            "to" => $contact_num
                        ]
                    ]
                ];
                $jsonData = json_encode($data);

                $ch = curl_init('https://rest.clicksend.com/v3/sms/send');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_HTTPHEADER, [
                    "Authorization: $authHeader",
                    "Content-Type: application/json"
                ]);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);

                $response = curl_exec($ch);
                $responseMessage = "";

                if (curl_errno($ch)) {
                    $responseMessage = "Error: " . curl_error($ch);
                } else {
                    $responseData = json_decode($response, true);

                    if ($responseData['http_code'] == 200 && isset($responseData['data']['messages'][0])) {
                        $messageData = $responseData['data']['messages'][0];

                        $message_id = $messageData['message_id'];
                        $recipient = $messageData['to'];
                        $status = $messageData['status'];
                        $message_price = $messageData['message_price'];
                        $country = $messageData['country'];
                        $carrier = $messageData['carrier'];
                        $queued_at = date('Y-m-d H:i:s', $messageData['date']);

                        $stmt = $conn->prepare("
                            INSERT INTO sms_history 
                            (message_id, recipient_phone, message_body, total_price, country, carrier, status, queued_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                        ");

                        if (!$stmt) {
                            die("Error preparing statement: " . $conn->error);
                        }

                        $stmt->bind_param(
                            "ssssssss",
                            $message_id,
                            $recipient,
                            $message,
                            $message_price,
                            $country,
                            $carrier,
                            $status,
                            $queued_at
                        );
                        $stmt->execute();
                        $stmt->close();

                        echo "<script>alert('Reminder sent successfully!'); window.location.href = 'admin.php';</script>";
                    } else {
                        echo "<script>alert('Failed to send SMS: " . $responseData['response_msg'] . "'); window.location.href = 'admin.php';</script>";
                    }
                }

                curl_close($ch);

                $insert_sql = "INSERT INTO reminders_sent (child_id, vaccine_name, sent_at) VALUES (?, ?, NOW())";
                $insert_stmt = $conn->prepare($insert_sql);

                if (!$insert_stmt) {
                    die("Error preparing statement: " . $conn->error);
                }

                $insert_stmt->bind_param("ss", $child_id, $vaccine_name);
                $insert_stmt->execute();
                $insert_stmt->close();

                echo $responseMessage;
            }

            $check_stmt->close();
        } else {
            echo "<script>alert('Contact number is missing for this child.'); window.location.href = 'admin.php';</script>";
        }
    } else {
        echo "<script>alert('Child record not found.'); window.location.href = 'admin.php';</script>";
    }

    $contact_stmt->close();
}

$conn->close();
?>
