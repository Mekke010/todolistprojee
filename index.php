<?php
// Veritabanı bağlantısını yapıyoruz
require_once 'db.php';

// Hata mesajı için değişken oluşturuyoruz
$error = "";

// Eğer form gönderildiyse (kullanıcı yeni görev eklediyse)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $priority = $_POST['priority'];
    $due_date = $_POST['due_date'];

    // Bugünün tarihi
    $today = date('Y-m-d');

    // Tarih kontrolü: Eğer seçilen tarih bugünden küçükse hata ver
    if ($due_date < $today) {
        $error = "Geçmiş bir tarih seçilemez. Lütfen bugünden sonraki bir tarihi seçin.";
    } else {
        // SQL sorgusu ile yeni görev ekliyoruz
        $sql = "INSERT INTO tasks (title, category, priority, due_date) 
                VALUES ('$title', '$category', '$priority', '$due_date')";
        $conn->query($sql);

        // Başarıyla eklenince sayfayı yeniliyoruz
        header("Location: index.php");
        exit();
    }
}

// Veritabanındaki tüm görevleri çekiyoruz
$sql = "SELECT * FROM tasks ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>To-Do List</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f2f2f2; padding: 20px; }
        .task { background: white; padding: 10px; margin-bottom: 10px; border-radius: 5px; }
        .task.completed { text-decoration: line-through; color: gray; }
        form { margin-bottom: 20px; }
        input, select { padding: 8px; margin-right: 5px; }
        table { width: 100%; border-collapse: collapse; background: white; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #eee; }
        .error { color: red; margin-bottom: 10px; }
    </style>
</head>
<body>

    <h1>Görev Listesi</h1>

    <!-- Eğer hata varsa göster -->
    <?php if (!empty($error)): ?>
        <div class="error"><?php echo $error; ?></div>
    <?php endif; ?>

    <!-- Yeni görev eklemek için form -->
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

    <!-- Görevleri tablo şeklinde listeleme -->
    <table>
        <thead>
            <tr>
                <th>Görev</th>
                <th>Kategori</th>
                <th>Öncelik</th>
                <th>Bitiş Tarihi</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <div class="task <?php echo $row['is_completed'] ? 'completed' : ''; ?>" style="position: relative; display: flex; align-items: center; gap: 10px;">
        
        <!-- Tamamlandı checkbox -->
        <form method="POST" action="toggle_complete.php" style="display:inline;">
            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
            <input type="checkbox" name="is_completed" onchange="this.form.submit()" <?php echo $row['is_completed'] ? 'checked' : ''; ?>>
        </form>

        <!-- Görev Başlığı -->
        <?php echo htmlspecialchars($row['title']); ?> 
        - [<?php echo htmlspecialchars($row['category']); ?>]
        - (Öncelik: <?php echo htmlspecialchars($row['priority']); ?>)
        - Son tarih: <?php echo htmlspecialchars($row['due_date']); ?>

        <!-- Sil ve Düzenle Butonları -->
        <div style="position:absolute; top:10px; right:10px;">
            <a href="edit.php?id=<?php echo $row['id']; ?>">Düzenle</a> | 
            <a href="delete.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Bu görevi silmek istediğinize emin misiniz?');">Sil</a>
        </div>

    </div>
<?php endwhile; ?>

        </tbody>
    </table>

</body>
</html>
