<?php
require_once __DIR__ . '/../src/db.php';
$id = $_GET['id'];
$student_id = $_GET['student_id'];

$stmt = $pdo->prepare("SELECT * FROM Grades WHERE id = ?");
$stmt->execute([$id]);
$grade = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $score = $_POST['score'];
    $date = $_POST['exam_date'];
    $pdo->prepare("UPDATE Grades SET score = ?, exam_date = ? WHERE id = ?")
        ->execute([$score, $date, $id]);
    header("Location: exam_results.php?student_id=$student_id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать оценку</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Изменение оценки</h1>
    <form method="POST" class="form-step">
        <p><label>Оценка:</label> <input type="number" name="score" value="<?= $grade['score'] ?>" min="0" max="5"></p>
        <p><label>Дата:</label> <input type="date" name="exam_date" value="<?= $grade['exam_date'] ?>"></p>
        <button type="submit" class="btn-add">Обновить</button>
        <a href="exam_results.php?student_id=<?= $student_id ?>">Отмена</a>
    </form>
</body>
</html>