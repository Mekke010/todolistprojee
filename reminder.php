<?php
session_start(); // Oturumu başlatır (giriş yapılmış mı kontrolü için gerekir)

require_once 'db.php'; // Veritabanı bağlantı dosyasını dahil eder

// Eğer kullanıcı giriş yapmamışsa, index.php'ye yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['user_id']; // Oturumdan kullanıcı ID'si alınır
$todays_date = date('Y-m-d'); // Bugünün tarihi alınır (örn. 2025-05-01)

// Kullanıcının yaklaşan (3 gün içinde) ve tamamlanmamış görevleri sorgulanır
$stmt = $conn->prepare("SELECT id, title, due_date FROM tasks WHERE user_id = ? AND is_completed = 0 AND due_date <= DATE_ADD(CURDATE(), INTERVAL 3 DAY)");

if (!$stmt) {
    // Sorguda bir hata varsa hata mesajı gösterilir ve işlem durdurulur
    die("SQL Hatası: " . $conn->error);
}

$stmt->bind_param("i", $user_id); // SQL sorgusuna kullanıcı ID'si parametre olarak bağlanır
$stmt->execute(); // Sorgu çalıştırılır
$result = $stmt->get_result(); // Sonuçlar alınır

$upcoming_tasks = []; // Yaklaşan görevleri tutacak dizi
$overdue_tasks = [];  // Geç kalınmış görevleri tutacak dizi

// Her bir görev kontrol edilerek ilgili kategoriye ayrılır
while ($row = $result->fetch_assoc()) {
    $due_date = $row['due_date'];
    if ($due_date < $todays_date) {
        $overdue_tasks[] = $row; // Bugünden önceki görevler: geç kalınmış
    } else {
        $upcoming_tasks[] = $row; // Bugün veya sonraki 3 gün içindeki görevler: yaklaşan
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Görev Hatırlatma</title>
  <!-- Google Fonts - Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #a1c4fd, #c2e9fb); /* Arka plan gradyanı */
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .reminder-box {
      background: white;
      padding: 30px 40px;
      border-radius: 16px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      max-width: 600px;
      width: 100%;
    }

    h2 {
      color: #333;
      margin-bottom: 20px;
      text-align: center;
    }

    .task {
      margin-bottom: 12px;
      padding: 10px;
      border-left: 5px solid #7f00ff;
      background: #f4f4f4;
      border-radius: 8px;
    }

    .overdue {
      border-color: #d9534f;
      background: #ffe6e6;
    }

    a.button {
      display: inline-block;
      margin-top: 20px;
      background: #7f00ff;
      color: white;
      text-decoration: none;
      padding: 10px 20px;
      border-radius: 8px;
      font-weight: bold;
      text-align: center;
    }

    a.button:hover {
      background: #5a00b3;
    }
  </style>
</head>
<body>
  <div class="reminder-box">
    <h2>Hatırlatmalar 📌</h2>

    <!-- Geç kalınan görevler varsa gösterilir -->
    <?php if (count($overdue_tasks) > 0): ?>
      <h3 style="color: #d9534f;">⏰ Geç Kaldığınız Görevler:</h3>
      <?php foreach ($overdue_tasks as $task): ?>
        <div class="task overdue">
          <?= htmlspecialchars($task['title']) ?> (<?= htmlspecialchars($task['due_date']) ?>)
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Yaklaşan görevler varsa gösterilir -->
    <?php if (count($upcoming_tasks) > 0): ?>
      <h3 style="color: #333;">📅 Yaklaşan Görevler:</h3>
      <?php foreach ($upcoming_tasks as $task): ?>
        <div class="task">
          <?= htmlspecialchars($task['title']) ?> (<?= htmlspecialchars($task['due_date']) ?>)
        </div>
      <?php endforeach; ?>
    <?php endif; ?>

    <!-- Görev yoksa kullanıcıya tebrik mesajı -->
    <?php if (count($overdue_tasks) === 0 && count($upcoming_tasks) === 0): ?>
      <p>🎉 Hiç hatırlatılacak görev bulunamadı. Harikasın!</p>
    <?php endif; ?>

    <!-- Anasayfaya dönme bağlantısı -->
    <a class="button" href="index.php">Anasayfaya Git</a>
  </div>
</body>
</html>
