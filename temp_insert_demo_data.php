<?php
require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/vendor/yiisoft/yii2/Yii.php';
$dbConfig = require __DIR__ . '/config/db.php';
unset($dbConfig['class']);
$db = new yii\db\Connection($dbConfig);
$db->open();

function logLine($msg){ echo $msg . PHP_EOL; }

// Supervisor user (existing admin) so we can use user_id 1
$supervisor = $db->createCommand('SELECT user_id FROM users WHERE username=:u', [':u'=>'admin'])->queryOne();
if (!$supervisor) {
    $db->createCommand()->insert('users', [
        'role_id' => 1,
        'username' => 'supdemo',
        'password' => 'supdemo123',
        'payroll_no' => '9001',
        'name' => 'Supervisor Demo',
        'phone' => '0712000000',
        'status' => 'active',
    ])->execute();
    $supervisorId = $db->getLastInsertID('users_user_id_seq');
    logLine('Inserted new supervisor user supdemo (ID '.$supervisorId.')');
} else {
    $supervisorId = $supervisor['user_id'];
    logLine('Using existing supervisor user ID '.$supervisorId);
}

// Seed standardized demo users for all roles (if absent)
$demoUsers = [
    ['username' => 'supervisor1', 'password' => 'password123', 'role_id' => 1, 'payroll_no' => 9001, 'name' => 'Supervisor 1', 'phone' => '0712000001', 'school_id' => 2],
    ['username' => 'coordinator1', 'password' => 'password123', 'role_id' => 2, 'payroll_no' => 9002, 'name' => 'Zone Coordinator 1', 'phone' => '0712000002', 'zone_id' => 1],
    ['username' => 'tpoffice1', 'password' => 'password123', 'role_id' => 3, 'payroll_no' => 9003, 'name' => 'TP Office 1', 'phone' => '0712000003'],
    ['username' => 'chair1', 'password' => 'password123', 'role_id' => 4, 'payroll_no' => 9004, 'name' => 'Department Chair 1', 'phone' => '0712000004', 'school_id' => 2],
];

foreach ($demoUsers as $demoUser) {
    $existing = $db->createCommand('SELECT user_id FROM users WHERE username=:u', [':u' => $demoUser['username']])->queryOne();
    if (!$existing) {
        $db->createCommand()->insert('users', array_merge($demoUser, ['status' => 'active']))->execute();
        $id = $db->getLastInsertID('users_user_id_seq');
        logLine('Inserted demo user '.$demoUser['username'].' (ID '.$id.')');
    } else {
        // Ensure the demo credentials are synchronized and active
        $updateData = [
            'role_id' => $demoUser['role_id'],
            'password' => $demoUser['password'],
            'payroll_no' => $demoUser['payroll_no'],
            'name' => $demoUser['name'],
            'phone' => $demoUser['phone'],
            'status' => 'active',
        ];
        if (isset($demoUser['zone_id'])) $updateData['zone_id'] = $demoUser['zone_id'];
        if (isset($demoUser['school_id'])) $updateData['school_id'] = $demoUser['school_id'];
        $db->createCommand()->update('users', $updateData, ['user_id' => $existing['user_id']])->execute();
        logLine('Updated existing demo user '.$demoUser['username'].' (ID '.$existing['user_id'].')');
    }
}


// Ensure zone exists
$zone = $db->createCommand('SELECT zone_id FROM zone WHERE zone_name=:name', [':name'=>'Demo Zone'])->queryOne();
if (!$zone) {
    $db->createCommand()->insert('zone', [
        'zone_name' => 'Demo Zone',
    ])->execute();
    $zoneId = $db->getLastInsertID('zone_zone_id_seq');
    logLine('Inserted zone id '.$zoneId);
} else {
    $zoneId = $zone['zone_id'];
    logLine('Using existing zone id '.$zoneId);
}

// Ensure school exists
$school = $db->createCommand('SELECT school_id FROM school WHERE school_code=:code', [':code'=>'SCH-DEMO'])->queryOne();
if (!$school) {
    $db->createCommand()->insert('school', [
        'school_code' => 'SCH-DEMO',
        'school_name' => 'Demo High School',
        'zone_id' => $zoneId,
    ])->execute();
    $schoolId = $db->getLastInsertID('school_school_id_seq');
    logLine('Inserted school id '.$schoolId);
} else {
    $schoolId = $school['school_id'];
    logLine('Using existing school id '.$schoolId);
}

// Ensure at least one learning area exists
$learning = $db->createCommand('SELECT learning_area_id FROM learning_area WHERE learning_area_name=:n', [':n'=>'Chemistry'])->queryOne();
if (!$learning) {
    $db->createCommand()->insert('learning_area', [
        'learning_area_name' => 'Chemistry'
    ])->execute();
    $learningAreaId = $db->getLastInsertID('learning_area_learning_area_id_seq');
    logLine('Inserted learning_area id '.$learningAreaId);
} else {
    $learningAreaId = $learning['learning_area_id'];
    logLine('Using learning area id '.$learningAreaId);
}

// Ensure competence area exists
$comp = $db->createCommand('SELECT competence_id FROM competence_area WHERE competence_name=:n', [':n'=>'Demonstrate chemical handling'])->queryOne();
if (!$comp) {
    $db->createCommand()->insert('competence_area', [
        'competence_name' => 'Demonstrate chemical handling',
        'description' => 'Handles chemicals safely'
    ])->execute();
    $competenceId = $db->getLastInsertID('competence_area_competence_id_seq');
    logLine('Inserted competence area id '.$competenceId);
} else {
    $competenceId = $comp['competence_id'];
    logLine('Using competence area id '.$competenceId);
}

// Insert sample assessments (if not exists)
$existingAssessment = $db->createCommand('SELECT assessment_id FROM assessment WHERE examiner_user_id=:u LIMIT 1', [':u'=>$supervisorId])->queryOne();
if (!$existingAssessment) {
    for ($i=1; $i<=3; $i++) {
        $date = date('Y-m-d', strtotime("-{$i} days"));
        $db->createCommand()->insert('assessment', [
            'examiner_user_id' => $supervisorId,
            'student_reg_no' => 'STU00'.($i+1),
            'school_id' => $schoolId,
            'learning_area_id' => $learningAreaId,
            'assessment_date' => $date,
            'start_time' => '09:00:00',
            'end_time' => '09:30:00',
            'total_score' => 60 + $i*10,
            'overall_level' => ['BE','AE','ME','EE'][$i % 4],
        ])->execute();
        $assessmentId = $db->getLastInsertID('assessment_assessment_id_seq');
        logLine('Inserted assessment '.$assessmentId.' for student STU00'.($i+1));

        // insert one grade row per assessment
        $db->createCommand()->insert('grade', [
            'assessment_id' => $assessmentId,
            'competence_id' => $competenceId,
            'level' => ['BE','AE','ME','EE'][$i % 4],
            'score' => 60 + $i * 10,
            'remarks' => 'Demo grade '.($i)
        ])->execute();
        logLine('Inserted grade for assessment '.$assessmentId);
    }
} else {
    logLine('Assessment samples already exist, no insert needed.');
}

logLine('Demo data setup complete.');
