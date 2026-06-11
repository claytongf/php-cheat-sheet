<?php
/**
 * PHP Cheat Sheet - 29: Sending Emails in PHP
 * 
 * Topics covered:
 * - The standard mail() function
 * - Configuring headers (HTML emails, CC/BCC, custom senders)
 * - PHP.ini configuration requirements (SMTP setups)
 * - Mail server limitations (Spam, SPF, DKIM)
 * - Modern SMTP Mailing: PHPMailer and Symfony Mailer
 */

echo "=== 1. THE STANDARD mail() FUNCTION ===\n";
echo "The native mail() function sends email directly through local sendmail / postfix:\n\n";

/*
// Basic mail signature:
// bool mail(string $to, string $subject, string $message, array|string $additional_headers = [], string $additional_params = "")
*/

// Simulating email dispatch
$to = "recipient@example.com";
$subject = "Welcome to our platform!";
$message = "Hello, thank you for joining us.";

// Set headers as an array (PHP 7.2+) or string
$headers = [
    'From' => 'no-reply@myplatform.com',
    'Reply-To' => 'support@myplatform.com',
    'X-Mailer' => 'PHP/' . phpversion()
];

echo "Recipient: $to\n";
echo "Subject: $subject\n";
echo "Headers:\n";
foreach ($headers as $key => $val) {
    echo "- $key: $val\n";
}
echo "\nSending via: mail(\$to, \$subject, \$message, \$headers);\n";


echo "\n=== 2. SENDING HTML EMAILS ===\n";
echo "To send HTML emails, you must configure the Content-Type header:\n\n";

$htmlHeaders = [
    'From' => 'newsletter@myplatform.com',
    'MIME-Version' => '1.0',
    'Content-Type' => 'text/html; charset=UTF-8',
    'Cc' => 'manager@myplatform.com'
];

$htmlMessage = "
<html>
<head>
  <title>HTML Email</title>
</head>
<body>
  <h1 style='color:#8f5fe8;'>Welcome!</h1>
  <p>Thank you for subscribing. Click <a href='#'>here</a> to verify.</p>
</body>
</html>
";

echo "HTML Message Payload:\n" . trim($htmlMessage) . "\n\n";
echo "HTML Headers:\n";
foreach ($htmlHeaders as $key => $val) {
    echo "- $key: $val\n";
}


echo "\n=== 3. PHP.INI SMTP CONFIGURATION ===\n";
echo "Standard mail() requires configurations inside php.ini to work:\n";
echo "- On Linux: 'sendmail_path' must point to the local mail binary (e.g. /usr/sbin/sendmail -t -i).\n";
echo "- On Windows: 'SMTP' and 'smtp_port' options must point to an active SMTP server (e.g. mail.isp.com).\n\n";


echo "=== 4. WHY NATIVE mail() OFTEN FAILS ===\n";
echo "1. No SMTP Auth: Native mail() doesn't support usernames/passwords to authenticate with SMTP relays.\n";
echo "2. Spam Flagging: Without SPF, DKIM, and DMARC settings, emails sent from raw server IPs are usually blocked or marked as spam.\n";
echo "3. Delivery Tracking: Lacks features like attachment encodings or delivery confirmations.\n\n";


echo "=== 5. THE MODERN SOLUTION: COMPOSER LIBRARIES ===\n";
echo "Modern PHP applications use composer packages like PHPMailer or Symfony Mailer for secure SMTP authentication (e.g., using Gmail or Mailgun):\n\n";

$phpMailerCode = '
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // Server settings
    $mail->isSMTP();
    $mail->Host       = "smtp.mailtrap.io";
    $mail->SMTPAuth   = true;
    $mail->Username   = "my_smtp_username";
    $mail->Password   = "my_smtp_password";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom("from@example.com", "Mailer");
    $mail->addAddress("joe@example.net", "Joe User");

    // Content
    $mail->isHTML(true);
    $mail->Subject = "Here is the subject";
    $mail->Body    = "This is the HTML message body <b>in bold!</b>";

    $mail->send();
    echo "Message has been sent";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
';

echo "PHPMailer Implementation Pattern:\n" . $phpMailerCode . "\n";
?>
