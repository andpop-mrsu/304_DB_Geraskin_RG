<?php
$dbPath = __DIR__ . '/../data/university.db';
try {
    $pdo = new PDO("sqlite:$dbPath");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON;");
} catch (PDOException $e) {
    die("Ошибка подключения к БД: " . $e->getMessage());
}