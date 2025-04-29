<?php
require 'db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $query = $conn->prepare("SELECT completed FROM tasks WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();

    if ($row = $result->fetch_assoc()) {
        $newStatus = $row['completed'] ? 0 : 1;

        $update = $conn->prepare("UPDATE tasks SET completed = ? WHERE id = ?");
        $update->bind_param("ii", $newStatus, $id);
        $update->execute();
    }
}

header("Location: index.php");
exit;
?>
