<?php
// Oturumu başlat
session_start();

// Giriş yapılmamışsa login'e yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Kullanıcı giriş yaptıktan sonra sadece 1 kez reminder.php sayfasına yönlendir
if (!isset($_SESSION['redirected_to_reminder'])) {
    $_SESSION['redirected_to_reminder'] = true;
    header('Location: reminder.php');
    exit();
}

// Veritabanı bağlantısı
$conn = new mysqli("localhost", "root", "", "todolist");

// Bağlantı hatası kontrolü
if ($conn->connect_error) {
    die("Bağlantı hatası: " . $conn->connect_error);
}

// Kullanıcı ID'si
$user_id = $_SESSION['user_id'];

// Görev tamamlama tıklanmışsa: durumu tersine çevir
if (isset($_GET['complete'])) {
    $task_id = intval($_GET['complete']);
    $sql = "UPDATE tasks SET is_completed = !is_completed WHERE id = $task_id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// Hata mesajı için değişken
$error = "";

// Görev ekleme işlemi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title    = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];
    $today    = date('Y-m-d');

    if ($due_date < $today) {
        $error = "Geçmiş bir tarih seçilemez.";
    } else {
        $stmt = $conn->prepare("INSERT INTO tasks (title, category, priority, due_date, user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssi", $title, $category, $priority, $due_date, $user_id);
        $stmt->execute();
        header("Location: index.php");
        exit();
    }
}

// Kategori filtresi
$filter = "";
if (isset($_GET['filter_category']) && $_GET['filter_category'] !== "") {
    $selected_category = $conn->real_escape_string($_GET['filter_category']);
    $filter = " AND category = '$selected_category'";
}

// Görevleri çek: tamamlanmış ve tamamlanmamış olarak ayır
$activeTasks = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id AND is_completed = 0 $filter ORDER BY 
    CASE priority
        WHEN 'Yüksek' THEN 1
        WHEN 'Orta' THEN 2
        WHEN 'Düşük' THEN 3
        ELSE 4
    END, due_date ASC");

$completedTasks = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id AND is_completed = 1 $filter ORDER BY due_date ASC");
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #fbc2eb, #a6c1ee);
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 80px auto 30px auto;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .logout-box {
            position: absolute;
            top: 20px;
            right: 30px;
            background-color: white;
            padding: 12px 18px;
            border-radius: 15px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }

        .logout-box a {
            color: red;
            text-decoration: none;
            font-weight: bold;
            margin-left: 10px;
        }

        h1 {
            margin-top: 0;
        }

        .task {
            background: #fff;
            padding: 15px;
            margin-bottom: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            position: relative;
        }

        .task.completed {
            background-color: #f1f1f1;
            color: #aaa;
            text-decoration: line-through;
        }

        .priority-low {
            border-left: 5px solid green;
        }

        .priority-medium {
            border-left: 5px solid orange;
        }

        .priority-high {
            border-left: 5px solid red;
        }

        .category-badge {
            background: #007bff;
            color: white;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 12px;
            position: absolute;
            top: 10px;
            right: 10px;
        }

        .due-date {
            margin-top: 8px;
            font-size: 13px;
            color: #555;
        }

        .actions a {
            margin-right: 10px;
            text-decoration: none;
            color: #007bff;
        }

        form input, form select {
            margin-right: 10px;
            margin-bottom: 15px;
            padding: 8px;
            border-radius: 8px;
            border: 1px solid #ccc;
        }

        form input[type="submit"] {
            background: #28a745;
            color: white;
            border: none;
            cursor: pointer;
        }

        .section-title {
            margin-top: 40px;
            font-size: 20px;
            color: #444;
            border-bottom: 2px solid #ccc;
            padding-bottom: 5px;
        }

        .error {
            color: red;
            font-weight: bold;
        }
    </style>
</head>
<body>

<!-- Kullanıcı kutucuğu -->
<div class="logout-box">
    Merhaba, <?= htmlspecialchars($_SESSION['username']) ?> 👋 
    <a href="logout.php">Çıkış Yap</a>
</div>

