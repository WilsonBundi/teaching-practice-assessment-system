<?php
/**
 * Generate Word-compatible document from the TECHNICAL_OVERVIEW.md file.
 * This does not use external system tools and does not affect application code.
 */

$markdownFile = __DIR__ . '/TECHNICAL_OVERVIEW.md';
if (!file_exists($markdownFile)) {
    die("Error: TECHNICAL_OVERVIEW.md not found!\n");
}

$markdown = file_get_contents($markdownFile);

function markdownToHtml($markdown) {
    $html = htmlspecialchars($markdown, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

    $html = preg_replace('/^# (.*?)$/m', '<h1 style="color: #2E75B6;">$1</h1>', $html);
    $html = preg_replace('/^## (.*?)$/m', '<h2 style="color: #5B9BD5;">$1</h2>', $html);
    $html = preg_replace('/^### (.*?)$/m', '<h3 style="color: #70AD47;">$1</h3>', $html);
    $html = preg_replace('/^#### (.*?)$/m', '<h4 style="color: #4F81BD;">$1</h4>', $html);

    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
    $html = preg_replace('/_(.*?)_/', '<em>$1</em>', $html);

    $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $html);

    $html = preg_replace_callback('/```(?:php)?\n(.*?)\n```/s', function ($matches) {
        return '<pre style="background:#2C3E50;color:#ECF0F1;padding:10px;border-radius:5px;overflow-x:auto;">' . $matches[1] . '</pre>';
    }, $html);

    $html = preg_replace('/\n\n+/', '</p><p>', $html);
    $html = '<p>' . trim($html) . '</p>';

    $html = preg_replace_callback('/(?:^|\n)(?:\*|-)\s+(.+)(?=\n|$)/', function ($matches) {
        return '<ul><li>' . trim($matches[1]) . '</li></ul>';
    }, $html);

    $html = preg_replace('/<ul>(.*?)<ul>/s', '<ul>$1</ul>', $html);
    $html = preg_replace('/<\/ul>\s*<ul>/', '', $html);

    $html = preg_replace_callback('/\|(.+)\n\|[-:\s|]+\n((?:\|.*\n?)*)/', function ($matches) {
        $rows = array_filter(explode("\n", trim($matches[0])));
        $table = '<table border="1" cellspacing="0" cellpadding="5" style="border-collapse:collapse;">';
        $header = array_shift($rows);
        $headers = array_map('trim', explode('|', trim($header, '|')));
        $table .= '<tr>';
        foreach ($headers as $cell) {
            $table .= '<th style="background:#5B9BD5;color:#ffffff;">' . trim($cell) . '</th>';
        }
        $table .= '</tr>';
        array_shift($rows);
        foreach ($rows as $row) {
            if (trim($row) === '') continue;
            $cells = array_map('trim', explode('|', trim($row, '|')));
            $table .= '<tr>';
            foreach ($cells as $cell) {
                $table .= '<td>' . trim($cell) . '</td>';
            }
            $table .= '</tr>';
        }
        $table .= '</table>';
        return $table;
    }, $html);

    return $html;
}

$htmlContent = markdownToHtml($markdown);

$docTemplate = <<<HTML
<!DOCTYPE html>
<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:w="urn:schemas-microsoft-com:office:word" xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta charset="utf-8">
<title>TP Assessment System Technical Documentation</title>
<!--[if gte mso 9]><xml>
 <w:WordDocument>
  <w:View>Print</w:View>
  <w:Zoom>100</w:Zoom>
 </w:WordDocument>
</xml><![endif]-->
<style>
body { font-family: Arial, sans-serif; color: #333; }
h1 { color: #2E75B6; }
h2 { color: #5B9BD5; }
h3 { color: #70AD47; }
pre { font-family: Consolas, monospace; }
table { width: 100%; border-collapse: collapse; margin: 15px 0; }
th, td { border: 1px solid #999; padding: 5px; }
</style>
</head>
<body>
<h1>TP Assessment System Technical Documentation</h1>
$htmlContent
</body>
</html>
HTML;

$outputDir = __DIR__ . '/documentation';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$outputPath = $outputDir . '/TP-Assessment-System-Technical-Documentation.doc';
file_put_contents($outputPath, $docTemplate);

echo "Word document generated: $outputPath\n";
