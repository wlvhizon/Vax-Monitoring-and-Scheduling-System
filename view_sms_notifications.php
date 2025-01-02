<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VIEW SMS LOG</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .search-bar {
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
        }
        .search-bar input {
            width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            font-size: 14px;
        }
        table th, table td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        table th {
            background-color: #f9f9f9;
        }
        .status-Queued {
            color: blue;
        }
        .status-Pending {
            color: orange;
        }
        .status-Sent {
            color: green;
        }
        .status-Failed {
            color: red;
        }
        .status-Refunded {
            color: purple;
        }
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            text-decoration: none;
            color: #333;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border-color: #4CAF50;
        }
        .pagination a:hover {
            background-color: #ddd;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>SMS Notifications</h1>
        <div class="search-bar">
            <form method="GET">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search by recipient, status, or message..." 
                    value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </form>
        </div>
        <table>
            <thead>
                <tr>
                    <th>Message ID</th>
                    <th>Recipient</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Sender Name</th>
                    <th>Created At</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $limit = 10;
                $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                $offset = ($page - 1) * $limit;

                $search = isset($_GET['search']) ? trim($_GET['search']) : '';
                $searchQuery = '';
                if (!empty($search)) {
                    $search = $conn->real_escape_string($search);
                    $searchQuery = "WHERE recipient LIKE '%$search%' OR message LIKE '%$search%' OR status LIKE '%$search%'";
                }

                $countResult = $conn->query("SELECT COUNT(*) AS total FROM sms_notifications $searchQuery");
                $totalRecords = $countResult->fetch_assoc()['total'];
                $totalPages = ceil($totalRecords / $limit);

                $query = "SELECT message_id, recipient, message, status, sender_name, created_at 
                          FROM sms_notifications 
                          $searchQuery 
                          ORDER BY created_at DESC 
                          LIMIT $limit OFFSET $offset";
                $result = $conn->query($query);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['message_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['recipient']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['message']) . "</td>";
                        echo "<td class='status-" . htmlspecialchars($row['status']) . "'>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['created_at']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6' style='text-align:center;'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
        <div class="pagination">
            <?php
            for ($i = 1; $i <= $totalPages; $i++) {
                $active = ($i === $page) ? 'active' : '';
                $searchParam = !empty($search) ? "&search=" . urlencode($search) : '';
                echo "<a href='?page=$i$searchParam' class='$active'>$i</a>";
            }
            ?>
        </div>
    </div>
</body>
</html>
