<?php
require __DIR__ . '/../config/database.php';

header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');

while (true) {
    $res = $conn->query(
        "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1"
    );

    if ($row = $res->fetch_assoc()) {
        echo "data: " . json_encode($row) . "\n\n";
    } else {
        echo "data: {}\n\n";
    }

    ob_flush();
    flush();
    sleep(2);
}
