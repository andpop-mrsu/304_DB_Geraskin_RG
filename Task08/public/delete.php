<?php
require_once __DIR__ . '/../src/db.php';
$id = $_GET['id'];
$pdo->prepare("DELETE FROM Students WHERE id = ?")->execute([$id]);
header("Location: index.php");