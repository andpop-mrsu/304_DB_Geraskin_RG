<?php

function getDbConnection(): PDO {
    $dbPath = __DIR__ . '/university.db';
    
    if (!file_exists($dbPath)) {
        die("Ошибка: Файл базы данных не найден. Сначала запустите php init_db.php");
    }

    try {
        $dsn = "sqlite:$dbPath";
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        return $pdo;
    } catch (PDOException $e) {
        die("Ошибка подключения к БД: " . $e->getMessage());
    }
}

function getActiveGroups(PDO $pdo): array {
    $currentYear = (int)date('Y');
    
    $sql = "
        SELECT 
            g.id, 
            g.name,
            g.admission_year,
            d.id as direction_id,
            d.name as dir_name
        FROM Groups g
        JOIN Directions d ON g.direction_id = d.id
        ORDER BY g.name ASC
    ";
    
    $stmt = $pdo->query($sql);
    $allGroups = $stmt->fetchAll();
    
    $activeGroups = [];

    foreach ($allGroups as $group) {
        if ($group['direction_id'] <= 3) {
            $duration = 4;
            $degreeName = 'Бакалавриат';
        } else {
            $duration = 2;
            $degreeName = 'Магистратура';
        }

        $gradYear = $group['admission_year'] + $duration;

        if ($gradYear > $currentYear) {
            $group['degree'] = $degreeName;
            $activeGroups[] = $group;
        }
    }
    
    return $activeGroups;
}

function getStudents(PDO $pdo, ?int $groupId = null): array {
    $currentYear = (int)date('Y');
    
    $sql = "
        SELECT 
            s.id as ticket,
            s.full_name,
            s.birth_date,
            s.gender,
            g.id as group_id,
            g.name as group_name,
            g.admission_year,
            d.id as direction_id,
            d.name as direction
        FROM Students s
        JOIN Groups g ON s.group_id = g.id
        JOIN Directions d ON g.direction_id = d.id
    ";
    
    if ($groupId !== null) {
        $sql .= " WHERE g.id = " . (int)$groupId;
    }

    $sql .= " ORDER BY g.name ASC, s.full_name ASC";
    
    $stmt = $pdo->query($sql);
    $allStudents = $stmt->fetchAll();
    
    $activeStudents = [];

    foreach ($allStudents as $student) {
        $duration = ($student['direction_id'] <= 3) ? 4 : 2;
        $gradYear = $student['admission_year'] + $duration;

        if ($gradYear > $currentYear) {
            $activeStudents[] = $student;
        }
    }

    return $activeStudents;
}