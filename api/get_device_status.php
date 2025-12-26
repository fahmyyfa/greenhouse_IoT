<?php
require __DIR__ . '/../config/database.php';

$sql = "SELECT created_at 
        FROM sensor_data 
        ORDER BY id DESC 
        LIMIT 1";

$result = $conn->query($sql);

$status = "OFFLINE";

if ($result && $row = $result->fetch_assoc()) {
    $last = strtotime($row['created_at']);
    if ((time() - $last) <= 15) {
        $status = "ONLINE";
    }
}

echo json_encode(["status" => $status]);
