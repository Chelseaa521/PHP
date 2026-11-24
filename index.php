<?php
require 'config.php';

if (!isset($_SESSION['user_id'])) { header('Location: login.php'); exit; }
$uid = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aksi = $_POST['aksi'] ?? '';
    $id   = (int)($_POST['id'] ?? 0);
    $baru = null;

    if ($aksi === 'tambah') {
        $teks = trim($_POST['teks'] ?? '');
        if ($teks !== '') {
            db()->prepare('INSERT INTO todos (user_id, teks) VALUES (?, ?)')
                ->execute([$uid, mb_substr($teks, 0, 255)]);
            $baru = ['tambah', (int)db()->lastInsertId()];
        }
    } elseif ($aksi === 'selesai') {
        db()->prepare('UPDATE todos SET selesai = 1 WHERE id = ? AND user_id = ?')->execute([$id, $uid]);
        $baru = ['selesai', $id];
    } elseif ($aksi === 'hapus') {
        db()->prepare('DELETE FROM todos WHERE id = ? AND user_id = ?')->execute([$id, $uid]);
    }
    $_SESSION['anim'] = $baru;   // dipakai untuk animasi sekali saja
    header('Location: index.php'); exit;
}

$anim = $_SESSION['anim'] ?? null;
unset($_SESSION['anim']);

$st = db()->prepare('SELECT id, teks, selesai FROM todos WHERE user_id = ? ORDER BY id ASC');
$st->execute([$uid]);
$todos = $st->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>To Do List</title>
<link rel="stylesheet" href="style.css">
</head>
<body>
<main class="wrap">
  <?php header_profil(); ?>

  <section class="kartu">
    <div class="bar">
      <h1>To Do List</h1>
      <a class="keluar" href="logout.php">Keluar (<?= e($_SESSION['username']) ?>)</a>
    </div>

    <form method="post" class="tambah">
      <input type="hidden" name="aksi" value="tambah">
      <input type="text" name="teks" placeholder="Tulis kegiatan baru" maxlength="255" required autofocus aria-label="Teks to do">
      <button type="submit" class="utama">Tambah</button>
    </form>

    <?php if (!$todos): ?>
      <p class="kosong">Belum ada kegiatan. Tulis satu di atas lalu tekan Tambah.</p>
    <?php else: ?>
    <ul class="daftar">
      <?php foreach ($todos as $t):
        $kelas = $t['selesai'] ? 'selesai' : '';
        if ($anim && $anim[1] === (int)$t['id']) {
            $kelas .= $anim[0] === 'tambah' ? ' masuk' : ' baru-selesai';
        } ?>
      <li class="<?= $kelas ?>">
        <span class="teks"><?= e($t['teks']) ?></span>
        <form method="post">
          <input type="hidden" name="id" value="<?= (int)$t['id'] ?>">
          <?php if (!$t['selesai']): ?>
            <button name="aksi" value="selesai" class="kecil ok">Selesai</button>
          <?php else: ?>
            <button class="kecil ok" disabled>Selesai</button>
          <?php endif; ?>
          <button name="aksi" value="hapus" class="kecil hapus"
                  onclick="return confirm('Hapus kegiatan ini?')">Hapus</button>
        </form>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
