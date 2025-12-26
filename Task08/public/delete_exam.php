<?php
require_once __DIR__ . '/../src/db.php';
$id = $_GET['id'];
$student_id = $_GET['student_id'];
$pdo->prepare("DELETE FROM Grades WHERE id = ?")->execute([$id]);
header("Location: exam_results.php?student_id=$student_id");