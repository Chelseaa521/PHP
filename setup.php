<?php
require 'config.php';

$pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS,
    [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$pdo->exec('CREATE DATABASE IF NOT EXISTS `' . DB_NAME . '` CHARACTER SET utf8mb4');
$pdo->exec('USE `' . DB_NAME . '`');

$pdo->exec('CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
)');
$pdo->exec('CREATE TABLE IF NOT EXISTS todos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    teks VARCHAR(255) NOT NULL,
    selesai TINYINT(1) NOT NULL DEFAULT 0,
    dibuat TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
)');

$st = $pdo->prepare('INSERT INTO users (username, password) VALUES (?, ?)
                     ON DUPLICATE KEY UPDATE password = VALUES(password)');
$st->execute(['admin', password_hash(NIM, PASSWORD_DEFAULT)]);

echo 'Setup selesai. User: admin, password: ' . e(NIM) . '. <a href="login.php">Ke halaman login</a>. '
   . 'Hapus file setup.php setelah ini.';
