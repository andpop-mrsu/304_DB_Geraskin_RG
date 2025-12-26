<?php
require_once __DIR__ . '/../src/db.php';

$student_id = $_GET['student_id'] ?? null;
if (!$student_id) die("Студент не указан");

$stmt = $pdo->prepare("SELECT full_name FROM Students WHERE id = ?");
$stmt->execute([$student_id]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

$sql = "SELECT g.id, sub.name as subject_name, g.score, g.exam_date, sp.control_type 
        FROM Grades g
        JOIN StudyPlan sp ON g.study_plan_id = sp.id
        JOIN Subjects sub ON sp.subject_id = sub.id
        WHERE g.student_id = ?
        ORDER BY g.exam_date ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id]);
$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Экзамены: <?= htmlspecialchars($student['full_name']) ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Результаты экзаменов</h1>
    <p>Студент: <strong><?= htmlspecialchars($student['full_name']) ?></strong></p>

    <table>
        <thead>
            <tr>
                <th>Дата</th>
                <th>Дисциплина</th>
                <th>Тип контроля</th>
                <th>Оценка</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($grades as $g): ?>
                <tr>
                    <td><?= htmlspecialchars($g['exam_date']) ?></td>
                    <td><?= htmlspecialchars($g['subject_name']) ?></td>
                    <td><?= htmlspecialchars($g['control_type']) ?></td>
                    <td><?= $g['score'] ?></td>
                    <td class="actions">
                        <a href="edit_exam.php?id=<?= $g['id'] ?>&student_id=<?= $student_id ?>">Редактировать</a>
                        <a href="delete_exam.php?id=<?= $g['id'] ?>&student_id=<?= $student_id ?>" class="delete" onclick="return confirm('Удалить оценку?')">Удалить</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="margin-top: 20px;">
        <a href="add_exam.php" class="btn-add">Добавить результат</a>
        <a href="index.php">Назад к списку студентов</a>
    </div>
</body>
</html>