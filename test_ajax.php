<?php
// Test the save-grid endpoint
$testData = [
    'assessmentId' => 5,
    'grades' => [
        [
            'gradeId' => null,
            'competenceId' => 3,
            'assessmentId' => 5,
            'score' => 8,
            'level' => 'EE',
            'remarks' => 'Excellent professional records'
        ],
        [
            'gradeId' => null,
            'competenceId' => 4,
            'assessmentId' => 5,
            'score' => 7,
            'level' => 'ME',
            'remarks' => 'Good lesson planning'
        ]
    ]
];

$jsonData = json_encode($testData);

$ch = curl_init('http://localhost:8080/assessment/save-grid');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Content-Length: ' . strlen($jsonData)
]);
curl_setopt($ch, CURLOPT_VERBOSE, true);
curl_setopt($ch, CURLOPT_STDERR, fopen('php://temp', 'rw+'));

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);

curl_close($ch);

echo "HTTP Code: $httpCode\n";
echo "Error: $error\n";
echo "Response length: " . strlen($response) . "\n";
echo "Response: " . substr($response, 0, 500) . "\n";
?>