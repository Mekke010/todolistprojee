<?php
// Veritabanı bağlantı ayarları
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "todolist";

// Bağlantıyı oluştur
$conn = new mysqli($servername, $username, $password, $dbname);

// Bağlantıyı kontrol et
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}
?>
