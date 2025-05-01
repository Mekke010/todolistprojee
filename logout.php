<?php
// Oturumu başlatır, böylece oturum verilerine erişebiliriz
session_start();

// Tüm oturum değişkenlerini temizler (örneğin: $_SESSION['user_id'], $_SESSION['username'] vb.)
session_unset(); 

// Oturumu tamamen sonlandırır
session_destroy();
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8"> <!-- Türkçe karakter desteği -->
  <title>Çıkış Yapıldı</title> <!-- Tarayıcı sekmesinde görünen başlık -->

  <!-- Google Fonts üzerinden modern bir yazı tipi (Poppins) ekleniyor -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">

  <style>
    /* Sayfa genel stilleri */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Poppins', sans-serif; /* Modern font */
      background: linear-gradient(135deg, #a18cd1, #fbc2eb); /* Gradyan arka plan */
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      color: #333;
      text-align: center;
    }

    /* Kutuyu saran dış yapı */
    .message-box {
      background: #fff;
      padding: 40px;
      border-radius: 12px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.1); /* Hafif gölge */
      max-width: 400px;
    }

    /* Çıkış ikonu */
    .message-box img {
      width: 120px;
      margin-bottom: 20px;
      opacity: 0.8;
    }

    /* Başlık */
    h1 {
      font-size: 24px;
      margin-bottom: 10px;
    }

    /* Açıklama yazısı */
    p {
      font-size: 16px;
      margin-bottom: 20px;
    }

    /* Buton biçimi */
    a {
      text-decoration: none;
      background: linear-gradient(to right, #7F00FF, #E100FF); /* Renk geçişli buton */
      color: #fff;
      padding: 12px 24px;
      border-radius: 8px;
      font-weight: bold;
      transition: background 0.3s;
    }

    /* Buton üzerine gelindiğinde saydamlık efekti */
    a:hover {
      opacity: 0.9;
    }
  </style>
</head>
<body>

  <!-- Ana kutu: kullanıcıya bilgi verir -->
  <div class="message-box">
    <!-- Çıkış simgesi (görsel) -->
    <img src="https://cdn-icons-png.flaticon.com/512/1828/1828490.png" alt="Logout Icon">

    <!-- Başlık -->
    <h1>Çıkış yaptınız</h1>

    <!-- Açıklama metni -->
    <p>Görüşmek üzere! Giriş sayfasına dönmek için aşağıdaki butona tıklayın.</p>

    <!-- Giriş sayfasına yönlendiren buton -->
    <a href="login.php">🔙 Giriş Sayfasına Dön</a>
  </div>

</body>
</html>
