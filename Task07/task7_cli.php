<?php
require_once 'db.php';

if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    exec('chcp 65001'); 
}

try {
    $pdo = getDbConnection();
    
    $groups = getActiveGroups($pdo);
    $validGroupIds = [];

    echo "\n=== СПИСОК ДОСТУПНЫХ ГРУПП ===\n";
    if (empty($groups)) {
        echo "В базе нет действующих групп.\n";
    } else {
        foreach ($groups as $group) {
            $validGroupIds[] = $group['id'];
            echo "[ID: {$group['id']}] Группа {$group['name']} ({$group['degree']})\n";
        }
    }
    echo "==============================\n";

    $selectedGroupId = null;

    while (true) {
        echo "Введите номер (ID) группы или нажмите Enter для вывода всех: ";
        $input = trim(fgets(STDIN));

        if ($input === '') {
            echo "Выбрано: Показать студентов всех групп.\n";
            $selectedGroupId = null; 
            break; 
        }
        if (!is_numeric($input)) {
            echo ">> Ошибка: Введите число!\n";
            continue; 
        }
        $id = (int)$input;
        if (!in_array($id, $validGroupIds)) {
            echo ">> Ошибка: Группы с ID $id нет в списке.\n";
            continue; 
        }
        $selectedGroupId = $id;
        echo "Выбрана группа с ID: $selectedGroupId\n";
        break; 
    }

    $students = getStudents($pdo, $selectedGroupId);

    $cols = [
        'group'  => 8,
        'dir'    => 35,
        'name'   => 30,
        'gender' => 3,
        'date'   => 12,
        'ticket' => 8
    ];

    function formatCell($text, $width) {
        $text = (string)$text;
        $len = mb_strlen($text);
        if ($len > $width) {
            return mb_strimwidth($text, 0, $width, ".."); 
        }
        return $text . str_repeat(" ", $width - $len); 
    }

    function printRow($data, $cols) {
        echo "| " . formatCell($data['group'],  $cols['group']) . 
             " | " . formatCell($data['dir'],    $cols['dir']) . 
             " | " . formatCell($data['name'],   $cols['name']) . 
             " | " . formatCell($data['gender'], $cols['gender']) . 
             " | " . formatCell($data['date'],   $cols['date']) . 
             " | " . formatCell($data['ticket'], $cols['ticket']) . " |\n";
    }

    $separator = "+-" . str_repeat("-", $cols['group']) . 
                 "-+-" . str_repeat("-", $cols['dir']) . 
                 "-+-" . str_repeat("-", $cols['name']) . 
                 "-+-" . str_repeat("-", $cols['gender']) . 
                 "-+-" . str_repeat("-", $cols['date']) . 
                 "-+-" . str_repeat("-", $cols['ticket']) . "-+\n";

    echo "\nРезультат:\n";
    echo $separator; 
    
    printRow([
        'group'  => "Группа",
        'dir'    => "Направление",
        'name'   => "ФИО",
        'gender' => "Пол",
        'date'   => "Дата рожд.",
        'ticket' => "Билет №"
    ], $cols);

    echo $separator; 
    if (empty($students)) {
        $totalLen = mb_strlen($separator) - 3;
        $msg = "Студенты не найдены";
        echo "| " . $msg . str_repeat(" ", $totalLen - 2 - mb_strlen($msg)) . " |\n";
    } else {
        foreach ($students as $s) {
            printRow([
                'group'  => $s['group_name'],
                'dir'    => $s['direction'],
                'name'   => $s['full_name'],
                'gender' => $s['gender'],
                'date'   => $s['birth_date'],
                'ticket' => $s['ticket']
            ], $cols);
        }
    }
    echo $separator;

} catch (Exception $e) {
    echo "Критическая ошибка: " . $e->getMessage();
}