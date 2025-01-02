<?php
include 'admin.php';
include 'db_connection.php';

$responseMessage = "";
$users = [];

if (isset($_GET['user_id'])) {
    $user_id = $conn->real_escape_string($_GET['user_id']);
    $query = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = $conn->query($query);

    if ($result && $result->num_rows == 1) {
        $users = $result->fetch_assoc();
    } else {
        echo "User not found.";
        exit;
    }
} else {
    echo " ";
    exit;
}

if (isset($_POST['update'])) {
    $username = $conn->real_escape_string($_POST['username']);
    $bhw_fullname = $conn->real_escape_string($_POST['bhw_fullname']);
    $roles = $conn->real_escape_string($_POST['roles']);

    if (!empty($_POST['pword'])) {
        $pword = $conn->real_escape_string($_POST['pword']);
        $hashed_pword = password_hash($pword, PASSWORD_BCRYPT);
        $update_query = "UPDATE users SET username = '$username', bhw_fullname = '$bhw_fullname', roles = '$roles', pword = '$hashed_pword' WHERE user_id = '$user_id'";
    } else {
        $update_query = "UPDATE users SET username = '$username', bhw_fullname = '$bhw_fullname', roles = '$roles' WHERE user_id = '$user_id'";
    }

    if ($conn->query($update_query)) {
        $result = $conn->query("SELECT * FROM users WHERE user_id = '$user_id'");
        if ($result && $result->num_rows == 1) {
            $users = $result->fetch_assoc();
        }

        $responseMessage = "User updated successfully!";
    } else {
        $responseMessage = "Error updating user: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT USER</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            padding: 20px 30px;
            width: 400px;
            text-align: center;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            color: #333;
        }
        form {
            margin-top: 15px;
        }
        label {
            display: block;
            text-align: left;
            margin-bottom: 8px;
            font-weight: 600;
            color: #555;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        button {
            background-color: #4CAF50;
            color: white;
            border: none;
            font-weight: 600;
            cursor: pointer;
        }
        button:hover {
            background-color: #45a049;
        }
        .response {
            margin-top: 20px;
            font-size: 1em;
            color: #28a745;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Edit User</h1>
        
        <?php if ($responseMessage): ?>
            <div class="response"><?php echo $responseMessage; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($users['username']); ?>" required>

            <label for="pword">New Password:</label>
            <input type="password" name="pword" placeholder="Leave blank to keep current password">

            <label for="bhw_fullname">Full Name:</label>
            <input type="text" name="bhw_fullname" value="<?php echo htmlspecialchars($users['bhw_fullname']); ?>" required>

            <label for="roles">Role:</label>
            <select name="roles" required>
                <option value="admin" <?php if ($users['roles'] == 'admin') echo 'selected'; ?>>Admin</option>
                <option value="user" <?php if ($users['roles'] == 'user') echo 'selected'; ?>>User</option>
            </select>

            <button type="submit" name="update">Update User</button>
        </form>
    </div>
</body>
</html>