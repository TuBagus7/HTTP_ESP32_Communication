<?php
// Ambil data JSON dari ESP32
$data = json_decode(file_get_contents("php://input"), true);

// Cek apakah data lengkap
if (isset($data["suhu"]) && isset($data["kelembapan"])) {
    $suhu = $data["suhu"];
    $kelembapan = $data["kelembapan"];

    // Koneksi ke database
    $koneksi = new mysqli("localhost", "root", "", "db_http");

    if ($koneksi->connect_error) {
        die("Koneksi gagal: " . $koneksi->connect_error);
    }

    // Simpan ke tabel data_sensor (bukan dht_log)
    // Di post.php
    $sql = "INSERT INTO sensor_data (suhu, kelembapan, waktu) VALUES ('$suhu', '$kelembapan', NOW())";



    if ($koneksi->query($sql) === TRUE) {
        echo "Data berhasil disimpan ke data_sensor";
    } else {
        echo "Gagal simpan data: " . $koneksi->error;
    }

    $koneksi->close();
} else {
    echo "Data tidak lengkap";
}
?>
