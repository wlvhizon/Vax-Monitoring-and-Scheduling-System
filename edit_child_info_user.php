<?php
include 'user.php';
include 'db_connection.php';

if (isset($_GET['childId'])){
    $childId = $_GET['childId'];

    $query = "SELECT * FROM children WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $childId);
    $stmt->execute();
    $childInfo = $stmt->get_result()->fetch_assoc();
    $stmt->close();
} else {
    die("Child ID not provided.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = $_POST['first_name'];
    $lastName = $_POST['last_name'];
    $birthDate = $_POST['birth_date'];
    $zone = $_POST['zone'];
    $fFirstName = $_POST['f_first_name'];
    $fMiddleName = $_POST['f_middle_name'];
    $fLastName = $_POST['f_last_name'];
    $mFirstName = $_POST['m_first_name'];
    $mMiddleName = $_POST['m_middle_name'];
    $mLastName = $_POST['m_last_name'];
    $contactNumber = $_POST['contact_num'];

    $updateQuery = "UPDATE children 
                    SET first_name = ?, last_name = ?, birth_date = ?, zone_num = ?, 
                        f_first_name = ?, f_middle_name = ?, f_last_name = ?, 
                        m_first_name = ?, m_middle_name = ?, m_last_name = ?, 
                        contact_num = ? 
                    WHERE id = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param(
        "sssssssssssi",
        $firstName, $lastName, $birthDate, $zone,
        $fFirstName, $fMiddleName, $fLastName,
        $mFirstName, $mMiddleName, $mLastName,
        $contactNumber, $childId
    );

    if ($updateStmt->execute()) {
        echo "<script>alert('Child information updated successfully!'); window.location.href = 'user.php';</script>";
        $updateStmt->close();
        exit();
    } else {
        echo "<script>alert('Failed to update child information.'); window.location.href = 'user.php';</script>";
        $updateStmt->close();
        exit();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Child Information</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
        }

        .form-container {
            max-width: 700px;
            margin: 100px auto;
            padding: 20px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form label {
            display: block;
            font-weight: 600;
            margin-top: 15px;
            color: #555;
        }

        input[type="text"], input[type="date"], input[type="tel"], button {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }

        input[type="text"]:focus, input[type="date"]:focus, input[type="tel"]:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 4px rgba(0, 123, 255, 0.2);
        }

        h3 {
            margin-top: 30px;
            color: #444;
            font-size: 18px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 5px;
        }

        button {
            margin-top: 20px;
            background-color: #4caf50;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 18px;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        .icon {
            font-size: 24px;
            vertical-align: middle;
        }

        .button-group {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Edit Child Information</h1>
        <form method="POST">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?= htmlspecialchars($childInfo['first_name']) ?>" required>

            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?= htmlspecialchars($childInfo['last_name']) ?>" required>

            <label for="birth_date">Birth Date</label>
            <input type="date" id="birth_date" name="birth_date" value="<?= htmlspecialchars($childInfo['birth_date']) ?>" required>

            <label for="zone">Zone</label>
            <input type="text" id="zone" name="zone" value="<?= htmlspecialchars($childInfo['zone_num']) ?>" required>

            <h3>Father's Information</h3>
            <label for="f_first_name">Father's First Name</label>
            <input type="text" id="f_first_name" name="f_first_name" value="<?= htmlspecialchars($childInfo['f_first_name']) ?>" required>

            <label for="f_middle_name">Father's Middle Name</label>
            <input type="text" id="f_middle_name" name="f_middle_name" value="<?= htmlspecialchars($childInfo['f_middle_name']) ?>">

            <label for="f_last_name">Father's Last Name</label>
            <input type="text" id="f_last_name" name="f_last_name" value="<?= htmlspecialchars($childInfo['f_last_name']) ?>" required>

            <h3>Mother's Information</h3>
            <label for="m_first_name">Mother's First Name</label>
            <input type="text" id="m_first_name" name="m_first_name" value="<?= htmlspecialchars($childInfo['m_first_name']) ?>" required>

            <label for="m_middle_name">Mother's Middle Name</label>
            <input type="text" id="m_middle_name" name="m_middle_name" value="<?= htmlspecialchars($childInfo['m_middle_name']) ?>">

            <label for="m_last_name">Mother's Last Name</label>
            <input type="text" id="m_last_name" name="m_last_name" value="<?= htmlspecialchars($childInfo['m_last_name']) ?>" required>

            <label for="contact_num">Contact Number</label>
            <input type="tel" id="contact_num" name="contact_num" value="<?= htmlspecialchars($childInfo['contact_num']) ?>" required>

            <div class="button-group">
                <button type="submit">
                    <span class="material-symbols-outlined icon">save</span>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</body>
</html>