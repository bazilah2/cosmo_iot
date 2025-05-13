<?php // index.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OpenSenseMap Viewer</title>

    <!-- Leaflet & Chart.js -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <!-- Styles -->
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }

        nav ul {
            list-style: none;
            padding: 0;
        }

        nav ul li {
            display: inline-block;
            margin-right: 15px;
        }

        #map {
            height: 400px;
            width: 100%;
            margin: 20px 0;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .sensor-card {
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 15px;
            width: 300px;
            box-shadow: 2px 2px 10px #ddd;
        }

        #timer {
            font-size: 16px;
            color: #28a745;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>

    <h1>SenseBox Dashboard</h1>
    <?php include 'menu.php'; ?>

    <div id="timer">Refreshing in 15 seconds...</div>
    <div id="data-container">
        <?php include 'box.php'; ?>
    </div>

    <script>
        let countdown = 15;

        function updateTimer() {
            document.getElementById('timer').textContent = "Refreshing in " + countdown + " seconds...";
        }

        function refreshData() {
            fetch('box.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('data-container').innerHTML = data;
                    countdown = 15;
                    updateTimer();
                })
                .catch(error => console.error("Refresh failed:", error));
        }

        // Countdown + Refresh
        setInterval(() => {
            countdown--;
            if (countdown <= 0) {
                refreshData();
            } else {
                updateTimer();
            }
        }, 1000);

        updateTimer();
    </script>
</body>
</html>
