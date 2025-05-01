<?php
// Oturumu başlat
session_start();

// Kullanıcı giriş yapmamışsa giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user_id'];

// Veritabanına bağlan
$conn = new mysqli("localhost", "root", "", "todolist");
if ($conn->connect_error) {
    die("Veritabanı bağlantı hatası: " . $conn->connect_error);
}

// GET ile gelen görev ID'sini al ve güvenliğe dikkat et
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Görevin gerçekten bu kullanıcıya ait olup olmadığını kontrol et
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id = ? AND user_id = ?");
$stmt->bind_param("ii", $id, $user_id);
$stmt->execute();
$result = $stmt->get_result();
$task = $result->fetch_assoc();

// Görev bulunamazsa anasayfaya yönlendir
if (!$task) {
    header("Location: index.php");
    exit();
}

$error = "";

// Form gönderildiyse güncelleme yap
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = trim($_POST["title"]);
    $category = trim($_POST["category"]);
    $priority = trim($_POST["priority"]);
    $due_date = $_POST["due_date"];

    // Tarih kontrolü: geçmiş tarih olmasın
    $today = date('Y-m-d');
    if ($due_date < $today) {
        $error = "Lütfen bugünden sonraki bir tarih seçin.";
    } else {
        // Güncelleme sorgusu
        $update = $conn->prepare("UPDATE tasks SET title = ?, category = ?, priority = ?, due_date = ? WHERE id = ? AND user_id = ?");
        $update->bind_param("ssssii", $title, $category, $priority, $due_date, $id, $user_id);
        $update->execute();
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Görev Düzenle</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins&display=swap" rel="stylesheet">
    <style>
        /* Genel gövde stilleri */
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: linear-gradient(135deg, #e2f7e1, #ffffff); /* Açık mint yeşili & beyaz */
            position: relative;
        }

        /* Sol üstteki illüstrasyon */
        .background-left {
            position: absolute;
            top: 0;
            left: 0;
            width: 500px;
            max-width: 80%;
            opacity: 0.25;
            z-index: 0;
        }

        /* Sağ üstteki illüstrasyon */
        .background-right {
            position: absolute;
            top: 0;
            right: 0;
            width: 500px;
            max-width: 80%;
            opacity: 0.25;
            z-index: 0;
        }

        /* Form kapsayıcısı */
        .form-container {
            position: relative;
            z-index: 1; /* Arka plan görsellerinin üzerinde */
            background: rgba(255, 255, 255, 0.95); /* Hafif opak beyaz kutu */
            padding: 30px;
            max-width: 500px;
            margin: 80px auto;
            border-radius: 20px;
            box-shadow: 0 4px 16px rgba(0,0,0,0.1);
        }

        /* Başlık */
        h2 {
            margin-top: 0;
            text-align: center;
        }

        /* Giriş alanları ve seçim kutuları */
        input, select {
            width: 100%;
            padding: 12px;
            margin: 10px 0 20px;
            border-radius: 8px;
            border: 1px solid #ccc;
            font-size: 15px;
        }

        /* Buton */
        button {
            width: 100%;
            background: #4CAF50;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: bold;
            font-size: 16px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        /* Hover efekti */
        button:hover {
            background: #45a049;
        }

        /* Hata mesajı */
        .error {
            color: red;
            font-weight: bold;
            text-align: center;
        }

        /* Geri dönüş linki */
        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #333;
            text-decoration: none;
        }
    </style>
</head>
<body>

    <!-- Sol üstteki arka plan görseli -->
    <img src="4457682.jpg" alt="Sol Görsel" class="background-left">

    <!-- Sağ üstteki arka plan görseli -->
    <img src="5118825.jpg" alt="Sağ Görsel" class="background-right">

    <!-- Form kutusu -->
    <div class="form-container">
        <h2>Görevi Düzenle</h2>

        <!-- Hata varsa göster -->
        <?php if (!empty($error)): ?>
            <p class="error"><?= $error ?></p>
        <?php endif; ?>

        <!-- Görev düzenleme formu -->
        <form method="POST">
            <label>Görev Başlığı</label>
            <input type="text" name="title" value="<?= htmlspecialchars($task['title']) ?>" required>

            <label>Kategori</label>
            <select name="category" required>
                <option value="">Kategori Seçin</option>
                <?php
                $categories = [
                    "İş", "Kişisel", "Alışveriş", "Okul", "Proje Teslimi", "Sınav Hazırlığı",
                    "İş Görüşmesi", "Kitap Okuma", "Dil Öğrenme", "Kurs/Seminer", "Toplantı", "Rapor Yazımı",
                    "Müşteri Görüşmesi", "Sunum Hazırlığı", "Namaz", "Dua / İbadet", "Kuran Okuma",
                    "Dini Etkinlik", "Ev İşleri", "Fatura Ödemeleri", "Sağlık Kontrolü", "Spor Yapma",
                    "Sağlıklı Beslenme", "Meditasyon", "Günlük Planlama", "Arkadaş Buluşması",
                    "Aile Ziyareti", "Etkinlik/Konser", "Diğer"
                ];
                foreach ($categories as $cat) {
                    $selected = ($task['category'] === $cat) ? 'selected' : '';
                    echo "<option value=\"$cat\" $selected>$cat</option>";
                }
                ?>
            </select>

            <label>Öncelik</label>
            <select name="priority">
                <option value="Düşük" <?= $task['priority'] == 'Düşük' ? 'selected' : '' ?>>Düşük</option>
                <option value="Orta" <?= $task['priority'] == 'Orta' ? 'selected' : '' ?>>Orta</option>
                <option value="Yüksek" <?= $task['priority'] == 'Yüksek' ? 'selected' : '' ?>>Yüksek</option>
            </select>

            <label>Teslim Tarihi</label>
            <input type="date" name="due_date" value="<?= $task['due_date'] ?>" required>

            <button type="submit">Kaydet</button>
        </form>

        <a href="index.php">↩ Geri Dön</a>
    </div>

</body>
</html>
