<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SEARCH CHILD</title>
    <link rel="icon" type="image/x-icon" href="redcross.png">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@24,400,0,0" />
    <link href='https://fonts.googleapis.com/css?family=Poppins' rel='stylesheet'>
    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .header-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: white;
        }

        .search-container {
            position: relative;
            width: 100%;
            max-width: 400px;
        }

        .search {
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 20px;
            background: #f6f6f6;
            box-shadow: 0 0 2px rgba(0, 0, 0, 0.75);
        }

        .search-input {
            font-size: 14px;
            font-family: "Poppins";
            color: #333333;
            margin-left: 10px;
            outline: none;
            border: none;
            background: transparent;
            flex-grow: 1;
        }

        .search-input::placeholder {
            color: rgba(0, 0, 0, 0.5);
        }

        .search-icon {
            color: rgba(0, 0, 0, 0.5);
        }

        .search-results {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            border: none;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            background: #fff;
            z-index: 1000;
            max-height: 200px;
            overflow-y: auto;
        }

        .search-results div {
            padding: 8px;
            cursor: pointer;
        }

        .search-results div:hover {
            background: #f0f0f0;
        }

        .child-info, .vaccination-records {
            margin-top: 20px;
            width: 100%;
        }

        .container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
            margin-top: 20px;
            width: 80%;
            max-width: 1200px;
            background: transparent;
            border: 2px solid rgba(255, 255, 255, .2);
            backdrop-filter: blur(20px);
            box-shadow: 0 0 10px rgba(0, 0, 0, .2);
            color: black;
        }

        .child-info, .vaccination-records {
            flex: 1;
            min-width: 300px;
            max-width: 48%;
        }

        @media screen and (max-width: 768px) {
            .container {
                flex-direction: column;
                align-items: center;
            }

            .child-info, .vaccination-records {
                max-width: 100%;
            }
        }
    </style>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <div class="header-container">
        <h1>Search Child</h1>
        <div class="search-container">
            <div class="search">
                <span class="search-icon material-symbols-outlined">search</span>
                <input type="text" class="search-input" id="searchBar" placeholder="Search" autocomplete="off">
            </div>
            <div id="searchResults" class="search-results"></div>
        </div>
    </div>

    <div class="container">
        <div id="childInfo" class="child-info"></div>
        <div id="vaccinationRecords" class="vaccination-records"></div>
    </div>

    <script>
        $(document).ready(function() {
            $('#searchBar').on('input', function(){
                const query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: 'search_child_handler.php',
                        method: 'POST',
                        data: { query },
                        success: function(data) {
                            $('#searchResults').html(data);
                        }
                    });
                } else {
                    $('#searchResults').html('');
                }
            });
            
            $(document).on('click', '.search-result', function () {
                const childId = $(this).data('id');
                $('#searchBar').val($(this).text());
                $('#searchResults').html('');

                $.ajax({
                    url: 'fetch_child_data_user.php',
                    method: 'POST',
                    data: { childId },
                    success: function (data) {
                        const result = JSON.parse(data);
                        $('#childInfo').html(result.childInfo);
                        $('#vaccinationRecords').html(result.vaccinationRecords);
                    }
                });
            });

            $(document).on('click', '.update-record', function () {
                const recordId = $(this).data('record-id');
                const newDate = $(this).closest('tr').find('.administered-date').val();

                if (newDate) {
                    $.ajax({
                        url: 'update_vaccination_record.php',
                        method: 'POST',
                        data: { record_id: recordId, newDate: newDate },
                        success: function (response) {
                            const res = JSON.parse(response);
                            alert(res.message);
                            location.reload();
                        },
                        error: function () {
                            alert("Failed to update vaccination record.");
                        },
                    });
                } else {
                    alert("Please select a valid date.");
                }
            });
        });
    </script>
</body>
</html>