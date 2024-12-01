<?php
use setasign\Fpdi\Fpdi;

require_once(__DIR__ . '/../assets/inc/fpdf/fpdf.php');
require_once(__DIR__ . '/../assets/inc/FPDI/src/autoload.php');

function generateInvoice($fname, $lname, $productName, $totalPrice, $payment_id) {
    // Create a new FPDI object
    $pdf = new Fpdi();
    
    // Add a new page to the PDF
    $pdf->AddPage();
    
    // Set the source file (the existing PDF template)
    $pdf->setSourceFile(__DIR__ . '/../assets/inc/fakturamal.pdf'); // Load the invoice template
    $fs = $pdf->importPage(1); // Import the first page of the template
    $pdf->useTemplate($fs); // Use the imported template
    
    // Add a logo to the invoice
    $pdf->Image(__DIR__ . '/../assets/image/logo.png', 85, 5, 40); 
    // Places the logo at position (85, 5) with a width of 40 units
    
    // Add invoice issuer information
    $pdf->SetFont('Arial', '', 10); // Set font to Arial, regular, size 10
    $pdf->SetXY(13, 20); // Set cursor position to (13, 20)
    $pdf->Cell(0, 10, "Fakturautsteder: Svalberg Motell", 0, 1); 
    // Adds the text "Invoice Issuer: Svalberg Motell"
    
    // Add customer information
    $pdf->SetXY(13, 50); // Set cursor position to (13, 50)
    $pdf->Cell(0, 10, "Kunde: " . $fname . " " . $lname, 0, 1); 
    // Adds the text "Customer: Ola Nordmann"
    
    // Add address information
    $pdf->SetXY(13, 60); // Set cursor position to (13, 60)
    $pdf->Cell(0, 10, "Adresse: Eksempelveien 10, 1234 Kristiansand", 0, 1); 
    // Adds the text "Address: Eksempelveien 10, 1234 Oslo"
    
    // Add product and price details
    $pdf->SetXY(13, 90); // Set cursor position to (13, 90)
    $pdf->Cell(0, 10, "Produkt: " . $productName . " - NOK " . $totalPrice, 0, 1); 
    
    
    // Add the total amount
    $pdf->SetFont('Arial', '', 12); // Change font size to 12 for emphasis
    $pdf->SetXY(100, 185); // Set cursor position to (100, 185)
    $pdf->Cell(0, 10, $totalPrice, 0, 1); 
    
    $tmpDir = __DIR__ . '/../tmp';
    if (!is_dir($tmpDir)) {
        if (!mkdir($tmpDir, 0755, true)) {
            throw new Exception("Failed to create tmp directory: " . $tmpDir);
        }
    }
    
    // Sjekk om mappen er skrivbar
    if (!is_writable($tmpDir)) {
        throw new Exception("Tmp directory is not writable: " . $tmpDir);
    }
    
    // Lag PDF-filen
    $invoiceFileName = 'faktura_' . $payment_id . '_' . time() . '.pdf';
    $filePath = $tmpDir . '/' . $invoiceFileName;
    $pdf->Output("F", $filePath);
    
    return $invoiceFileName;
}
?>