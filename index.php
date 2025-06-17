<?php
include 'koneksi.php';

// Ambil data sensor terbaru
$sensor = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 1");
$data = mysqli_fetch_assoc($sensor);

// Ambil status LED
$led = mysqli_query($conn, "SELECT * FROM led_status WHERE id = 1");
$status = mysqli_fetch_assoc($led);

// Ambil 10 data terakhir untuk riwayat
$riwayat = mysqli_query($conn, "SELECT * FROM sensor_data ORDER BY id DESC LIMIT 10");

// Jika tombol ditekan
if (isset($_GET['led1']) || isset($_GET['led2'])) {
    $led1 = $_GET['led1'] ?? $status['led1'];
    $led2 = $_GET['led2'] ?? $status['led2'];
    mysqli_query($conn, "UPDATE led_status SET led1='$led1', led2='$led2' WHERE id=1");
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Kontrol LED & Sensor ESP32</title>
    <meta http-equiv="refresh" content="5">
    <style>
        body { font-family: sans-serif; padding: 20px; }
        h2 { margin-bottom: 5px; }
        .box {
            border: 1px solid #ccc;
            padding: 20px;
            width: 300px;
            margin-bottom: 20px;
        }
        .led-control a {
            display: inline-block;
            padding: 10px 20px;
            margin: 5px;
            text-decoration: none;
            color: white;
            border-radius: 5px;
        }
        .on { background: green; }
        .off { background: red; }

        table {
            border-collapse: collapse;
            width: 100%;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #aaa;
        }
        th, td {
            padding: 8px;
            text-align: center;
        }
        th {
            background: #f2f2f2;
        }
    </style>
</head>
<body>

<h1>📡 Monitoring & Kontrol ESP32</h1>

<div class="box">
    <h2>🌡️ Data Sensor</h2>
    <p><b>Suhu:</b> <?= $data['suhu'] ?? '-' ?> °C</p>
    <p><b>Kelembapan:</b> <?= $data['kelembapan'] ?? '-' ?> %</p>
    <p><small>Terakhir update: <?= $data['waktu'] ?? '-' ?></small></p>
</div>

<div class="box led-control">
    <h2>💡 Kontrol LED</h2>

    <p>LED 1: <?= $status['led1'] ?></p>
    <a href="?led1=ON" class="on">LED1 ON</a>
    <a href="?led1=OFF" class="off">LED1 OFF</a>

    <p>LED 2: <?= $status['led2'] ?></p>
    <a href="?led2=ON" class="on">LED2 ON</a>
    <a href="?led2=OFF" class="off">LED2 OFF</a>
</div>

<!-- TABEL RIWAYAT -->
<h2>📈 Riwayat Data Sensor (10 Terbaru)</h2>
<table>
    <tr>
        <th>No</th>
        <th>Suhu (°C)</th>
        <th>Kelembapan (%)</th>
        <th>Waktu</th>
    </tr>
    <?php
    $no = 1;
    while ($row = mysqli_fetch_assoc($riwayat)) {
        echo "<tr>
                <td>{$no}</td>
                <td>{$row['suhu']}</td>
                <td>{$row['kelembapan']}</td>
                <td>{$row['waktu']}</td>
              </tr>";
        $no++;
    }
    ?>
</table>

</body>
</html>
