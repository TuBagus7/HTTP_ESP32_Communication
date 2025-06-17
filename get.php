<?php
include 'koneksi.php';

$sql = "SELECT led1, led2 FROM led_status WHERE id = 1";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    echo "LED1=" . $row['led1'] . ";LED2=" . $row['led2'];
} else {
    echo "LED1=OFF;LED2=OFF";
}
?>
