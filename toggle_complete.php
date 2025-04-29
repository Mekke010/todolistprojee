<?php
require_once 'db.php';

if (isset($_POST['id'])) {
    $id = (int) $_POST['id'];
    $is_completed = isset($_POST['is_completed']) ? 1 : 0;

    $sql = "UPDATE tasks SET is_completed = $is_completed WHERE id = $id";
    $conn->query($sql);
}

header("Location: index.php");
exit();
?>
