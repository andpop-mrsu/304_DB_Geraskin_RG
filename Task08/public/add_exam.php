<?php
require_once __DIR__ . '/../src/db.php';

$group_id = $_GET['group_id'] ?? null;
$student_id = $_GET['student_id'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $st_id = $_POST['st_id'];
    $sp_id = $_POST['study_plan_id'];
    $score = $_POST['score'];
    $date = $_POST['exam_date'];

    $stmt = $pdo->prepare("INSERT INTO Grades (student_id, study_plan_id, score, exam_date) VALUES (?, ?, ?, ?)");
    $stmt->execute([$st_id, $sp_id, $score, $date]);
    header("Location: exam_results.php?student_id=$st_id");
    exit;
}

$groups = $pdo->query("SELECT id, name FROM Groups ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
$students = $group_id ? $pdo->prepare("SELECT id, full_name FROM Students WHERE group_id = ?") : null;
if ($students) $students->execute([$group_id]);

$subjects = $student_id ? $pdo->prepare("
    SELECT sp.id, sub.name 
    FROM StudyPlan sp 
    JOIN Subjects sub ON sp.subject_id = sub.id 
    JOIN Students s ON s.group_id = sp.group_id 
    WHERE s.id = ?") : null;
if ($subjects) $subjects->execute([$student_id]);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Ввод результата</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Ввод результатов экзаменов</h1>

    <form method="GET" class="form-step">
        <label>1. Выберите группу:</label>
        <select name="group_id" onchange="this.form.submit()">
            <option value="">-- выберите --</option>
            <?php foreach ($groups as $g): ?>
                <option value="<?= $g['id'] ?>" <?= $group_id == $g['id'] ? 'selected' : '' ?>><?= $g['name'] ?></option>
            <?php endforeach; ?>
        </select>
    </form>

    <?php if ($students): ?>
    <form method="GET" class="form-step">
        <input type="hidden" name="group_id" value="<?= $group_id ?>">
        <label>2. Выберите студента:</label>
        <select name="student_id" onchange="this.form.submit()">
            <option value="">-- выберите --</option>
            <?php foreach ($students->fetchAll() as $s): ?>
                <option value="<?= $s['id'] ?>" <?= $student_id == $s['id'] ? 'selected' : '' ?>><?= $s['full_name'] ?></option>
            <?php endforeach; ?>
        </select>
    </form>
    <?php endif; ?>

    <?php if ($subjects): ?>
    <form method="POST" class="form-step">
        <input type="hidden" name="st_id" value="<?= $student_id ?>">
        <p>
            <label>3. Дисциплина:</label>
            <select name="study_plan_id" required>
                <?php foreach ($subjects->fetchAll() as $sub): ?>
                    <option value="<?= $sub['id'] ?>"><?= $sub['name'] ?></option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label>Оценка (0-5):</label>
            <input type="number" name="score" min="0" max="5" required>
        </p>
        <p>
            <label>Дата сдачи:</label>
            <input type="date" name="exam_date" value="<?= date('Y-m-d') ?>" required>
        </p>
        <button type="submit" class="btn-add">Сохранить</button>
    </form>
    <?php endif; ?>
    <a href="index.php">На главную</a>
</body>
</html>