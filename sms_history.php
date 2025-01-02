<?php

include 'db_connection.php';

$sql = "SELECT * FROM sms_history ORDER BY queued_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMS History</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f9f9f9;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-bottom: 20px;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>SMS History</h2>
        <table>
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>Recipient</th>
                    <th>Message</th>
                    <th>Sender</th>
                    <th>Status</th>
                    <th>Cost (USD)</th>
                    <th>Queued At</th>
                    <th>Delivered At</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['message_id']) ?></td>
                            <td><?= htmlspecialchars($row['recipient_phone']) ?></td>
                            <td><?= htmlspecialchars($row['message_body']) ?></td>
                            <td><?= htmlspecialchars($row['sender']) ?></td>
                            <td><?= htmlspecialchars($row['status']) ?></td>
                            <td><?= htmlspecialchars($row['total_price']) ?></td>
                            <td><?= htmlspecialchars($row['queued_at']) ?></td>
                            <td><?= htmlspecialchars($row['delivered_at']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">No SMS history found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php
$conn->close();
?>