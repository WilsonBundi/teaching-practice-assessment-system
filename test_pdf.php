<?php
require 'vendor/autoload.php';

try {
    $dompdf = new Dompdf\Dompdf();
    $dompdf->loadHtml('<h1>Test PDF</h1>');
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    echo "PDF generation successful\n";
    echo "Output length: " . strlen($dompdf->output()) . " bytes\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
