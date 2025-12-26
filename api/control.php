<?php
require __DIR__ . '/../config/database.php';

$kipas = isset($_GET['kipas']) ? (int)$_GET['kipas'] : null;
$lampu = isset($_GET['lampu']) ? (int)$_GET['lampu'] : null;

if ($kipas === null || $lampu === null) {
  die("PARAM ERROR");
}

$sql = "UPDATE control_data 
        SET kipas=$kipas, lampu=$lampu 
        WHERE id=1";

if ($conn->query($sql)) {
  echo "OK CONTROL";
} else {
  echo "DB ERROR";
}
