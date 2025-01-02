<?php
session_start();
include 'db_connection.php';

if (isset($_SESSION['username'])) {
    if ($_SESSION['roles'] == 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: user.php");
    }
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $pword = $_POST['pword'];

    $username = $conn->real_escape_string($username);
    $pword = $conn->real_escape_string($pword);

    $sql = "SELECT * FROM users WHERE username='$username'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        if (password_verify($pword, $user['pword'])) {

            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['roles'] = $user['roles'];

            if ($user['roles'] == 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: user.php");
            }
            exit;
        } else {
            $error = "Invalid username or password";
        }
    } else {
        $error = "Invalid username or password";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>LOGIN</title>
        <link rel="icon" type="image/x-icon" href="redcross.png">
        <link rel="stylesheet" href="style.css">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
        <style>
            body {
                background: url('bg.png');
                background-size: cover;
            }
        </style>
    </head>
    <body>
        <div class="wrapper">
            <form action="" method="POST">
                <h1>Login</h1>
                <div class="input-box">
                    <input type="text" placeholder="Username" id="username" name="username" required>
                    <i class='bx bxs-user'></i>
                </div>
                <div class="input-box">
                    <input type="password" placeholder="Password" id="pword" name="pword" required>
                    <i class='bx bxs-lock-alt'></i>
                </div>
                <div class="showpass">
                    <label><input type="checkbox" onclick="showPword()">Show Password</label>
                </div>
                <script>
                    function showPword() {
                      var x = document.getElementById("pword");
                      if (x.type === "password") {
                        x.type = "text";
                      } else {
                        x.type = "password";
                      }
                    }
                </script>
                <button type="submit" class="btn">Login</button>
            </form>
            <?php if ($error != ''): ?>
            <script type="text/javascript">
                alert("<?php echo $error; ?>")
            </script>
            <?php endif; ?>
        </div>
    </body>
</html>