<?php
require_once 'db.php';

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Görev bilgilerini çekelim
    $sql = "SELECT * FROM tasks WHERE id = $id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $task = $result->fetch_assoc();
    } else {
        echo "Görev bulunamadı.";
        exit;
    }
} else {
    header("Location: index.php");
    exit;
}

// Form gönderildiyse güncelle
if (isset($_POST['title']) && isset($_POST['category']) && isset($_POST['priority']) && isset($_POST['due_date'])) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    $sql = "UPDATE tasks 
            SET title = '$title', category = '$category', priority = '$priority', due_date = '$due_date' 
            WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: index.php");
        exit;
    } else {
        echo "Güncelleme hatası: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Düzenle</title>
</head>
<body>

<h1>Görev Düzenle</h1>

<form method="POST">
    <input type="text" name="title" value="<?php echo htmlspecialchars($task['title']); ?>" required><br><br>
    <input type="text" name="category" value="<?php echo htmlspecialchars($task['category']); ?>" required><br><br>
    <select name="priority" required>
        <option value="Yüksek" <?php if($task['priority'] == 'Yüksek') echo 'selected'; ?>>Yüksek</option>
        <option value="Orta" <?php if($task['priority'] == 'Orta') echo 'selected'; ?>>Orta</option>
        <option value="Düşük" <?php if($task['priority'] == 'Düşük') echo 'selected'; ?>>Düşük</option>
    </select><br><br>
    <input type="date" name="due_date" value="<?php echo htmlspecialchars($task['due_date']); ?>" required><br><br>
    <input type="submit" value="Kaydet">
</form>

</body>
</html>
