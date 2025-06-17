<?php
include 'koneksi.php';

$led1 = $_GET['led1'] ?? 'OFF';
$led2 = $_GET['led2'] ?? 'OFF';

$sql = "UPDATE led_status SET led1='$led1', led2='$led2' WHERE id=1";

if (mysqli_query($conn, $sql)) {
    echo "Status LED diperbarui: LED1=$led1, LED2=$led2";
} else {
    echo "Gagal update: " . mysqli_error($conn);
}
?>
