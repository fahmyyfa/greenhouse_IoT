<?php
require __DIR__ . '/../config/database.php';

$sql = "SELECT mode, kipas, lampu, servo 
        FROM control_data 
        WHERE id = 1 LIMIT 1";

$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo json_encode([
        "mode"  => (int)$row['mode'],
        "kipas" => (int)$row['kipas'],
        "lampu" => (int)$row['lampu'],
        "servo" => (int)$row['servo']
    ]);
} else {
    echo json_encode([
        "mode" => 0,
        "kipas" => 0,
        "lampu" => 0,
        "servo" => 90
    ]);
}
