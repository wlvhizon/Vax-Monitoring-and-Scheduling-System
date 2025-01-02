<?php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD USER</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <script>
        function checkUsername() {
            const username = document.getElementById('username').value;
            if (username.length > 0) {
                const xhr = new XMLHttpRequest();
                xhr.onreadystatechange = function () {
                    if (xhr.readyState == 4 && xhr.status == 200) {
                        document.getElementById('usernameFeedback').innerHTML = xhr.responseText;
                    }
                };
                xhr.open('POST', 'check_username.php', true);
                xhr.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
                xhr.send('username=' + encodeURIComponent(username));
            } else {
                document.getElementById('usernameFeedback').innerHTML = '';
            }
        }

        function validatePassword() {
            const pword = document.getElementById('pword').value;
            const warning = document.getElementById('passwordWarning');
            if (pword.length < 8) {
                warning.style.display = 'block';
            } else {
                warning.style.display = 'none';
            }
        }
    </script>
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
            border-radius: 10px;
            padding: 20px 30px;
            width: 400px;
            text-align: center;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(100px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: black;
        }
        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            color: white;
        }
        form {
            margin-top: 15px;
        }
        .form-group {
            text-align: left;
            margin-bottom: 15px;
        }
        label {
            font-weight: 600;
            color: white;
            display: block;
            margin-bottom: 8px;
        }
        input, select, button {
            width: 100%;
            padding: 10px;
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
        .feedback {
            color: red;
            font-size: 0.9em;
        }
        #passwordWarning {
            display: none;
            color: red;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Add User</h1>
        <form method="POST" action="process_add_user.php">
            <div class="form-group">
                <label for="bhw_fullname">Full Name:</label>
                <input type="text" id="bhw_fullname" name="bhw_fullname" required>
            </div>
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" onkeyup="checkUsername()" required>
                <div id="usernameFeedback" class="feedback"></div>
            </div>
            <div class="form-group">
                <label for="pword">Password:</label>
                <input type="password" id="pword" name="pword" onkeyup="validatePassword()" required>
                <div id="passwordWarning" class="feedback">Password must be at least 8 characters long</div>
            </div>
            <div class="form-group">
                <label for="roles">Role:</label>
                <select id="roles" name="roles" required>
                    <option value="">Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="user">User</option>
                </select>
            </div>
            <button type="submit">Add User</button>
        </form>
    </div>
</body>
</html>
