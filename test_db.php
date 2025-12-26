<?php
require __DIR__ . '/config/database.php';

$r = $conn->query("SELECT COUNT(*) AS total FROM sensor_data");
$d = $r->fetch_assoc();

echo "JUMLAH DATA sensor_data = " . $d['total'];
