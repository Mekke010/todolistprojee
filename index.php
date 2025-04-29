<?php
// Veritabanı bağlantısını yapıyoruz
require_once 'db.php';

// GÖREV İŞARETLEME (BURAYI BURAYA EKLE)
if (isset($_GET['complete'])) {
    $task_id = intval($_GET['complete']);
    $sql = "UPDATE tasks SET is_completed = !is_completed WHERE id = $task_id";
    $conn->query($sql);
    header("Location: index.php");
    exit();
}

// Hata mesajı için değişken oluşturuyoruz
$error = "";

// Eğer form gönderildiyse (kullanıcı yeni görev eklediyse)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    $today = date('Y-m-d');

    if ($due_date < $today) {
        $error = "Geçmiş bir tarih seçilemez. Lütfen bugünden sonraki bir tarihi seçin.";
    } else {
        $sql = "INSERT INTO tasks (title, category, priority, due_date) VALUES ('$title', '$category', '$priority', '$due_date')";
        $conn->query($sql);
        header("Location: index.php");
        exit();
    }
}

$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background: #f4f4f4;
        padding: 20px;
        color: #333;
    }
    .task {
        background: white;
        padding: 15px;
        margin-bottom: 15px;
        border-radius: 10px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        position: relative;
        transition: transform 0.2s ease;
    }
    .task:hover {
        transform: scale(1.01);
    }
    .task.completed {
        text-decoration: line-through;
        color: #aaa;
        background-color: #e9ecef;
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
    .due-past {
        color: red;
        font-weight: bold;
    }
    .due-soon {
        color: orange;
    }
    .due-later {
        color: #6c757d;
    }
    .actions {
        margin-top: 10px;
    }
    .actions a {
        margin-right: 10px;
        text-decoration: none;
        color: #007bff;
    }
    </style>
</head>
<body>
<h1>Görev Listesi</h1>
<?php if (!empty($error)): ?>
    <div class="error"><?= $error ?></div>
<?php endif; ?>
<form method="POST" action="">
    <input type="text" name="title" placeholder="Görev başlığı" required>
    <select name="category" required>
        <option value="">Kategori Seçin</option>
        <option value="İş">İş</option>
        <option value="Kişisel">Kişisel</option>
        <option value="Alışveriş">Alışveriş</option>
        <option value="Okul">Okul</option>
        <option value="Proje Teslimi">Proje Teslimi</option>
        <option value="Sınav Hazırlığı">Sınav Hazırlığı</option>
        <option value="İş Görüşmesi">İş Görüşmesi</option>
        <option value="Kitap Okuma">Kitap Okuma</option>
        <option value="Dil Öğrenme">Dil Öğrenme</option>
        <option value="Kurs/Seminer">Kurs/Seminer</option>
        <option value="Toplantı">Toplantı</option>
        <option value="Rapor Yazımı">Rapor Yazımı</option>
        <option value="Müşteri Görüşmesi">Müşteri Görüşmesi</option>
        <option value="Sunum Hazırlığı">Sunum Hazırlığı</option>
        <option value="Namaz">Namaz</option>
        <option value="Dua / İbadet">Dua/İbadet</option>
        <option value="Kuran Okuma">Kuran Okuma</option>
        <option value="Dini Etkinlik">Dini Etkinlik</option>
        <option value="Ev İşleri">Ev İşleri</option>
        <option value="Fatura Ödemeleri">Fatura Ödemeleri</option>
        <option value="Sağlık Kontrolü">Sağlık Kontrolü</option>
        <option value="Spor Yapma">Spor Yapma</option>
        <option value="Sağlıklı Beslenme">Sağlıklı Beslenme</option>
        <option value="Meditasyon">Meditasyon</option>
        <option value="Günlük Planlama">Günlük Planlama</option>
        <option value="Arkadaş Buluşması">Arkadaş Buluşması</option>
        <option value="Aile Ziyareti">Aile Ziyareti</option>
        <option value="Etkinlik/Konser">Etkinlik/Konser</option>
        <option value="Diğer">Diğer</option>
    </select>
    <select name="priority" required>
        <option value="">Öncelik Seçin</option>
        <option value="Düşük">Düşük</option>
        <option value="Orta">Orta</option>
        <option value="Yüksek">Yüksek</option>
    </select>
    <input type="date" name="due_date" required>
    <input type="submit" value="Görev Ekle">
</form>

<?php while($row = $result->fetch_assoc()): ?>
    <?php
        $priorityClass = 'priority-' . strtolower($row['priority']);
        $due_date = new DateTime($row['due_date']);
        $today = new DateTime();
        $interval = $today->diff($due_date)->days;

        if ($due_date < $today) {
            $dateClass = 'due-past';
        } elseif ($interval <= 1) {
            $dateClass = 'due-soon';
        } else {
            $dateClass = 'due-later';
        }
    ?>
    <div class="task <?= $priorityClass ?> <?= $row['is_completed'] ? 'completed' : '' ?>">
        <strong><?= htmlspecialchars($row['title']) ?></strong><br>
        <span class="<?= $dateClass ?>">Son Tarih: <?= htmlspecialchars($row['due_date']) ?></span><br>
        <span class="category-badge"><?= htmlspecialchars($row['category']) ?></span>
        <div class="actions">
            <a href="?complete=<?= $row['id'] ?>">✔ Tamamla</a>
            <a href="edit.php?id=<?= $row['id'] ?>">✏ Düzenle</a>
            <a href="delete.php?id=<?= $row['id'] ?>" onclick="return confirm('Bu görevi silmek istediğinizden emin misiniz?')">🗑 Sil</a>
        </div>
    </div>
<?php endwhile; ?>

</body>
</html>
