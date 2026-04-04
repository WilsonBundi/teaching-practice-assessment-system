<?php
// Fix deprecated PHP 5.x syntax in mPDF for PHP 8.2 compatibility
$file = 'vendor/kartik-v/mpdf/mpdf.php';
$content = file_get_contents($file);

// Replace all curly brace array/string access with square brackets
// Pattern: {$variable} or {$arr['key']} or {digit}
$content = preg_replace('/\{(\$[a-zA-Z_][a-zA-Z0-9_\[\]\'\"]*)\}/', '[$1]', $content);
$content = preg_replace('/\{(\d+)\}/', '[$1]', $content);

file_put_contents($file, $content);
echo "Fixed mPDF PHP 8.2 syntax issues\n";
?>
