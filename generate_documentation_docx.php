<?php
/**
 * Generate a .docx document from the TECHNICAL_OVERVIEW.md file.
 * This uses PHP's ZipArchive and does not require external conversion tools.
 */

$markdownFile = __DIR__ . '/TECHNICAL_OVERVIEW.md';
if (!file_exists($markdownFile)) {
    die("Error: TECHNICAL_OVERVIEW.md not found!\n");
}

$markdown = file_get_contents($markdownFile);

function escapeXml($text) {
    return htmlspecialchars($text, ENT_XML1 | ENT_QUOTES, 'UTF-8');
}

function inlineRuns($text, $monospace = false) {
    $segments = preg_split('/(\*\*.*?\*\*|__.*?__|\*.*?\*|_.*?_|\[.*?\]\(.*?\))/s', $text, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    $xml = '';
    foreach ($segments as $segment) {
        if (preg_match('/^\*\*(.*?)\*\*$|^__(.*?)__$/s', $segment, $m)) {
            $content = $m[1] !== '' ? $m[1] : $m[2];
            $xml .= createRun($content, true, false, $monospace);
        } elseif (preg_match('/^\*(.*?)\*$|^_(.*?)_$/s', $segment, $m)) {
            $content = $m[1] !== '' ? $m[1] : $m[2];
            $xml .= createRun($content, false, true, $monospace);
        } elseif (preg_match('/^\[(.*?)\]\((.*?)\)$/s', $segment, $m)) {
            $label = $m[1];
            $url = $m[2];
            $xml .= createRun($label . ' (' . $url . ')', false, false, $monospace);
        } else {
            $xml .= createRun($segment, false, false, $monospace);
        }
    }
    return $xml;
}

function createRun($text, $bold = false, $italic = false, $monospace = false) {
    $parts = preg_split('/(\r\n|\r|\n)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
    $runXml = '';
    foreach ($parts as $part) {
        if ($part === "\r\n" || $part === "\r" || $part === "\n") {
            $runXml .= '<w:r><w:t xml:space="preserve">' . '</w:t><w:br/></w:r>';
            continue;
        }

        $runXml .= '<w:r><w:rPr>';
        if ($bold) {
            $runXml .= '<w:b/>';
        }
        if ($italic) {
            $runXml .= '<w:i/>';
        }
        if ($monospace) {
            $runXml .= '<w:rFonts w:ascii="Consolas" w:hAnsi="Consolas" w:cs="Consolas"/>';
        }
        $runXml .= '</w:rPr><w:t xml:space="preserve">' . escapeXml($part) . '</w:t></w:r>';
    }
    return $runXml;
}

function createParagraph($text, $style = null, $monospace = false, $bullet = false) {
    $pPr = '';
    if ($style) {
        $pPr .= '<w:pPr><w:pStyle w:val="' . $style . '"/></w:pPr>';
    }
    if ($bullet) {
        $pPr .= '<w:pPr><w:rPr/></w:pPr>';
    }
    $content = inlineRuns($text, $monospace);
    return '<w:p>' . $pPr . $content . '</w:p>';
}

function createTable($rows) {
    $xml = '<w:tbl><w:tblPr><w:tblW w:w="0" w:type="auto"/><w:tblBorders>' .
           '<w:top w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '<w:left w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '<w:bottom w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '<w:right w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '<w:insideH w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '<w:insideV w:val="single" w:sz="4" w:space="0" w:color="999999"/>' .
           '</w:tblBorders></w:tblPr>';

    foreach ($rows as $rowIndex => $row) {
        $xml .= '<w:tr>';
        foreach ($row as $cell) {
            $cellText = trim($cell);
            $cellXml = '<w:tc><w:tcPr><w:tcW w:w="0" w:type="auto"/></w:tcPr><w:p>' .
                       createRun($cellText, $rowIndex === 0, false, false) .
                       '</w:p></w:tc>';
            $xml .= $cellXml;
        }
        $xml .= '</w:tr>';
    }
    $xml .= '</w:tbl>';
    return $xml;
}

function parseMarkdown($markdown) {
    $lines = preg_split('/\R/', $markdown);
    $blocks = [];
    $paragraphLines = [];
    $listItems = [];
    $tableLines = null;
    $inCode = false;
    $codeLines = [];

    $flushParagraph = function() use (&$paragraphLines, &$blocks) {
        if (!empty($paragraphLines)) {
            $blocks[] = ['type' => 'paragraph', 'text' => implode(' ', $paragraphLines)];
            $paragraphLines = [];
        }
    };

    $flushList = function() use (&$listItems, &$blocks) {
        if (!empty($listItems)) {
            $blocks[] = ['type' => 'list', 'items' => $listItems];
            $listItems = [];
        }
    };

    $flushTable = function() use (&$tableLines, &$blocks) {
        if ($tableLines !== null) {
            $rows = [];
            foreach ($tableLines as $line) {
                $cells = array_map('trim', explode('|', trim($line, '|')));
                if (!empty($cells)) {
                    $rows[] = $cells;
                }
            }
            if (!empty($rows)) {
                $blocks[] = ['type' => 'table', 'rows' => $rows];
            }
            $tableLines = null;
        }
    };

    foreach ($lines as $index => $line) {
        if ($inCode) {
            if (preg_match('/^```/', $line)) {
                $blocks[] = ['type' => 'code', 'lines' => $codeLines];
                $codeLines = [];
                $inCode = false;
                continue;
            }
            $codeLines[] = $line;
            continue;
        }

        if (preg_match('/^```/', $line)) {
            $flushParagraph();
            $flushList();
            $flushTable();
            $inCode = true;
            continue;
        }

        if ($line === '') {
            $flushParagraph();
            $flushList();
            $flushTable();
            continue;
        }

        if (preg_match('/^(#{1,4})\s+(.*)$/', $line, $matches)) {
            $flushParagraph();
            $flushList();
            $flushTable();
            $level = strlen($matches[1]);
            $blocks[] = ['type' => 'heading', 'level' => $level, 'text' => $matches[2]];
            continue;
        }

        if (preg_match('/^[*\-]\s+(.*)$/', trim($line), $matches)) {
            $flushParagraph();
            $flushTable();
            $listItems[] = $matches[1];
            continue;
        }

        if (preg_match('/^\|.*\|$/', $line)) {
            $nextLine = $lines[$index + 1] ?? '';
            if (preg_match('/^\|[-:\s|]+\|$/', $nextLine)) {
                $flushParagraph();
                $flushList();
                $tableLines = [$line];
                continue;
            }
            if ($tableLines !== null) {
                $tableLines[] = $line;
                continue;
            }
        }

        if ($tableLines !== null && preg_match('/^\|.*\|$/', $line)) {
            $tableLines[] = $line;
            continue;
        }

        if ($tableLines !== null) {
            $flushTable();
        }

        $paragraphLines[] = $line;
    }

    if ($inCode) {
        $blocks[] = ['type' => 'code', 'lines' => $codeLines];
    }
    $flushParagraph();
    $flushList();
    $flushTable();

    return $blocks;
}

function buildDocumentXml($blocks) {
    $xml = '';
    foreach ($blocks as $block) {
        switch ($block['type']) {
            case 'heading':
                $style = 'Heading' . min(4, max(1, $block['level']));
                $xml .= createParagraph($block['text'], $style);
                break;
            case 'paragraph':
                $xml .= createParagraph($block['text']);
                break;
            case 'list':
                foreach ($block['items'] as $item) {
                    $xml .= createParagraph('• ' . $item, null, false, true);
                }
                break;
            case 'code':
                $xml .= '<w:p>' . createRun(implode("\n", $block['lines']), false, false, true) . '</w:p>';
                break;
            case 'table':
                $xml .= createTable($block['rows']);
                break;
        }
    }
    return $xml;
}

function getDocumentXml($bodyXml) {
    return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
           '<w:document xmlns:wpc="http://schemas.microsoft.com/office/word/2010/wordprocessingCanvas"' .
           ' xmlns:mc="http://schemas.openxmlformats.org/markup-compatibility/2006"' .
           ' xmlns:o="urn:schemas-microsoft-com:office:office"' .
           ' xmlns:r="http://schemas.openxmlformats.org/officeDocument/2006/relationships"' .
           ' xmlns:m="http://schemas.openxmlformats.org/officeDocument/2006/math"' .
           ' xmlns:v="urn:schemas-microsoft-com:vml"' .
           ' xmlns:wp="http://schemas.openxmlformats.org/drawingml/2006/wordprocessingDrawing"' .
           ' xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main"' .
           ' xmlns:w10="urn:schemas-microsoft-com:office:word"' .
           ' xmlns:w14="http://schemas.microsoft.com/office/word/2010/wordml"' .
           ' xmlns:wpg="http://schemas.microsoft.com/office/word/2010/wordprocessingGroup"' .
           ' xmlns:wpi="http://schemas.microsoft.com/office/word/2010/wordprocessingInk"' .
           ' xmlns:wne="http://schemas.microsoft.com/office/word/2006/wordml"' .
           ' xmlns:wps="http://schemas.microsoft.com/office/word/2010/wordprocessingShape"' .
           ' mc:Ignorable="w14 wp14">' .
           '<w:body>' .
           $bodyXml .
           '<w:sectPr>' .
           '<w:pgSz w:w="12240" w:h="15840"/>' .
           '<w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440" w:header="720" w:footer="720" w:gutter="0"/>' .
           '</w:sectPr>' .
           '</w:body></w:document>';
}

$blocks = parseMarkdown($markdown);
$bodyXml = buildDocumentXml($blocks);
$documentXml = getDocumentXml($bodyXml);

$contentTypes = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
                '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
                '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
                '<Default Extension="xml" ContentType="application/xml"/>' .
                '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
                '</Types>';

$rels = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
        '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
        '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
        '</Relationships>';

$styles = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
          '<w:styles xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
          '<w:style w:type="paragraph" w:styleId="Heading1"><w:name w:val="heading 1"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:sz w:val="32"/></w:rPr></w:style>' .
          '<w:style w:type="paragraph" w:styleId="Heading2"><w:name w:val="heading 2"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:sz w:val="28"/></w:rPr></w:style>' .
          '<w:style w:type="paragraph" w:styleId="Heading3"><w:name w:val="heading 3"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:sz w:val="24"/></w:rPr></w:style>' .
          '<w:style w:type="paragraph" w:styleId="Heading4"><w:name w:val="heading 4"/><w:basedOn w:val="Normal"/><w:next w:val="Normal"/><w:rPr><w:b/><w:sz w:val="22"/></w:rPr></w:style>' .
          '</w:styles>';

$outputDir = __DIR__ . '/documentation';
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$outputPath = $outputDir . '/TP-Assessment-System-Technical-Documentation.docx';

$zip = new ZipArchive();
if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
    die("Error: Unable to create $outputPath\n");
}

$zip->addFromString('[Content_Types].xml', $contentTypes);
$zip->addFromString('_rels/.rels', $rels);
$zip->addFromString('word/document.xml', $documentXml);
$zip->addFromString('word/styles.xml', $styles);
$zip->close();

echo "DOCX document generated: $outputPath\n";
