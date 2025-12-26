<?php
require __DIR__ . '/../config/database.php';

$id = 1;

$mode  = $_POST['mode']  ?? null;
$kipas = $_POST['kipas'] ?? null;
$lampu = $_POST['lampu'] ?? null;
$servo = $_POST['servo'] ?? null;

$fields = [];

if ($mode  !== null) $fields[] = "mode='$mode'";
if ($kipas !== null) $fields[] = "kipas='$kipas'";
if ($lampu !== null) $fields[] = "lampu='$lampu'";
if ($servo !== null) $fields[] = "servo='$servo'";

if (empty($fields)) {
    http_response_code(400);
    exit("NO DATA");
}

$sql = "UPDATE control_data SET " . implode(", ", $fields) . " WHERE id = $id";

if ($conn->query($sql)) {
    echo "OK UPDATE";
} else {
    http_response_code(500);
    echo "DB ERROR";
}
