<?php
/**
 * TP Assessment System - Technical Documentation PDF Generator
 * 
 * This script converts the TECHNICAL_OVERVIEW.md file into a comprehensive PDF
 * without affecting the running system.
 * 
 * Usage: php generate_documentation_pdf.php
 */

require 'vendor/autoload.php';

use Mpdf\Mpdf;

// Read the markdown file
$markdownFile = __DIR__ . '/TECHNICAL_OVERVIEW.md';
if (!file_exists($markdownFile)) {
    die("Error: TECHNICAL_OVERVIEW.md not found!\n");
}

$markdown = file_get_contents($markdownFile);

// Simple markdown to HTML converter
function markdownToHtml($markdown) {
    // Convert headers
    $html = preg_replace('/^# (.*?)$/m', '<h1 style="color: #2E75B6; border-bottom: 3px solid #2E75B6; padding-bottom: 10px; margin-top: 30px;">$1</h1>', $markdown);
    $html = preg_replace('/^## (.*?)$/m', '<h2 style="color: #5B9BD5; margin-top: 25px; border-left: 5px solid #5B9BD5; padding-left: 10px;">$1</h2>', $html);
    $html = preg_replace('/^### (.*?)$/m', '<h3 style="color: #70AD47; margin-top: 15px;">$1</h3>', $html);
    $html = preg_replace('/^#### (.*?)$/m', '<h4 style="color: #4F81BD;">$1</h4>', $html);
    
    // Convert bold and italic
    $html = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $html);
    $html = preg_replace('/__(.*?)__/', '<strong>$1</strong>', $html);
    $html = preg_replace('/_(.+?)_/', '<em>$1</em>', $html);
    
    // Convert links [text](url) to HTML links
    $html = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2" style="color: #5B9BD5; text-decoration: none;">$1</a>', $html);
    
    // Convert inline code
    $html = preg_replace('/`(.*?)`/', '<code style="background-color: #F0F0F0; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 12px;">$1</code>', $html);
    
    // Convert code blocks (triple backticks)
    $html = preg_replace_callback(
        '/```(?:php)?\n(.*?)\n```/s',
        function($matches) {
            return '<div style="background-color: #2C3E50; color: #ECF0F1; padding: 15px; border-radius: 5px; margin: 10px 0; overflow-x: auto; font-family: monospace; font-size: 11px; line-height: 1.4;">' 
                   . htmlspecialchars($matches[1]) 
                   . '</div>';
        },
        $html
    );
    
    // Convert tables
    $html = preg_replace_callback(
        '/\|(.+?)\n\|[-\|\s:]+\n((?:\|.+?\n)*)/m',
        function($matches) {
            $rows = array_filter(array_map('trim', explode("\n", trim($matches[1] . "\n" . $matches[2]))));
            $html = '<table style="width: 100%; border-collapse: collapse; margin: 15px 0;">';
            $isHeader = true;
            foreach ($rows as $row) {
                $cells = array_map('trim', array_filter(explode('|', $row)));
                if (empty($cells)) continue;
                $tag = $isHeader ? 'th' : 'td';
                $style = $isHeader ? 'background-color: #5B9BD5; color: white; padding: 10px; text-align: left; border: 1px solid #DDD;' 
                                   : 'padding: 10px; border: 1px solid #DDD;';
                $html .= '<tr>';
                foreach ($cells as $cell) {
                    $html .= "<$tag style=\"$style\">$cell</$tag>";
                }
                $html .= '</tr>';
                if ($isHeader) $isHeader = false;
            }
            $html .= '</table>';
            return $html;
        },
        $html
    );
    
    // Convert line breaks and paragraphs
    $html = preg_replace('/\n\n+/', '</p><p style="margin: 10px 0;">', $html);
    $html = '<p style="margin: 10px 0;">' . $html . '</p>';
    
    // Convert unordered lists
    $html = preg_replace_callback(
        '/(?:^|\n)((?:\s*[-*]\s.+\n?)+)/m',
        function($matches) {
            $items = preg_split('/\n\s*[-*]\s/', trim($matches[1]), -1, PREG_SPLIT_NO_EMPTY);
            $list = '<ul style="margin: 10px 0; padding-left: 30px;">';
            foreach ($items as $item) {
                $list .= '<li style="margin: 5px 0;">' . trim($item) . '</li>';
            }
            $list .= '</ul>';
            return $list;
        },
        $html
    );
    
    // Convert horizontal rules
    $html = preg_replace('/^---+$/m', '<hr style="border: none; border-top: 2px solid #DDD; margin: 20px 0;">', $html);
    
    return $html;
}

