<?php
require __DIR__ . '/config/database.php';

$data = $conn->query(
  "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1"
)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
  <title>Greenhouse Monitoring</title>
</head>
<body>

<h1>Greenhouse Monitoring</h1>

<p>Suhu: <?= $data['suhu'] ?? '-' ?> °C</p>
<p>Kelembapan: <?= $data['kelembapan'] ?? '-' ?> %</p>
<p>Status Hujan: <?= ($data['hujan'] ?? 0) ? 'Hujan' : 'Tidak' ?></p>

</body>
</html>
