<?php
include 'koneksi.php';
$result = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC");
while ($row = mysqli_fetch_assoc($result)) {
    echo "Waktu: " . $row['waktu'] . " - Suhu: " . $row['suhu'] . "°C - Kelembapan: " . $row['kelembapan'] . "%<br>";
}
?>
