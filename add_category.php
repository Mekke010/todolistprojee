<?php
session_start(); // Oturumu başlat

require_once 'db.php'; // Veritabanı bağlantısı

// Kullanıcı giriş yapmamışsa, giriş sayfasına yönlendir
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Form gönderildiyse (POST isteği geldiyse)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_name = trim($_POST['category_name']); // Formdan gelen kategori adını al ve boşlukları kırp

    if (!empty($category_name)) {
        // Veritabanına kategori ekleme işlemi
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param("s", $category_name);

        if ($stmt->execute()) {
            $success = "Kategori başarıyla eklendi!";
        } else {
            $error = "Bir hata oluştu: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Lütfen kategori adını girin.";
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Kategori Ekle</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right, #667eea, #764ba2);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .form-box {
            background: white;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        input[type="text"] {
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        button {
            margin-top: 15px;
            padding: 10px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
        }

        .message {
            margin-top: 15px;
            text-align: center;
            font-weight: bold;
        }

        .success {
            color: green;
        }

        .error {
            color: red;
        }

        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            text-decoration: none;
            color: #667eea;
        }
    </style>
</head>
<body>
    <div class="form-box">
        <h2>Yeni Kategori Ekle</h2>
        <form method="POST" action="">
            <input type="text" name="category_name" placeholder="Kategori adı girin">
            <button type="submit">Kategori Ekle</button>
        </form>

        <!-- Başarı veya hata mesajı -->
        <?php if (isset($success)): ?>
            <div class="message success"><?= htmlspecialchars($success) ?></div>
        <?php elseif (isset($error)): ?>
            <div class="message error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <a href="index.php">⬅ Ana sayfaya dön</a>
    </div>
</body>
</html>
