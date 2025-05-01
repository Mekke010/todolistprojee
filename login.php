<?php
session_start();
require_once 'db.php';

// Zaten giriş yapmışsa yönlendir
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

$error = '';

// Form gönderildiyse kontrol et
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = isset($_POST['email'])    ? trim($_POST['email'])    : '';
    $password = isset($_POST['password']) ? $_POST['password']       : '';

    if ($email === '' || $password === '') {
        $error = 'Lütfen e-posta ve şifrenizi girin.';
    } else {
        $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE email = ?');
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($id, $username, $hashed_password);
            $stmt->fetch();

            if (password_verify($password, $hashed_password)) {
                $_SESSION['user_id']   = $id;
                $_SESSION['username']  = $username;
                header('Location: index.php');
                exit();
            } else {
                $error = 'Şifreniz yanlış.';
            }
        } else {
            $error = 'Bu e-posta ile kayıtlı kullanıcı bulunamadı.';
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8">
  <title>Giriş Yap</title>
  <!-- Yazı tipi: Poppins -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      margin: 0;
      padding: 0;
    }

    body {
      font-family: 'Poppins', sans-serif;
      height: 100vh;
      display: flex;
      justify-content: center;
      align-items: center;

      /* 🔹 Arka plan görseli tam ekran */
      background-image: url('6333220.jpg'); /* Görselin todolistproje klasöründe olduğunu varsayıyoruz */
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
    }

    /* 🔹 Giriş kutusu */
    .container {
      background: rgba(255, 255, 255, 0.8); /* Hafif şeffaf beyaz */
      border-radius: 16px;
      padding: 40px;
      box-shadow: 0 8px 24px rgba(0,0,0,0.2);
      max-width: 400px;
      width: 100%;
      backdrop-filter: blur(4px); /* Arka planı hafif bulanıklaştırır */
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #333;
    }

    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 14px;
    }

    button {
      width: 100%;
      padding: 12px;
      background: linear-gradient(to right, #7F00FF, #E100FF);
      border: none;
      color: #fff;
      font-size: 16px;
      font-weight: bold;
      border-radius: 8px;
      cursor: pointer;
    }

    button:hover {
      opacity: 0.9;
    }

    .error {
      color: #d9534f;
      text-align: center;
      margin-bottom: 10px;
    }

    a {
      display: block;
      text-align: center;
      margin-top: 15px;
      text-decoration: none;
      color: #444;
    }
  </style>
</head>
<body>

  <!-- 🔹 Giriş Formu Kutusu -->
  <div class="container">
    <h2>Giriş Yap</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" novalidate>
      <input type="email"    name="email"    placeholder="E-posta" required>
      <input type="password" name="password" placeholder="Şifre" required>
      <button type="submit">Giriş Yap</button>
    </form>

    <a href="register.php">Hesabın yok mu? Kayıt Ol</a>
  </div>

</body>
</html>
