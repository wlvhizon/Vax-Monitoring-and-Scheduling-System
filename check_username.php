<?php
include 'db_connection.php';

if(isset($_POST['username'])){
    $username = $conn->real_escape_string($_POST['username']);
    $sql = "SELECT * FROM users WHERE username = '$username'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0){
        echo '<span style="color: red;">Username already taken</span>';
    }else{
        echo '<span style="color: green;">Username available</span>';
    }
}
?>