// Convert markdown to HTML
$html = markdownToHtml($markdown);

// Create PDF with custom styling
try {
    $mpdf = new Mpdf([
        'mode' => 'utf-8',
        'format' => 'A4',
        'margin_left' => 15,
        'margin_right' => 15,
        'margin_top' => 20,
        'margin_bottom' => 15,
    ]);
    
    // Set document metadata
    $mpdf->SetAuthor('TP Assessment System - Development Team');
    $mpdf->SetTitle('TP Assessment System - Technical Documentation');
    $mpdf->SetSubject('System Architecture, Code Structure & Implementation Guide');
    $mpdf->SetCreator('PDF Generator Script');
    $mpdf->SetKeywords('TP Assessment, Yii2, PHP, Education, Documentation');
    
    // Add footer with page numbers
    $mpdf->setFooter('{PAGENO}');
    
    // Create styled HTML content
    $styledHtml = <<<HTML
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        .cover-page {
            text-align: center;
            page-break-after: always;
            padding-top: 100px;
        }
        .cover-page h1 {
            color: #2E75B6;
            font-size: 36px;
            margin-bottom: 20px;
        }
        .cover-page p {
            font-size: 14px;
            margin: 10px 0;
            color: #666;
        }
        h1 { color: #2E75B6; border-bottom: 3px solid #2E75B6; padding-bottom: 10px; margin-top: 30px; }
        h2 { color: #5B9BD5; margin-top: 25px; border-left: 5px solid #5B9BD5; padding-left: 10px; }
        h3 { color: #70AD47; margin-top: 15px; }
        h4 { color: #4F81BD; }
        p { margin: 10px 0; text-align: justify; }
        strong { color: #2E75B6; }
        a { color: #5B9BD5; text-decoration: none; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; }
        table th { background-color: #5B9BD5; color: white; padding: 10px; text-align: left; border: 1px solid #DDD; }
        table td { padding: 10px; border: 1px solid #DDD; }
        code { background-color: #F0F0F0; padding: 2px 6px; border-radius: 3px; font-family: monospace; font-size: 12px; }
        ul { margin: 10px 0; padding-left: 30px; }
        li { margin: 5px 0; }
        hr { border: none; border-top: 2px solid #DDD; margin: 20px 0; }
        .file-path { color: #70AD47; font-weight: bold; }
    </style>
</head>
<body>
    <div class="cover-page">
        <h1>TP Assessment System</h1>
        <h2 style="color: #4F81BD; border: none; padding: 0; margin-top: 10px;">Technical Documentation</h2>
        <p style="margin-top: 40px; font-size: 16px;">Complete System Architecture & Implementation Guide</p>
        <p style="font-size: 14px; color: #999; margin-top: 100px;">
            <strong>Project:</strong> Teaching Practice (TP) E24 Assessment Management System<br>
            <strong>Framework:</strong> Yii 2 Framework (PHP)<br>
            <strong>Database:</strong> PostgreSQL<br>
            <strong>Documentation Date:</strong> April 2026
        </p>
    </div>
    
    <div class="content">
        $html
    </div>
    
    <div style="margin-top: 50px; padding-top: 20px; border-top: 2px solid #DDD; font-size: 12px; color: #999; text-align: center;">
        <p>This documentation was automatically generated from the system codebase.</p>
        <p>For the latest version, please refer to the TECHNICAL_OVERVIEW.md file in the project root.</p>
    </div>
</body>
</html>
HTML;
    
    // Write HTML to PDF
    $mpdf->WriteHTML($styledHtml);
    
    // Create documentation directory if it doesn't exist
    $docDir = __DIR__ . '/documentation';
    if (!is_dir($docDir)) {
        mkdir($docDir, 0755, true);
    }
    
    // Save PDF
    $outputPath = $docDir . '/TP-Assessment-System-Technical-Documentation.pdf';
    $mpdf->Output($outputPath, 'F');
    
    echo "✅ PDF Documentation Successfully Generated!\n";
    echo "📄 File: $outputPath\n";
    echo "📊 Size: " . round(filesize($outputPath) / 1024, 2) . " KB\n";
    echo "\n✨ Your system remains unaffected. The PDF is now available in the /documentation folder.\n";
    
} catch (Exception $e) {
    die("❌ Error generating PDF: " . $e->getMessage() . "\n");
}
