<?php
include 'db_connection.php';

$responseMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = "wlvhizon1996@gmail.com";
    $password = "5F63561B-633C-68AE-FC4D-BDAA10ADCD4E";

    $to = htmlspecialchars($_POST["to"]);
    $body = htmlspecialchars($_POST["body"]);

    if (!empty($to) && !empty($body)) {
        $authHeader = "Basic " . base64_encode("$username:$password");

        $data = [
            "messages" => [
                [
                    "source" => "php",
                    "body" => $body,
                    "to" => $to
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

                $stmt = $conn->prepare("INSERT INTO sms_history (message_id, recipient_phone, message_body, total_price, country, carrier, status, queued_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssssss", $message_id, $recipient, $body, $message_price, $country, $carrier, $status, $queued_at);

                if ($stmt->execute()) {
                    $responseMessage = "Message sent and stored successfully!";
                } else {
                    $responseMessage = "Message sent but failed to store in the database: " . $stmt->error;
                }

                $stmt->close();
            } else {
                $responseMessage = "Failed to send SMS: " . $responseData['response_msg'];
            }
        }

        curl_close($ch);
    } else {
        $responseMessage = "Please fill out all fields.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send SMS</title>
    <link href="https://fonts.googleapis.com/css?family=Poppins" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            padding: 20px 30px;
            border-radius: 10px;
            width: 400px;
            text-align: center;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(100px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: black;
            
        }
        h2 {
            margin-bottom: 20px;
            color: black;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }
        button {
            padding: 10px 20px;
            border: none;
            background-color: #4CAF50;
            color: white;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .response {
            margin-top: 20px;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Send SMS</h2>
        <form method="POST" action="">
            <input type="text" name="to" placeholder="Recipient's Phone Number (e.g., +639XXXXXXXXX)" required>
            <textarea name="body" placeholder="Enter your message here" rows="5" required></textarea>
            <button type="submit">Send Message</button>
        </form>
        <?php if (!empty($responseMessage)): ?>
            <div class="response"><?php echo $responseMessage; ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
