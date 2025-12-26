<?php
require_once __DIR__ . '/../src/db.php';

$filterGroup = $_GET['group_id'] ?? null;

$groups = $pdo->query("SELECT id, name FROM Groups ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

$sql = "SELECT s.id, s.full_name, g.name as group_name, s.birth_date, s.gender 
        FROM Students s 
        JOIN Groups g ON s.group_id = g.id";

$params = [];
if ($filterGroup) {
    $sql .= " WHERE s.group_id = ?";
    $params[] = $filterGroup;
}
$sql .= " ORDER BY g.name, s.full_name";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Состав учебных групп</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Состав учебных групп</h1>

    <div class="filter">
        <form method="GET">
            <label>Фильтр по группе:</label>
            <select name="group_id" onchange="this.form.submit()">
                <option value="">Все группы</option>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= $filterGroup == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($filterGroup): ?>
                <a href="index.php" style="margin-left: 10px;">Сбросить</a>
            <?php endif; ?>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>Группа</th>
                <th>ФИО студента</th>
                <th>Пол</th>
                <th>Дата рождения</th>
                <th class="actions">Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($students as $s): ?>
                <tr>
                    <td><?= htmlspecialchars($s['group_name']) ?></td>
                    <td style="text-align: left;"><?= htmlspecialchars($s['full_name']) ?></td> <!-- ФИО лучше оставить по левому краю или тоже по центру -->
                    <td><?= htmlspecialchars($s['gender']) ?></td>
                    <td><?= htmlspecialchars($s['birth_date']) ?></td>
                    <td class="actions">
                        <a href="edit.php?id=<?= $s['id'] ?>">Редактировать</a>
                        <a href="delete.php?id=<?= $s['id'] ?>" class="delete" onclick="return confirm('Удалить запись?')">Удалить</a>
                        <a href="exam_results.php?student_id=<?= $s['id'] ?>">Результаты экзаменов</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div style="text-align: left;">
        <a href="edit.php" class="btn-add">Добавить студента</a>
    </div>
</body>
</html>