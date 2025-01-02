<?php
include 'db_connection.php';

$sql = "SELECT * FROM notifications WHERE is_read = 0 ORDER BY created_at DESC";
$result = $conn->query($sql);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Notifications</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        table th {
            background-color: #007bff;
            color: white;
            text-align: center;
        }
        table td {
            vertical-align: middle;
        }
        .action-buttons form {
            display: inline-block;
            margin: 0 5px;
        }
        .action-buttons button {
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        .send-btn {
            background-color: #28a745;
            color: white;
        }
        .read-btn {
            background-color: #6c757d;
            color: white;
        }
        .send-btn:hover {
            background-color: #218838;
        }
        .read-btn:hover {
            background-color: #5a6268;
        }
        .no-notifications {
            text-align: center;
            font-size: 18px;
            color: #555;
        }
    </style>
</head>
<body>
    <h1>Admin Notifications</h1>

    <?php if ($result->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Notification</th>
                    <th>Child ID</th>
                    <th>Vaccine Name</th>
                    <th>Schedule Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td><?php echo htmlspecialchars($row['child_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['vaccine_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['schedule_date']); ?></td>
                        <td class="action-buttons">
                            <form method="POST" action="send_reminder.php">
                                <input type="hidden" name="child_id" value="<?php echo $row['child_id']; ?>">
                                <input type="hidden" name="vaccine_name" value="<?php echo $row['vaccine_name']; ?>">
                                <input type="hidden" name="schedule_date" value="<?php echo $row['schedule_date']; ?>">
                                <button type="submit" class="send-btn">Send Reminder</button>
                            </form>
                            <form method="POST" action="mark_as_read.php">
                                <input type="hidden" name="notification_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="read-btn">Mark as Read</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="no-notifications">No new notifications.</p>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>
