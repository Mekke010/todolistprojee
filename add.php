<?php
// Kullanıcı oturum kontrolü
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "todolist_db");
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcının kategorilerini veritabanından çekiyoruz
$category_result = $conn->query("SELECT id, name FROM categories WHERE user_id = " . $_SESSION['user_id']);

// Görev ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $conn->real_escape_string($_POST["title"]);
    $category_id = (int)$_POST["category_id"]; // Seçilen kategori ID'si
    $priority = $conn->real_escape_string($_POST["priority"]);
    $due_date = $_POST["due_date"];

    $today = date('Y-m-d');
    if ($due_date <= $today) {
        $error = "Lütfen bugünden sonraki bir tarih seçin.";
    } else {
        // Görevi veritabanına ekle
        $conn->query("INSERT INTO tasks (title, category_id, priority, due_date, user_id) 
                      VALUES ('$title', '$category_id', '$priority', '$due_date', " . $_SESSION['user_id'] . ")");
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Ekle</title>
    <style>
        body {
            font-family: Arial;
            background: #f6f7fb;
            padding: 20px;
        }

        .form-container {
            background: #fff;
            padding: 20px;
            max-width: 500px;
            margin: auto;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.2);
        }

        input, select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
        }

        button {
            background: #4caf50;
            color: white;
            padding: 10px 16px;
            border: none;
            cursor: pointer;
        }

        .error {
            color: red;
        }

        a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
            color: #555;
        }
    </style>
</head>
<body>
<div class="form-container">
    <h2>Yeni Görev Ekle</h2>

    <!-- Hatalı tarih girilirse uyarı göster -->
    <?php if (!empty($error)): ?>
        <p class="error"><?= $error ?></p>
    <?php endif; ?>

    <!-- Görev ekleme formu -->
    <form method="POST">
        <label>Görev Başlığı</label>
        <input type="text" name="title" required>

        <!-- Kategori dropdown olarak geliyor -->
        <label>Kategori</label>
        <select name="category_id" required>
            <option value="">Kategori seçin</option>
            <?php while ($row = $category_result->fetch_assoc()): ?>
                <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['name']) ?></option>
            <?php endwhile; ?>
        </select>

        <label>Öncelik</label>
        <select name="priority">
            <option value="Düşük">Düşük</option>
            <option value="Orta">Orta</option>
            <option value="Yüksek">Yüksek</option>
        </select>

        <label>Teslim Tarihi</label>
        <input type="date" name="due_date" required>

        <button type="submit">Görev Ekle</button>
    </form>

    <a href="index.php">← Listeye Dön</a>
</div>
</body>
</html>
