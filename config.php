<?php
const DB_HOST = 'localhost';
const DB_NAME = 'todo_db';
const DB_USER = 'root';
const DB_PASS = '';

const NAMA = 'Maria Chelsea Bebo';        
const NIM  = '255314051';       
const FOTO = 'poyoyoi.jpg';         

session_start();

function db(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $pdo = new PDO(
            'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
            DB_USER, DB_PASS,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
             PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
        );
    }
    return $pdo;
}

function e(string $s): string {
    return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
}

function header_profil(): void {
    $adaFoto = is_file(__DIR__ . '/' . FOTO);
    echo '<header class="profil">';
    if ($adaFoto) {
        echo '<img src="' . e(FOTO) . '" alt="Foto ' . e(NAMA) . '">';
    } else {
        echo '<div class="avatar">' . e(strtoupper(substr(NAMA, 0, 1))) . '</div>';
    }
    echo '<div><h2>' . e(NAMA) . '</h2><p>NIM ' . e(NIM) . '</p></div></header>';
}
