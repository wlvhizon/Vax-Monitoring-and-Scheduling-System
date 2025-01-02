<?php

include 'db_connection.php';

$vaccine_query = "SELECT v.vaccine_id, v.vaccine_name, vs.schedule_id, vs.months_after_birth 
                  FROM vaccines v 
                  JOIN vaccine_schedules vs ON v.vaccine_id = vs.vaccine_id";
$vaccines = $conn->query($vaccine_query);

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADD CHILD</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f9;
        }

        .form-container {
            max-width: 700px;
            margin: 50px auto;
            padding: 20px 30px;
            border-radius: 10px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(100px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: black;
        }

        h2, h3 {
            text-align: center;
            color: black;
            margin-bottom: 20px;
        }

        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
            color: black;
        }

        input[type="text"], input[type="date"], input[type="file"], input[type="tel"], select {
            width: 100%;
            padding: 10px;
            margin: 5px 0 15px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-size: 14px;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: 600;
            color: black;
        }

        .button-group {
            text-align: center;
            margin-top: 20px;
        }

        button {
            width: 150px;
            padding: 10px;
            border: none;
            border-radius: 5px;
            background-color: #4caf50;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #45a049;
        }

        .icon {
            font-size: 24px;
            vertical-align: middle;
            margin-right: 5px;
        }

        video, canvas {
            display: block;
            margin: 20px auto;
            max-width: 100%;
            border: 1px solid #ddd;
            border-radius: 5px;
        }

        #captureBtn {
            margin-top: 10px;
            width: auto;
            padding: 8px 15px;
            background-color: #f58220;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            cursor: pointer;
        }

        #captureBtn:hover {
            background-color: #d96d1a;
        }
    </style>
    <script>
        function toUpperCase(input) {
            input.value = input.value.toUpperCase();
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h2>Add Child Information</h2>
        <form id="childForm" action="save_child.php" method="POST" enctype="multipart/form-data">
            <h3>Child Information</h3>
            <label>Last Name</label>
            <input type="text" name="last_name" oninput="toUpperCase(this)" required>
            <label>First Name</label>
            <input type="text" name="first_name" oninput="toUpperCase(this)" required>
            <label>Middle Name</label>
            <input type="text" name="middle_name" oninput="toUpperCase(this)" required>
            <label>Birthdate</label>
            <input type="date" name="birth_date" required>
            <label>Zone Number</label>
            <select name="zone_num" required>
                <option value="">-- Select Zone --</option>
                <option value="01">01</option>
                <option value="02">02</option>
                <option value="03">03</option>
                <option value="04">04</option>
                <option value="05">05</option>
                <option value="06">06</option>
                <option value="07">07</option>
            </select>
            <h3>Father's Information</h3>
            <label>Last Name</label>
            <input type="text" name="f_last_name" oninput="toUpperCase(this)" required>
            <label>First Name</label>
            <input type="text" name="f_first_name" oninput="toUpperCase(this)" required>
            <label>Middle Name</label>
            <input type="text" name="f_middle_name" oninput="toUpperCase(this)" required>
            <h3>Mother's Information</h3>
            <label>Last Name</label>
            <input type="text" name="m_last_name" oninput="toUpperCase(this)" required>
            <label>First Name</label>
            <input type="text" name="m_first_name" oninput="toUpperCase(this)" required>
            <label>Middle Name</label>
            <input type="text" name="m_middle_name" oninput="toUpperCase(this)" required>
            <label>Contact Number</label>
            <input type="tel" name="contact_num" required>
            <h3>Vaccination Records</h3>
            <table>
                <thead>
                    <tr>
                        <th>Vaccine Name</th>
                        <th>Months After Birth</th>
                        <th>Administered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($vaccines->num_rows > 0): ?>
                        <?php while ($row = $vaccines->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['vaccine_name']) ?></td>
                                <td><?= htmlspecialchars($row['months_after_birth']) ?> months</td>
                                <td>
                                    <input type="hidden" name="schedule_ids[]" value="<?= $row['schedule_id'] ?>">
                                    <input type="hidden" name="vaccine_ids[]" value="<?= $row['vaccine_id'] ?>">
                                    <input type="date" name="administered_dates[]" placeholder="yyyy-mm-dd">
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="3">No vaccines available.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
            <h3>Upload Photo</h3>
            <input type="file" id="child_photo" name="child_photo" accept="image/*">

            <h3>Take a Picture</h3>
            <video id="video" autoplay></video>
            <button type="button" id="captureBtn">
                <span class="material-symbols-outlined icon">photo_camera</span>
                Capture Photo
            </button>
            <canvas id="canvas" style="display:none;"></canvas>
            <input type="hidden" name="photo_data" id="photo_data">

            <div class="button-group">
                <button type="submit">
                    <span class="material-symbols-outlined icon">save</span>
                    Save
                </button>
            </div>
        </form>
    </div>
    <script>
        const video = document.getElementById('video');
        const canvas = document.getElementById('canvas');
        const captureBtn = document.getElementById('captureBtn');
        const photoDataInput = document.getElementById('photo_data');
        const form = document.getElementById('childForm');
        const submitBtn = document.getElementById('submitBtn');
        const photoInput = document.getElementById('child_photo');

        if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
            navigator.mediaDevices.getUserMedia({video:true}).then(function(stream) {
                video.srcObject = stream;
            });
        }

        captureBtn.addEventListener('click', function(){
            const context = canvas.getContext('2d');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            context.drawImage(video, 0, 0, canvas.width, canvas.height);
            const dataURL = canvas.toDataURL('image/png');
            photoDataInput.value = dataURL;
            alert('Photo Captured!');
        });

        form.addEventListener('submit', function(event){
            if (!photoInput.value && !photoDataInput.value){
                event.preventDefault();
                alert('Please upload or take photo');
            }
        });

        <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
            <?php if ($success_message): ?>
                showMessage("<?php echo $success_message; ?>");
                clearForm();
            <?php endif; ?>

            <?php if ($error_message): ?>
                showMessage("<?php echo $error_message; ?>");
            <?php endif; ?>
        <?php endif; ?>
    </script>
</body>
</html>