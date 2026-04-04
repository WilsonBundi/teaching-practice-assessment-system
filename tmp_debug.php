<?php
try {
    $pdo = new PDO('pgsql:host=localhost;port=5432;dbname=tp_assessment','postgres','kabish');
    echo "Assessments:\n";
    $stmt = $pdo->query('SELECT assessment_id, student_reg_no, examiner_user_id, archived, overall_level, assessment_date FROM assessment ORDER BY assessment_id DESC LIMIT 5');
    foreach ($stmt as $row) {
        echo "$row[assessment_id] | $row[student_reg_no] | $row[examiner_user_id] | $row[archived] | $row[overall_level] | $row[assessment_date]\n";
    }
    echo "Users:\n";
    $stmt2 = $pdo->query('SELECT user_id, username, role_id FROM users ORDER BY user_id DESC LIMIT 10');
    foreach ($stmt2 as $row) {
        echo "USER: $row[user_id] | $row[username] | $row[role_id]\n";
    }
} catch (PDOException $e) {
    echo 'DB error: ' . $e->getMessage();
}
