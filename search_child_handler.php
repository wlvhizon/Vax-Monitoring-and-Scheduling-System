<?php
include 'db_connection.php';

if (isset($_POST['query'])) {
    $query = $_POST['query'];
    $sql = "SELECT id, first_name, last_name FROM children WHERE first_name LIKE ? OR last_name LIKE ? OR CAST(id AS CHAR) LIKE ? LIMIT 10";
    $stmt = $conn->prepare($sql);
    $searchTerm = "%".$query."%";
    $stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        echo "<div class = 'search-result' data-id='".$row['id']."'>".htmlspecialchars($row['first_name']." ".$row['last_name'])." (".$row['id'].")</div>";
    }
    $stmt->close();
    $conn->close();
}
?>