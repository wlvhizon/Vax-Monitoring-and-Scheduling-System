<?php
session_start();

if (!isset($_SESSION['username']) || $_SESSION['roles'] != 'admin') {
    header("Location: index.php");
    exit;
}

include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrator</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }

        body {
            min-height: 100vh;
            background: url('bg.png') no-repeat;
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            padding: 1.3rem 10%;
            background: rgba(0, 0, 0, .8);
            backdrop-filter: blur(50px);
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 100;
        }

        .welcome-message {
            font-size: 1.5rem;
            color: #fff;
            font-weight: 600;
        }

        .navbar {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .navbar a {
            font-size: 1.15rem;
            color: #fff;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s;
        }

        .navbar a:hover {
            color: #00bcd4;
        }

        .dropdown {
            position: relative;
        }

        .dropdown .content {
            display: none;
            position: absolute;
            background: rgba(0, 0, 0, .9);
            padding: 1rem;
            top: 100%;
            left: 0;
            border-radius: 5px;
            z-index: 100;
        }

        .dropdown:hover .content {
            display: block;
        }

        .dropdown .content a {
            display: block;
            color: #fff;
            font-size: 1rem;
            margin: 0.5rem 0;
            text-decoration: none;
        }

        .dropdown .content a:hover {
            color: #00bcd4;
        }

        .nested-dropdown {
            position: relative;
        }

        .nested-dropdown .nested-content {
            display: none;
            position: absolute;
            left: 100%;
            top: 0;
            background: rgba(0, 0, 0, .9);
            padding: 1rem;
            border-radius: 5px;
        }

        .nested-dropdown:hover .nested-content {
            display: block;
        }

        #check {
            display: none;
        }

        .menu_icon {
            display: none;
            font-size: 2.5rem;
            color: #fff;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .welcome-message {
                font-size: 1.2rem;
            } 

            .menu_icon {
                display: inline-flex;
            }

            #check:checked~.menu_icon  #menu {
                display: none;
            }

            .menu_icon #close {
                display: none;
            }

            #check:checked~.menu_icon  #close {
                display: block;
            }

            .navbar {
                display: none;
                position: absolute;
                top: 100%;
                left: 0;
                width: 100%;
                background: rgba(0, 0, 0, .8);
                flex-direction: column;
                padding: 1rem;
                gap: 0.5rem;
                box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .1);
            }

            #check:checked ~ .navbar {
                display: flex;
            }

            .navbar a {
                font-size: 1.1rem;
            }

            .dropdown .content,
            .nested-dropdown .nested-content {
                position: static;
                display: none;
                width: 100%;
            }

            .dropdown:hover .content,
            .nested-dropdown:hover .nested-content {
                display: block;
            }
        }
    </style>
</head>
<body>
    <header class="header">

        <div class="welcome-message">
            Welcome, Admin <?php echo isset($_SESSION['username']) ? $_SESSION['username'] : 'Guest'; ?>!
        </div>

        <input type="checkbox" id="check">
        <label for="check" class="menu_icon">
            <i id="menu" class="bx bx-menu"></i>
            <i id="close" class="bx bx-x"></i>
        </label>
        <nav class="navbar">
            <a href="?page=home">Home</a>
            <a href="?page=search_child">Search Child</a>
            <a href="?page=add_child">Add Child</a>
            <div class="dropdown">
                <a href="#">Admin</a>
                <div class="content">
                <a href="?page=check_vax">Check Dues</a>
                <div class="nested-dropdown">
                        <a href="#">SMS</a>
                        <div class="nested-content">
                            <a href="?page=send_sms">Send</a>
                            <a href="?page=sms_history">History</a>
                        </div>
                    </div>
                    <div class="nested-dropdown">
                        <a href="#">Vaccines</a>
                        <div class="nested-content">
                            <a href="?page=add_vaccine">Add</a>
                            <a href="?page=view_vaccines">View</a>
                        </div>
                    </div>
                    <div class="nested-dropdown">
                        <a href="#">Users</a>
                        <div class="nested-content">
                            <a href="?page=search_user">Search</a>
                            <a href="?page=add_user">Add</a>
                        </div>
                    </div>
                </div>
            </div>
            <a href="?page=admin_notif">Notifications</a>
            <a href="logout.php">Logout</a>
        </nav>
    </header>

    <div class="content2" style="padding-top: 150px;">
        <?php
        if (isset($_GET['page'])) {
            switch ($_GET['page']) {
                case 'home':
                    include 'home.php';
                    break;
                case 'search_child':
                    include 'search_child.php';
                    break;
                case 'add_child':
                    include 'add_child.php';
                    break;
                case 'add_user':
                    include 'add_user.php';
                    break;
                case 'search_user':
                    include 'search_user.php';
                    break;
                case 'add_vaccine':
                    include 'add_vaccine.php';
                    break;
                case 'view_vaccines':
                    include 'view_vaccines.php';
                    break;
                case 'send_sms':
                    include 'send_sms.php';
                    break;
                case 'sms_history':
                    include 'sms_history.php';
                    break;
                case 'admin_notif':
                    include 'admin_notifications.php';
                    break;
                case 'check_vax':
                    include 'check_due_vaccines.php';
                    break;
                    echo '<h2>Page not found</h2>';
                    break;
            }
        }
        ?>
    </div>
</body>
</html>