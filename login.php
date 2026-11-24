<?php
require 'config.php';

// Buat user 'cecel' otomatis kalau belum ada (password = NIM)
$cek = db()->prepare('SELECT COUNT(*) FROM users WHERE username = ?');
$cek->execute(['cecel']);
if ($cek->fetchColumn() == 0) {
    db()->prepare('INSERT INTO users (username, password) VALUES (?, ?)')
        ->execute(['cecel', password_hash(NIM, PASSWORD_DEFAULT)]);
}

if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st = db()->prepare('SELECT id, password FROM users WHERE username = ?');
    $st->execute([trim($_POST['username'] ?? '')]);
    $u = $st->fetch();
    if ($u && password_verify($_POST['password'] ?? '', $u['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']  = (int)$u['id'];
        $_SESSION['username'] = trim($_POST['username']);
        header('Location: index.php'); exit;
    }
    $error = 'User name atau password salah. Periksa lagi lalu coba masuk kembali.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login - To Do List</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
  <?php header_profil(); ?>
  <section class="kartu login">
    <h1>Login</h1>
    <?php if ($error): ?><p class="galat" role="alert"><?= e($error) ?></p><?php endif; ?>
    <form method="post">
      <label for="username">User Name</label>
      <input id="username" name="username" type="text" autocomplete="username" required autofocus>
      <label for="password">Password</label>
      <input id="password" name="password" type="password" autocomplete="current-password" required>
      <button type="submit" class="utama">Submit</button>
    </form>
  </section>
</main>
</body>
</html>