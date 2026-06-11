<?php
/**
 * PHP Cheat Sheet - 40: Advanced Emails with Attachments (Multipart/MIME)
 * 
 * Topics covered:
 * - Understanding the Multipart/MIME email standard
 * - Setting up boundaries to separate message parts
 * - Generating Base64 encoded file attachment blocks
 * - Constructing the email body payload
 * - Dispatching via the native mail() function
 */

echo "=== 1. THE MULTIPART/MIME STRUCTURE ===\n";
echo "To send emails with attachments, the Content-Type header must be set to 'multipart/mixed'.\n";
echo "A random boundary string separates the headers, plain text body, HTML body, and file attachments:\n\n";

function sendEmailWithAttachment(
    string $to,
    string $subject,
    string $messageText,
    string $messageHtml,
    string $attachmentFilePath,
    string $fromEmail
): string {
    $fileName = basename($attachmentFilePath);
    
    // Read and encode attachment in base64 chunked blocks
    if (!file_exists($attachmentFilePath)) {
        // Mock fallback block for simulation
        $attachmentData = base64_encode("Simulated PDF Document Binary Data Content");
    } else {
        $fileContent = file_get_contents($attachmentFilePath);
        $attachmentData = base64_encode($fileContent);
    }
    // Split base64 into standard 76-character lines (RFC 2045 requirement)
    $attachmentChunked = chunk_split($attachmentData);
    
    // Generate a unique boundary string
    $boundary = "PHP-mixed-" . md5(time() . uniqid());
    
    // 1. Setup global email headers
    $headers = [
        "From: $fromEmail",
        "Reply-To: $fromEmail",
        "MIME-Version: 1.0",
        "Content-Type: multipart/mixed; boundary=\"$boundary\""
    ];
    
    // 2. Build body payload
    $body = "--$boundary\r\n";
    
    // Plain Text Part
    $body .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "$messageText\r\n\r\n";
    
    // HTML Part
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: text/html; charset=\"UTF-8\"\r\n";
    $body .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
    $body .= "$messageHtml\r\n\r\n";
    
    // Attachment Part
    $body .= "--$boundary\r\n";
    $body .= "Content-Type: application/octet-stream; name=\"$fileName\"\r\n";
    $body .= "Content-Description: $fileName\r\n";
    $body .= "Content-Disposition: attachment; filename=\"$fileName\"\r\n";
    $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
    $body .= "$attachmentChunked\r\n\r\n";
    
    // Closing Boundary
    $body .= "--$boundary--";
    
    // In simulation mode, we return the constructed raw email payload
    return "Headers:\n" . implode("\n", $headers) . "\n\nBody Payload:\n$body";
}

$to = "customer@client.com";
$subject = "Your Invoice #4092";
$plainText = "Hello, please find your invoice attached to this email.";
$htmlContent = "
<html>
  <body>
    <h2 style='color:#8f5fe8;'>Your Invoice</h2>
    <p>Thank you for your business. Please find your invoice <b>attached</b> below.</p>
  </body>
</html>";

$dummyPdfPath = sys_get_temp_dir() . '/invoice_4092.pdf';

echo "Constructing Multipart MIME email payload...\n";
$emailPayload = sendEmailWithAttachment(
    $to,
    $subject,
    $plainText,
    $htmlContent,
    $dummyPdfPath,
    "billing@mycompany.com"
);

echo "--- CONSTRUCTED MIME MESSAGE START ---\n";
echo $emailPayload . "\n";
echo "--- CONSTRUCTED MIME MESSAGE END ---\n\n";

echo "To send this payload natively, you execute:\n";
echo "mail(\$to, \$subject, '', \$headersJoinString, \$bodyPayload);\n";
?>
