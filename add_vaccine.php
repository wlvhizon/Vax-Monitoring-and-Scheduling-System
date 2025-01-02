<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $vaccine_name = $conn->real_escape_string($_POST['vaccine_name']);
    $descriptions = $conn->real_escape_string($_POST['descriptions']);
    $schedules = array_map('floatval', array_filter($_POST['schedules']));

    if (empty($schedules)) {
        $schedules[] = 0;
    }

    if (!empty($vaccine_name)) {
        $sql = "INSERT INTO vaccines (vaccine_name, descriptions) VALUES ('$vaccine_name', '$descriptions')";
        if ($conn->query($sql) === TRUE) {
            $vaccine_id = $conn->insert_id;

            $schedule_error = false;
            $schedule_ids = [];
            foreach ($schedules as $months) {
                $schedule_sql = "INSERT INTO vaccine_schedules (vaccine_id, months_after_birth) VALUES ('$vaccine_id', '$months')";
                if ($conn->query($schedule_sql)) {
                    $schedule_ids[] = $conn->insert_id;
                } else {
                    $schedule_error = true;
                    error_log("Schedule insert error: " . $conn->error);
                    break;
                }
            }

            if (!$schedule_error) {
                $children_query = "SELECT id, birth_date FROM children";
                $children_result = $conn->query($children_query);

                if ($children_result && $children_result->num_rows > 0) {
                    while ($child = $children_result->fetch_assoc()) {
                        $child_id = $child['id'];
                        $birth_date = $child['birth_date'];

                        foreach ($schedule_ids as $schedule_id) {
                            $schedule_months_query = "SELECT months_after_birth FROM vaccine_schedules WHERE schedule_id = $schedule_id";
                            $schedule_months_result = $conn->query($schedule_months_query);
                        
                            if ($schedule_months_result && $schedule_months_result->num_rows > 0) {
                                $months = floatval($schedule_months_result->fetch_assoc()['months_after_birth']);

                                $whole_months = floor($months);
                                $remaining_days = round(($months - $whole_months) * 30);

                                $date = new DateTime($birth_date);
                                $date->modify("+$whole_months months");
                                $date->modify("+$remaining_days days");
                        
                                $scheduled_date = $date->format('Y-m-d');

                                $record_sql = "INSERT INTO vaccination_records (id, vaccine_id, schedule_id, schedule_date, vaccine_status) 
                                               VALUES ('$child_id', '$vaccine_id', '$schedule_id', '$scheduled_date', 'pending')";
                                if (!$conn->query($record_sql)) {
                                    error_log("Record insert error for child ID $child_id: " . $conn->error);
                                }
                            } else {
                                error_log("Failed to fetch months for schedule_id $schedule_id: " . $conn->error);
                            }
                        }
                        
                    }
                    echo "<script>alert('Vaccine, schedules, and vaccination records added successfully!'); window.location.href = 'admin.php';</script>";
                } else {
                    error_log("No children found or query failed: " . $conn->error);
                    echo "<script>alert('No children records found to update vaccination records.');</script>";
                }
            } else {
                echo "<script>alert('Error adding one or more schedules: " . $conn->error . "');</script>";
            }
        } else {
            error_log("Vaccine insert error: " . $conn->error);
            echo "<script>alert('Error adding vaccine: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Vaccine name is required.');</script>";
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
    <head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD VACCINE</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <title>ADD VACCINE</title>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
        }
        .form-container {
            width: 400px;
            padding: 20px 30px;
            border-radius: 10px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(100px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: black;
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
            font-size: 14px;
        }
        .form-container button {
            background-color: #4CAF50;
            color: #fff;
            cursor: pointer;
        }
        .form-container button:hover {
            background-color: #45a049;
        }
        .form-container .schedule-group {
            display: flex;
            align-items: center;
            margin: 10px 0;
        }
        .form-container .schedule-group input {
            flex: 1;
            margin-right: 10px;
        }
        .form-container .add-schedule {
            margin-top: 10px;
            text-align: center;
            cursor: pointer;
            color: #4CAF50;
            font-size: 14px;
        }
        .form-container .add-schedule:hover {
            text-decoration: underline;
        }
        .message {
            margin: 10px 0;
            padding: 10px;
            color: #fff;
            border-radius: 5px;
            text-align: center;
        }
        .message.success {
            background-color: #4CAF50;
        }
        .message.error {
            background-color: #f44336;
        }
    </style>
    <script>
        function addScheduleField() {
        const scheduleContainer = document.getElementById('schedule-container');
        const newField = document.createElement('div');
        newField.classList.add('schedule-group');
        newField.innerHTML = `
            <input type="number" name="schedules[]" min="0" step="0.01" placeholder="Months after birth">
            <div style="text-align: right; margin-top: 5px;">
                <button type="button" onclick="removeScheduleField(this)">Remove</button>
            </div>
        `;
        scheduleContainer.appendChild(newField);
    }

    function removeScheduleField(button) {
        const field = button.closest('.schedule-group');
        field.remove();
    }
    </script>
    </head>
    <body>
        <div class="form-container">
            <h2>Add New Vaccine</h2>
            <?php if (!empty($message)) :?>
                <div class="message <?php echo strpos($message, 'successfully') !== false ? 'success' : 'error'; ?>">
                    <?php echo htmlspecialchars($message);?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="vaccine_name">Vaccine Name:</label>
                <input type="text" id="vaccine_name" name="vaccine_name" required>

                <label for="descriptions">Description (optional):</label>
                <textarea id="descriptions" name="descriptions" rows="4"></textarea>

                <label>Schedules (Months after birth):</label>
                <div id="schedule-container">
                    <div class="schedule-group">
                        <input type="number" name="schedules[]" min="0" step="0.01" placeholder="Months after birth">
                    </div>
                </div>
                <div class="add-schedule" onclick="addScheduleField()">+ Add Another Schedule</div>

                <button type="submit">Add Vaccine</button>
            </form>
        </div>
    </body>
</html>