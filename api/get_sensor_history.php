<?php
require __DIR__ . '/../config/database.php';

$sql = "SELECT suhu, kelembapan, created_at
        FROM sensor_data
        ORDER BY id DESC
        LIMIT 20";

$result = $conn->query($sql);

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        "suhu" => (float)$row['suhu'],
        "kelembapan" => (float)$row['kelembapan'],
        "time" => date("H:i:s", strtotime($row['created_at']))
    ];
}

echo json_encode(array_reverse($data));
