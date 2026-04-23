<?php
/**
 * BLUE ECO FAST - Configuration & Mail Helper
 * PHPMailer SMTP edition
 */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ── PHPMailer manual includes (files live in includes/PHPMailer/) ─────────────
require_once __DIR__ . '/PHPMailer/Exception.php';
require_once __DIR__ . '/PHPMailer/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/SMTP.php';
//049343/ 34974
// ── Company details ───────────────────────────────────────────────────────────
define('COMPANY_EMAIL',    'kimuludoviko@gmail.com');
define('COMPANY_NAME',     'Blue Eco Fast Ltd');
define('COMPANY_PHONE',    '+250 788 449 994');
define('COMPANY_ADDR',     'Kigali, Rwanda');

// ── Site ──────────────────────────────────────────────────────────────────────
define('SITE_NAME',        'Blue Eco Fast');
define('SITE_TAGLINE',     'Reimagine the Ride');
define('SITE_URL',         'https://blueEcoFast.rw');

// ── SMTP credentials ──────────────────────────────────────────────────────────
define('SMTP_HOST',        'mail.rca.ac.rw');
define('SMTP_PORT',        587);
define('SMTP_USER',        'louismukama@rca.ac.rw');
define('SMTP_PASS',        'Tryingisbetter2025');
define('SMTP_FROM',        'louismukama@rca.ac.rw');
define('SMTP_FROM_NAME',   COMPANY_NAME);

// ── Environment ───────────────────────────────────────────────────────────────
error_reporting(E_ALL);
ini_set('display_errors', 1);
date_default_timezone_set('Africa/Kigali');

// ── Helpers ───────────────────────────────────────────────────────────────────

/**
 * Sanitize user input.
 */
function clean(string $val): string {
    return htmlspecialchars(strip_tags(trim($val)), ENT_QUOTES, 'UTF-8');
}

/**
 * Send an email via PHPMailer / SMTP.
 *
 * @param  string      $toEmail      Recipient email address
 * @param  string      $toName       Recipient display name
 * @param  string      $subject      Email subject line
 * @param  string      $htmlBody     Full HTML body
 * @param  string      $plainBody    Plain-text fallback body
 * @param  string|null $replyToEmail Optional Reply-To address
 * @param  string|null $replyToName  Optional Reply-To name
 * @return array{ok: bool, error: string}
 */
function sendMail(
    string  $toEmail,
    string  $toName,
    string  $subject,
    string  $htmlBody,
    string  $plainBody,
    ?string $replyToEmail = null,
    ?string $replyToName  = null
): array {
    $mail = new PHPMailer(true); // true = throw exceptions on error

    try {
        // ── SMTP server settings ─────────────────────────────────────────────
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->Port       = SMTP_PORT;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // port 587 uses STARTTLS
        $mail->CharSet    = 'UTF-8';

        // Uncomment during development to debug SMTP handshake:
        // $mail->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;

        // ── Sender ───────────────────────────────────────────────────────────
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);

        // ── Reply-To (optional) ──────────────────────────────────────────────
        if (!empty($replyToEmail)) {
            $mail->addReplyTo($replyToEmail, $replyToName ?? $replyToEmail);
        }

        // ── Recipient ────────────────────────────────────────────────────────
        $mail->addAddress($toEmail, $toName);

        // ── Content ──────────────────────────────────────────────────────────
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = $plainBody;

        $mail->send();

        return ['ok' => true, 'error' => ''];

    } catch (Exception $e) {
        // Log the SMTP error details
        $logEntry = date('Y-m-d H:i:s') . " | SMTP ERROR | To: {$toEmail} | {$mail->ErrorInfo}\n";
        @file_put_contents(__DIR__ . '/../logs/mail_errors.log', $logEntry, FILE_APPEND | LOCK_EX);

        return ['ok' => false, 'error' => $mail->ErrorInfo];
    }
}
