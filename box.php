<?php
// Map locations to their respective box IDs
$locations = [
    'default'  => '65c77d4345c52a00087472fb', // original box
    'newyork'  => '5f2b56f4263635001c1dd1fd', // New York
    'berlin'   => '5ea03ce2d55c30001c265c3b',
    'london'   => '5b6325dba73b2c001b7bd22b',
    'tokyo'    => '5a2707d68dc0d40019bbf7b1',
];

// Determine which location to load
$location = isset($_GET['location']) ? strtolower($_GET['location']) : 'default';
$boxId = $locations[$location] ?? $locations['default'];

$apiUrl = "https://api.opensensemap.org/boxes/$boxId";
$response = file_get_contents($apiUrl);

if ($response === FALSE) {
    echo "<p>Failed to fetch data.</p>";
    return;
}

$data = json_decode($response, true);
if (!$data) {
    echo "<p>Invalid response from API.</p>";
    return;
}

// Get coordinates
if (isset($data['currentLocation']['coordinates'])) {
    $lon = $data['currentLocation']['coordinates'][0];
    $lat = $data['currentLocation']['coordinates'][1];

    echo "<h2>Location: " . ucfirst($location) . "</h2>";
    echo "<p>Latitude: $lat</p>";
    echo "<p>Longitude: $lon</p>";

    echo '<div id="map"></div>';
    echo "
    <script>
        var map = L.map('map').setView([$lat, $lon], 16);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);
        L.marker([$lat, $lon]).addTo(map)
            .bindPopup('{$data['name']}')
            .openPopup();
    </script>";
}

// Display sensors
echo "<h3>Box: {$data['name']}</h3>";
echo "<div class='card-container'>";

$index = 0;
foreach ($data['sensors'] as $sensor) {
    $title = $sensor['title'];
    $unit = $sensor['unit'];
    $value = $sensor['lastMeasurement']['value'] ?? 'N/A';
    $time = $sensor['lastMeasurement']['createdAt'] ?? 'N/A';

    // Simulated graph data
    $labels = ['T-4', 'T-3', 'T-2', 'T-1', 'Now'];
    $values = [$value + 1, $value - 1, $value + 0.5, $value, $value];

    echo "
    <div class='sensor-card'>
        <h4>$title</h4>
        <p><strong>Value:</strong> $value $unit</p>
        <p><strong>Time:</strong> $time</p>
        <canvas id='chart$index' width='280' height='200'></canvas>
    </div>
    <script>
        const ctx$index = document.getElementById('chart$index').getContext('2d');
        new Chart(ctx$index, {
            type: 'line',
            data: {
                labels: " . json_encode($labels) . ",
                datasets: [{
                    label: '$title ($unit)',
                    data: " . json_encode($values) . ",
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    tension: 0.3
                }]
            },
            options: {
                responsive: false,
                scales: {
                    y: { beginAtZero: false }
                }
            }
        });
    </script>";
    $index++;
}
echo "</div>";
?>
