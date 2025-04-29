<?php
$conn = new mysqli("localhost", "root", "", "todolist");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$id = intval($_GET['id']);  // Güvenlik: sadece sayı kabul edilir
$conn->query("DELETE FROM tasks WHERE id = $id");

header("Location: index.php");
exit();
?>
