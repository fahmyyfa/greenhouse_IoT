<?php
require __DIR__ . '/../config/database.php';

foreach ($_POST as $key => $val) {
  $conn->query("UPDATE control_data SET $key='$val' WHERE id=1");
}

echo "OK";
