<?php
// Veritabanı bağlantısını çağırıyoruz
require_once 'db.php';

// Eğer URL'den "id" bilgisi geldiyse
if (isset($_GET['id'])) {
    $id = (int) $_GET['id']; // Gelen id'yi alıyoruz (int'e çevirerek güvenli hale getiriyoruz)

    // Görevi veritabanından sil
    $sql = "DELETE FROM tasks WHERE id = $id";
    $conn->query($sql);
}

// Silme işlemi bitince ana sayfaya geri yönlendir
header("Location: index.php");
exit();
?>
