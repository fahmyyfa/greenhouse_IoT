<?php
// ===============================
// DEBUG MODE (WAJIB SAAT TEST)
// ===============================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===============================
// DB CONNECTION
// ===============================
require __DIR__ . '/../config/database.php';

// ===============================
// AMBIL DATA (POST, BUKAN GET)
// ===============================
$suhu       = $_POST['suhu']       ?? null;
$kelembapan = $_POST['kelembapan'] ?? null;
$hujan      = $_POST['hujan']      ?? null;
$kipas      = $_POST['kipas']      ?? null;
$lampu      = $_POST['lampu']      ?? null;
$servo      = $_POST['servo']      ?? null;

// ===============================
// VALIDASI
// ===============================
if (
  $suhu === null ||
  $kelembapan === null ||
  $hujan === null ||
  $kipas === null ||
  $lampu === null ||
  $servo === null
) {
  http_response_code(400);
  die("PARAMETER TIDAK LENGKAP");
}

// ===============================
// INSERT (PREPARED STATEMENT)
// ===============================
$stmt = $conn->prepare(
  "INSERT INTO sensor_data (suhu, kelembapan, hujan, kipas, lampu, servo)
   VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
  "ddiiii",
  $suhu,
  $kelembapan,
  $hujan,
  $kipas,
  $lampu,
  $servo
);

// ===============================
// EKSEKUSI
// ===============================
if ($stmt->execute()) {
  echo "OK INSERT";
} else {
  http_response_code(500);
  echo "DB ERROR: " . $stmt->error;
}

$stmt->close();
$conn->close();
