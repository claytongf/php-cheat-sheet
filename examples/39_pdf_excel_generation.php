<?php
/**
 * PHP Cheat Sheet - 39: PDF and Excel Spreadsheet Generation
 * 
 * Topics covered:
 * - PDF Generation: FPDF (cell layouts) & Dompdf (HTML to PDF converter)
 * - Spreadsheet Generation: PhpSpreadsheet library patterns
 * - Native CSV exports (standard library)
 * - Native XML Spreadsheet generation (for styling & multiple sheets without composer libraries)
 */

echo "=== 1. PDF GENERATION PATTERNS ===\n";
echo "PHP has no native PDF writer. We use Composer packages. The two most common are:\n";
echo "1. FPDF / TCPDF: Fast, lightweight, absolute-coordinates based canvas.\n";
echo "2. Dompdf: CSS/HTML layout engine converter (simplest for templates).\n\n";

// 1. FPDF implementation code pattern
$fpdfPattern = '
use FPDF;

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont("Arial", "B", 16);

// Cell(width, height, text, border, ln, alignment)
$pdf->Cell(40, 10, "Invoice Report", 0, 1, "L");
$pdf->SetFont("Arial", "", 12);
$pdf->Cell(40, 10, "Date: " . date("Y-m-d"), 0, 1, "L");

// Output to file or standard HTTP output stream
$pdf->Output("F", "report.pdf"); // F = File, I = Inline browser view, D = Download
';

// 2. Dompdf implementation code pattern
$dompdfPattern = '
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$html = "
<html>
  <body>
    <h1 style=\'color: #8f5fe8;\'>Invoice #1029</h1>
    <table border=\'1\' style=\'width: 100%; border-collapse: collapse;\'>
      <tr><td>Item</td><td>Price</td></tr>
      <tr><td>PHP Training Course</td><td>$299.00</td></tr>
    </table>
  </body>
</html>
";

$dompdf->loadHtml($html);
$dompdf->setPaper("A4", "portrait");
$dompdf->render();

// Download file attachment
$dompdf->stream("invoice.pdf", ["Attachment" => 1]);
';

echo "FPDF Code Pattern:\n" . trim($fpdfPattern) . "\n\n";
echo "Dompdf HTML to PDF Code Pattern:\n" . trim($dompdfPattern) . "\n\n";


echo "=== 2. SPREADSHEET GENERATION (PhpSpreadsheet) ===\n";
echo "PhpSpreadsheet is the standard library to read/write Excel files (.xlsx):\n\n";

$spreadsheetPattern = '
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$spreadsheet = new Spreadsheet();
$activeSheet = $spreadsheet->getActiveSheet();

// Set cell values
$activeSheet->setCellValue("A1", "Product Name");
$activeSheet->setCellValue("B1", "Stock Quantity");
$activeSheet->setCellValue("A2", "PHP Masterclass Bundle");
$activeSheet->setCellValue("B2", 450);

// Style columns
$activeSheet->getColumnDimension("A")->setAutoSize(true);
$activeSheet->getStyle("A1:B1")->getFont()->setBold(true);

// Write file to XLSX format
$writer = new Xlsx($spreadsheet);
$writer->save("inventory_report.xlsx");
';

echo "PhpSpreadsheet Code Pattern:\n" . trim($spreadsheetPattern) . "\n\n";


echo "=== 3. NATIVE SPREADSHEET EXPORT: RAW XML EXCEL ===\n";
echo "You can generate structured worksheets with styling and columns using raw XML sheets without any composer packages:\n\n";

function generateXmlExcel(array $headers, array $rows): string {
    $xml = '<?xml version="1.0"?>' . "\n";
    $xml .= '<?mso-application progid="Excel.Sheet"?>' . "\n";
    $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet" ' .
            'xmlns:o="urn:schemas-microsoft-com:office:office" ' .
            'xmlns:x="urn:schemas-microsoft-com:office:excel" ' .
            'xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">' . "\n";
    
    // Style definition for bold headers
    $xml .= '  <Styles>' . "\n";
    $xml .= '    <Style ss:ID="HeaderStyle">' . "\n";
    $xml .= '      <Font ss:Bold="1" ss:Color="#FFFFFF"/>' . "\n";
    $xml .= '      <Interior ss:Color="#8F5FE8" ss:Pattern="Solid"/>' . "\n";
    $xml .= '    </Style>' . "\n";
    $xml .= '  </Styles>' . "\n";
    
    $xml .= '  <Worksheet ss:Name="Sheet 1">' . "\n";
    $xml .= '    <Table>' . "\n";
    
    // Write Headers
    $xml .= '      <Row ss:Height="22">' . "\n";
    foreach ($headers as $header) {
        $xml .= '        <Cell ss:StyleID="HeaderStyle"><Data ss:Type="String">' . htmlspecialchars($header) . '</Data></Cell>' . "\n";
    }
    $xml .= '      </Row>' . "\n";
    
    // Write Rows
    foreach ($rows as $row) {
        $xml .= '      <Row>' . "\n";
        foreach ($row as $val) {
            $type = is_numeric($val) ? 'Number' : 'String';
            $xml .= '        <Cell><Data ss:Type="' . $type . '">' . htmlspecialchars($val) . '</Data></Cell>' . "\n";
        }
        $xml .= '      </Row>' . "\n";
    }
    
    $xml .= '    </Table>' . "\n";
    $xml .= '  </Worksheet>' . "\n";
    $xml .= '</Workbook>' . "\n";
    
    return $xml;
}

$headers = ['Item Description', 'Price (USD)', 'License'];
$data = [
    ['PHP Cheat Sheet Premium', 19.99, 'Single User'],
    ['Vim Command Card', 9.50, 'Single User'],
    ['Enterprise License Upgrade', 499.00, 'Corporate']
];

$excelXmlContent = generateXmlExcel($headers, $data);
echo "Generated Raw XML Spreadsheet (first 250 characters):\n";
echo substr($excelXmlContent, 0, 250) . "...\n\n";

// Serving XML to trigger immediate Excel download
$httpSampleHeaders = '
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"billing_report.xls\"");
echo $xmlContent;
';
echo "Serving XML Download headers:\n" . trim($httpSampleHeaders) . "\n";
?>
