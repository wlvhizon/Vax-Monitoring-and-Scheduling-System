<?php
include 'admin.php';
include 'db_connection.php';

$message = "";

if (isset($_GET['vaccine_id'])) {
    $vaccine_id = $_GET['vaccine_id'];

    $vaccine_query = "SELECT * FROM vaccines WHERE vaccine_id = ?";
    $stmt = $conn->prepare($vaccine_query);
    $stmt->bind_param("i", $vaccine_id);
    $stmt->execute();
    $vaccine = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    $schedules_query = "SELECT schedule_id, months_after_birth FROM vaccine_schedules WHERE vaccine_id = ?";
    $stmt = $conn->prepare($schedules_query);
    $stmt->bind_param("i", $vaccine_id);
    $stmt->execute();
    $schedules_result = $stmt->get_result();
    $schedules = [];
    while ($schedule = $schedules_result->fetch_assoc()) {
        $schedules[] = $schedule;
    }
    $stmt->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $vaccine_name = $conn->real_escape_string($_POST['vaccine_name']);
    $descriptions = $conn->real_escape_string($_POST['descriptions']);
    $schedules_input = $_POST['schedules'];
    $schedules_to_remove = isset($_POST['remove_schedules']) ? $_POST['remove_schedules'] : [];

    $update_vaccine_query = "UPDATE vaccines SET vaccine_name = ?, descriptions = ? WHERE vaccine_id = ?";
    $stmt = $conn->prepare($update_vaccine_query);
    $stmt->bind_param("ssi", $vaccine_name, $descriptions, $vaccine_id);
    $stmt->execute();
    $stmt->close();

    foreach ($schedules_to_remove as $schedule_id) {
        $delete_schedule_query = "DELETE FROM vaccine_schedules WHERE schedule_id = ?";
        $stmt = $conn->prepare($delete_schedule_query);
        $stmt->bind_param("i", $schedule_id);
        $stmt->execute();
        $stmt->close();
    }

    foreach ($schedules_input as $schedule_id => $months_after_birth) {
        if (strpos($schedule_id, 'new_') === 0) {
            $insert_schedule_query = "INSERT INTO vaccine_schedules (vaccine_id, months_after_birth) VALUES (?, ?)";
            $stmt = $conn->prepare($insert_schedule_query);
            $stmt->bind_param("id", $vaccine_id, $months_after_birth);
            $stmt->execute();
            $stmt->close();
        } else {
            $update_schedule_query = "UPDATE vaccine_schedules SET months_after_birth = ? WHERE schedule_id = ?";
            $stmt = $conn->prepare($update_schedule_query);
            $stmt->bind_param("di", $months_after_birth, $schedule_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    echo "<script>alert('Vaccine and schedules updated successfully!'); window.location.href = 'admin.php';</script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Vaccine</title>
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
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
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
        }
        .form-container input, .form-container textarea, .form-container button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            border: 1px solid #ddd;
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
        .form-container .schedule-group button {
            background-color: #f44336;
            color: #fff;
            border: none;
            border-radius: 3px;
            width: 60px;
            height: 30px;
            font-size: 12px;
            text-align: center;
            cursor: pointer;
            padding: 0;
            line-height: 30px;
        }
        .form-container .schedule-group button:hover {
            background-color: #e53935;
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
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Edit Vaccine</h2>
        <form method="POST" action="edit_vaccine.php?vaccine_id=<?php echo $vaccine_id; ?>">
            <label for="vaccine_name">Vaccine Name:</label>
            <input type="text" name="vaccine_name" value="<?php echo htmlspecialchars($vaccine['vaccine_name']); ?>" required>

            <label for="descriptions">Descriptions:</label>
            <textarea name="descriptions" required><?php echo htmlspecialchars($vaccine['descriptions']); ?></textarea>

            <label for="schedules">Schedules (Months after birth):</label>
            <div id="schedule-container">
                <?php foreach ($schedules as $schedule): ?>
                    <div class="schedule-group" id="schedule-<?php echo $schedule['schedule_id']; ?>">
                        <input type="number" 
                               name="schedules[<?php echo $schedule['schedule_id']; ?>]" 
                               value="<?php echo $schedule['months_after_birth']; ?>" 
                               min="0" step="0.01" required>
                        <button type="button" class="remove-schedule" data-id="<?php echo $schedule['schedule_id']; ?>">Remove</button>
                    </div>
                <?php endforeach; ?>
            </div>
            <input type="hidden" name="remove_schedules[]" id="remove-schedules">

            <div class="add-schedule" id="add-schedule">+ Add New Schedule</div>
            <button type="submit">Update Vaccine</button>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const scheduleContainer = document.getElementById("schedule-container");
            const addScheduleButton = document.getElementById("add-schedule");
            const removeSchedulesField = document.getElementById("remove-schedules");

            addScheduleButton.addEventListener("click", function () {
                const newScheduleId = `new_${Date.now()}`;
                const newSchedule = document.createElement("div");
                newSchedule.classList.add("schedule-group");

                newSchedule.innerHTML = `
                    <input type="number" name="schedules[${newScheduleId}]" 
                           placeholder="Months after birth" 
                           min="0" step="0.01" required>
                    <button type="button" class="remove-schedule" data-id="${newScheduleId}">Remove</button>
                `;

                scheduleContainer.appendChild(newSchedule);
            });

            scheduleContainer.addEventListener("click", function (e) {
                if (e.target.classList.contains("remove-schedule")) {
                    const scheduleId = e.target.getAttribute("data-id");

                    if (!scheduleId.startsWith("new_")) {
                        const removeSchedules = removeSchedulesField.value
                            ? removeSchedulesField.value.split(",")
                            : [];
                        removeSchedules.push(scheduleId);
                        removeSchedulesField.value = removeSchedules.join(",");
                    }

                    const scheduleGroup = e.target.closest(".schedule-group");
                    scheduleContainer.removeChild(scheduleGroup);
                }
            });
        });
    </script>
</body>
</html>
