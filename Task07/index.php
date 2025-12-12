<?php
require_once 'db.php';

$pdo = getDbConnection();
$groups = getActiveGroups($pdo);

$selectedGroupId = isset($_GET['group_id']) && $_GET['group_id'] !== '' ? (int)$_GET['group_id'] : null;

$students = getStudents($pdo, $selectedGroupId);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Список студентов</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container">
    <h1>Список студентов действующих групп</h1>

    <form action="index.php" method="GET" class="filter-form">
        <label for="group_select">Выберите группу:</label>
        <select name="group_id" id="group_select" onchange="this.form.submit()">
            <option value="">-- Все группы --</option>
            <?php foreach ($groups as $group): ?>
                <option value="<?= $group['id'] ?>" <?= ($selectedGroupId === $group['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($group['name']) ?> (<?= htmlspecialchars($group['degree']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </form>

    <table>
        <thead>
            <tr>
                <th>Группа</th>
                <th>Направление</th>
                <th>Студенческий билет</th>
                <th>ФИО</th>
                <th>Пол</th>
                <th>Дата рождения</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr>
                    <td colspan="6" style="text-align: center;">Студенты не найдены</td>
                </tr>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['group_name']) ?></td>
                        <td><?= htmlspecialchars($student['direction']) ?></td>
                        <td><?= htmlspecialchars($student['ticket']) ?></td>
                        <td><?= htmlspecialchars($student['full_name']) ?></td>
                        <td><?= htmlspecialchars($student['gender']) ?></td>
                        <td><?= htmlspecialchars($student['birth_date']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>