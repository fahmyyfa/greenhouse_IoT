<?php
require __DIR__ . '/../config/database.php';

$sql = "SELECT suhu, kelembapan, hujan, created_at
        FROM sensor_data
        ORDER BY id DESC
        LIMIT 1";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode([
        "suhu" => round($row['suhu'], 1),
        "kelembapan" => round($row['kelembapan'], 1),
        "hujan" => (int)$row['hujan'],
        "time" => $row['created_at']
    ]);
} else {
    echo json_encode([
        "suhu" => 0,
        "kelembapan" => 0,
        "hujan" => 0,
        "time" => "-"
    ]);
}
