<?php
$file = 'vendor/kartik-v/mpdf/mpdf.php';
$content = file_get_contents($file);
$original = $content;

// Pattern 1: Replace {number} with [number]
$content = preg_replace('/\{(\d+)\}/', '[$1]', $content);

// Pattern 2: Replace {$variable} with [$variable]
$content = preg_replace('/\{(\$\w+)\}/', '[$1]', $content);

// Pattern 3: Replace {$array[...]} with [$array[...]]
// This handles {$var['key']} -> [$var['key']]
$content = preg_replace('/\{(\$\w+\[[\'"]?\w+[\'"]?\])\}/', '[$1]', $content);

// Pattern 4: Handle more complex cases like {$array['key'][0]}
$content = preg_replace('/\{(\$\w+(?:\[[\'"]?\w+[\'"]?\])+)\}/', '[$1]', $content);

if ($content === $original) {
    echo "No changes made\n";
} else{
    file_put_contents($file, $content);
    echo "Fixed mPDF curly brace syntax\n";
    echo shell_exec('php -l vendor/kartik-v/mpdf/mpdf.php');
}
?>
