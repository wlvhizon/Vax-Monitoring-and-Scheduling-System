<?php
include 'db_connection.php';

$search_term = '';
if (isset($_POST['search'])) {
    $search_term = $conn->real_escape_string($_POST('search_tearm'));
}

$search_query = "SELECT * FROM users";
if (!empty($search_term)) {
    $search_query .= "WHERE username LIKE '$search_term$' OR bhw_fullname LIKE '$search_term$' OR roles LIKE '$search_term$'";
}

$search_result = $conn->query($search_query);
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SEARCH USERS</title>
        <link rel="icon" type="image/x-icon" href="redcross.png">
        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
        <style>
            body {
                font-family: 'Poppins', sans-serif;
                margin: 0;
                padding: 20px;
            }
            h2 {
                text-align: center;
                margin-bottom: 20px;
            }
            .search-bar {
                text-align: center;
                margin-bottom: 20px;
            }
            .search-bar input[type="text"] {
                padding: 10px;
                width: 300px;
                border: 1px solid #ddd;
                border-radius: 5px;
                margin-right: 10px;
            }
            .search-bar button {
                padding: 10px 15px;
                border: none;
                border-radius: 5px;
                background-color: #4CAF50;
                color: white;
                cursor: pointer;
            }
            .search-bar button:hover {
                background-color: #45a049;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin: 20px 0;
                background-color: white;
            }
            th, td {
                border: 1px solid #ddd;
                padding: 8px;
                text-align: left;
            }
            th {
                background-color: #f2f2f2;
            }
            .action-buttons a {
                text-decoration: none;
                padding: 8px 12px;
                border-radius: 5px;
                color: white;
                font-size: 14px;
                margin-right: 5px;
                display: inline-block;
            }
            .action-buttons a.edit-btn {
                background-color: #4CAF50;
            }
            .action-buttons a.edit-btn:hover {
                background-color: #45a049;
            }
            .action-buttons a.delete-btn {
                background-color: #f44336;
            }
            .action-buttons a.delete-btn:hover {
                background-color: #e53935;
            }
        </style>
    </head>
    <body>
        <h2>Search Users</h2>
        <form method="POST" action="search_user.php" class="search-bar">
            <input type="text" name="search_term" placeholder="Search" value="<?php echo htmlspecialchars($search_term); ?>">
            <button type="submit" name="search">Search</button>
        </form>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($search_result->num_rows > 0): ?>
                    <?php while ($row = $search_result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row['user_id']; ?></td>
                            <td><?php echo htmlspecialchars($row['username']); ?></td>
                            <td><?php echo htmlspecialchars($row['bhw_fullname']); ?></td>
                            <td><?php echo htmlspecialchars($row['roles']); ?></td>
                            <td class="action-buttons">
                                <a href="edit_user.php?user_id=<?php echo $row['user_id']; ?>" class="edit-btn">Edit</a>
                                <a href="delete_user.php?user_id=<?php echo $row['user_id']; ?>" 
                                   class="delete-btn" 
                                   onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="text-align: center;">No users found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </body>
</html>
