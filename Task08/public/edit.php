<?php
require_once __DIR__ . '/../src/db.php';

$id = $_GET['id'] ?? null;
$student = null;
$error = '';

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM Students WHERE id = ?");
    $stmt->execute([$id]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name']);
    $group_id = $_POST['group_id'];
    $gender = $_POST['gender'];
    $birth = $_POST['birth_date'];

    if ($name && $group_id && $gender && $birth) {
        if ($id) {
            $stmt = $pdo->prepare("UPDATE Students SET full_name=?, group_id=?, gender=?, birth_date=? WHERE id=?");
            $stmt->execute([$name, $group_id, $gender, $birth, $id]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO Students (full_name, group_id, gender, birth_date) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $group_id, $gender, $birth]);
        }
        header("Location: index.php");
        exit;
    } else {
        $error = "Все поля обязательны для заполнения!";
    }
}

$groups = $pdo->query("SELECT id, name FROM Groups ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title><?= $id ? 'Редактировать' : 'Добавить' ?> студента</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1><?= $id ? 'Редактирование данных студента' : 'Новая запись' ?></h1>

    <?php if ($error): ?> <div class="error"><?= $error ?></div> <?php endif; ?>

    <form method="POST" class="form-step">
        <p>
            <label>ФИО:</label>
            <input type="text" name="full_name" value="<?= htmlspecialchars($student['full_name'] ?? '') ?>" required>
        </p>
        <p>
            <label>Группа:</label>
            <select name="group_id" required>
                <?php foreach ($groups as $g): ?>
                    <option value="<?= $g['id'] ?>" <?= ($student['group_id'] ?? '') == $g['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($g['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <p>
            <label>Пол:</label>
            <input type="radio" name="gender" value="М" <?= ($student['gender'] ?? 'М') == 'М' ? 'checked' : '' ?>> Мужской
            <input type="radio" name="gender" value="Ж" <?= ($student['gender'] ?? '') == 'Ж' ? 'checked' : '' ?>> Женский
        </p>
        <p>
            <label>Дата рождения:</label>
            <input type="date" name="birth_date" value="<?= htmlspecialchars($student['birth_date'] ?? '') ?>" required>
        </p>
        <button type="submit" class="btn-add">Сохранить</button>
        <a href="index.php">Отмена</a>
    </form>
</body>
</html>