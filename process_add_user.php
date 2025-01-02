<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $bhw_fullname = $conn->real_escape_string($_POST['bhw_fullname']);
    $username = $conn->real_escape_string($_POST['username']);
    $pword = $conn->real_escape_string($_POST['pword']);
    $roles = $conn->real_escape_string($_POST['roles']);

    if (strlen($pword) >= 8){
        $hashed_pword = password_hash($pword, PASSWORD_BCRYPT);

        $sql = "INSERT INTO users (username, pword, bhw_fullname, roles) VALUES ('$username', '$hashed_pword', '$bhw_fullname', '$roles')";
        if ($conn->query($sql)===TRUE){
            echo "<script>alert('User added successfully!'); window.location.href = 'admin.php';</script>";
        }else{
            echo "<script>alert('Error adding user: " . $conn->error . "'); window.location.href = 'search_user.php';</script>";
        }
    }else{
        echo "Password must be at least 8 characters long.";
    }

    $conn->close();
}
?>