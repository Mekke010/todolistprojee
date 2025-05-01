<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "todolist");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

if (isset($_GET['id'])) {
    $task_id = intval($_GET['id']);

    // Görev gerçekten bu kullanıcıya mı ait kontrolü
    $stmt = $conn->prepare("SELECT id FROM tasks WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $task_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        // Kullanıcıya aitse sil
        $deleteStmt = $conn->prepare("DELETE FROM tasks WHERE id = ? AND user_id = ?");
        $deleteStmt->bind_param("ii", $task_id, $user_id);
        $deleteStmt->execute();
        $deleteStmt->close();
    }

    $stmt->close();
}

$conn->close();
header("Location: index.php");
exit();