<!-- Ana kutucuk -->
<div class="container">
    <h1>Görev Listesi</h1>

    <!-- Hata mesajı -->
    <?php if (!empty($error)): ?>
        <div class="error"><?= $error ?></div>
    <?php endif; ?>
    
    <!-- Görev ekleme formu -->
    <form method="POST">
        <input type="text" name="title" placeholder="Görev başlığı" required>
        <select name="category" required>
            <option value="">Kategori</option>
            <option value="İş">İş</option>
            <option value="Kişisel">Kişisel</option>
            <option value="Alışveriş">Alışveriş</option>
            <option value="Okul">Okul</option>
            <option value="Sınav Hazırlığı">Sınav Hazırlığı</option>
            <option value="Kurs/Seminer">Kurs/Seminer</option>
            <option value="Toplantı/Randevu">Toplantı/Randevu</option>
            <option value="Dua/ İbadet">Dua/ İbadet</option>
            <option value="Dini Etkinlik">Dini Etkinlik</option>
            <option value="Fatura Ödemeleri">Fatura Ödemeleri</option>
            <option value="Sağlıklı Beslenme">Sağlıklı Beslenme</option>
            <option value="Meditasyon">Meditasyon</option>
            <option value="Günlük Planlama">Günlük Planlama</option>
            <option value="Aile Ziyareti">Aile Ziyareti</option>
            <option value="Diğer">Diğer</option>
        </select>
        <select name="priority" required>
            <option value="">Öncelik</option>
            <option value="Yüksek">Yüksek</option>
            <option value="Orta">Orta</option>
            <option value="Düşük">Düşük</option>
        </select>
        <input type="date" name="due_date" required>
        <input type="submit" value="Görev Ekle">
    </form>

    <!-- Kategori filtreleme -->
    <form method="GET" action="">
        <label for="filter_category">Kategoriye göre filtrele:</label>
        <select name="filter_category" onchange="this.form.submit()">
            <option value="">Tüm Kategoriler</option>
            <?php
            $kategoriler = [
                "İş", "Kişisel", "Alışveriş", "Okul", "Sınav Hazırlığı", "Kurs/Seminer", "Toplantı/Randevu",
                "Dua/ İbadet", "Dini Etkinlik", "Fatura Ödemeleri", "Sağlıklı Beslenme",
                "Meditasyon", "Günlük Planlama", "Aile Ziyareti", "Diğer"
            ];
            foreach ($kategoriler as $kategori) {
                $selected = (isset($_GET['filter_category']) && $_GET['filter_category'] === $kategori) ? 'selected' : '';
                echo "<option value=\"$kategori\" $selected>$kategori</option>";
            }
            ?>
        </select>
    </form>

    <!-- Yapılacaklar -->
    <div class="section-title">📝 Yapılacaklar</div>
    <?php while($row = $activeTasks->fetch_assoc()): ?>
        <?php $priorityClass = 'priority-' . strtolower($row['priority']); ?>
        <div class="task <?= $priorityClass ?> <?= $row['is_completed'] ? 'completed' : '' ?>">
            <strong><?= htmlspecialchars($row['title']) ?></strong>
            <span class="category-badge"><?= htmlspecialchars($row['category']) ?></span>
            <div class="due-date">📅 <?= htmlspecialchars($row['due_date']) ?></div>
            <div class="actions">
                <a href="?complete=<?= $row['id'] ?>">✔ Tamamla</a>
                <a href="edit.php?id=<?= $row['id'] ?>">✏️ Düzenle</a>
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">🗑️ Sil</a>
            </div>
        </div>
    <?php endwhile; ?>

    <!-- Tamamlananlar -->
    <div class="section-title">✅ Tamamlananlar</div>
    <?php while($row = $completedTasks->fetch_assoc()): ?>
        <?php $priorityClass = 'priority-' . strtolower($row['priority']); ?>
        <div class="task completed <?= $priorityClass ?>">
            <strong><?= htmlspecialchars($row['title']) ?></strong>
            <span class="category-badge"><?= htmlspecialchars($row['category']) ?></span>
            <div class="due-date">📅 <?= htmlspecialchars($row['due_date']) ?></div>
            <div class="actions">
                <a href="?complete=<?= $row['id'] ?>">↩ Geri Al</a>
                <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Silmek istediğinize emin misiniz?')">🗑️ Sil</a>
            </div>
        </div>
    <?php endwhile; ?>
</div>
</body>
</html>
