<?php
/**
 * BLUE ECO FAST - Enquiry Form Handler (AJAX)
 * Validates input, sends emails via PHPMailer SMTP, returns JSON.
 */

header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

require_once __DIR__ . '/../includes/config.php';

// Start session to check if user is logged in
session_start();

// ── Accept POST only ──────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// ── Honeypot spam trap ────────────────────────────────────────────────────────
if (!empty($_POST['website'])) {
    echo json_encode(['success' => false, 'message' => 'Spam detected.']);
    exit;
}

// ── Collect & sanitise inputs ─────────────────────────────────────────────────
$fullName      = clean($_POST['full_name']      ?? '');
$email         = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$phone         = clean($_POST['phone']          ?? '');
$service       = clean($_POST['service']        ?? '');
// Handle cars array from checkboxes
$carsArray     = $_POST['cars'] ?? [];
$preferredCars = implode(', ', array_map('clean', (array)$carsArray));
$budget        = clean($_POST['budget']         ?? '');
$howHear       = clean($_POST['how_hear']       ?? '');
$message       = clean($_POST['message']        ?? '');
$rating        = (int)($_POST['rating']         ?? 0);

// ── Server-side validation ────────────────────────────────────────────────────
$errors = [];

if (strlen($fullName) < 2) {
    $errors[] = 'Please enter your full name (at least 2 characters).';
}
if (!$email) {
    $errors[] = 'Please enter a valid email address.';
}
if (strlen($phone) < 7) {
    $errors[] = 'Please enter a valid phone number.';
}
if (empty($service)) {
    $errors[] = 'Please select a service of interest.';
}
if (empty($preferredCars)) {
    $errors[] = 'Please select at least one preferred car.';
}


if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
    exit;
}

// ── Save to database ──────────────────────────────────────────────────────────
$id = $_SESSION['id'] ?? null;
$cars = implode(', ', array_map('clean', explode(',', $preferredCars))); // Assuming cars are comma-separated

try {
    $pdo = getDB();
    $stmt = $pdo->prepare("INSERT INTO enquiries (id, full_name, email, phone, service, cars, budget, how_hear, message, rating, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
    $stmt->execute([$id, $fullName, $email, $phone, $service, $cars, $budget, $howHear, $message, $rating]);
} catch (PDOException $e) {
    // Log DB error but continue with email sending
    $logEntry = date('Y-m-d H:i:s') . " | DB SAVE FAIL | {$fullName} | {$email} | Error: {$e->getMessage()}\n";
    @file_put_contents(__DIR__ . '/../logs/db_errors.log', $logEntry, FILE_APPEND | LOCK_EX);
}

// ── Build content shared by both emails ───────────────────────────────────────
$ratingStars  = str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
$submittedAt  = date('l, d F Y \a\t H:i T');
$budgetLine   = $budget   ?: 'Not specified';
$howHearLine  = $howHear  ?: 'Not specified';
$messageLine  = $message  ?: '—';

// ════════════════════════════════════════════════════════════════════════════
// EMAIL 1 — Company notification
// ════════════════════════════════════════════════════════════════════════════
$companySubject = "🚗 New Car Enquiry from {$fullName} — " . SITE_NAME;

$companyHTML = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body  { font-family: Arial, sans-serif; background: #f4f7fb; margin: 0; padding: 20px; }
  .wrap { max-width: 620px; margin: 0 auto; background: #fff; border-radius: 14px;
          overflow: hidden; box-shadow: 0 4px 24px rgba(26,86,219,.12); }
  .hdr  { background: linear-gradient(135deg,#0f2340,#1a56db,#2eb84e);
          padding: 32px 28px; text-align: center; }
  .hdr h1 { color:#fff; margin:0; font-size:1.5rem; }
  .hdr p  { color:rgba(255,255,255,.78); margin:6px 0 0; font-size:.88rem; }
  .body { padding: 28px; }
  .lbl  { font-size:.75rem; font-weight:700; text-transform:uppercase;
          letter-spacing:.06em; color:#1a56db; margin-bottom:3px; }
  .val  { font-size:.95rem; color:#111827; margin-bottom:16px; padding:10px 14px;
          background:#f4f7fb; border-radius:8px; border-left:3px solid #2eb84e; }
  .val a { color:#1a56db; text-decoration:none; }
  .stars { font-size:1.4rem; color:#f59e0b; }
  .ftr  { background:#0f2340; padding:16px 28px; text-align:center; }
  .ftr p { color:rgba(255,255,255,.45); font-size:.76rem; margin:0; }
  .ftr a { color:#2eb84e; text-decoration:none; }
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr">
    <h1>🚗 New Car Enquiry Received</h1>
    <p>Blue Eco Fast — Reimagine the Ride</p>
  </div>
  <div class="body">
    <p style="margin-bottom:20px;color:#374151;">
      A customer has submitted a car preference enquiry. Please follow up within 24 hours.
    </p>

    <div class="lbl">Full Name</div>
    <div class="val">{$fullName}</div>

    <div class="lbl">Email Address</div>
    <div class="val"><a href="mailto:{$email}">{$email}</a></div>

    <div class="lbl">Phone Number</div>
    <div class="val"><a href="tel:{$phone}">{$phone}</a></div>

    <div class="lbl">Service of Interest</div>
    <div class="val">{$service}</div>

    <div class="lbl">Preferred Cars / Models</div>
    <div class="val">{$preferredCars}</div>

    <div class="lbl">Budget Range</div>
    <div class="val">{$budgetLine}</div>

    <div class="lbl">How They Heard About Us</div>
    <div class="val">{$howHearLine}</div>

    <div class="lbl">Satisfaction Rating</div>
    <div class="val"><span class="stars">{$ratingStars}</span>&nbsp; {$rating} / 5</div>

    <div class="lbl">Additional Message</div>
    <div class="val">{$messageLine}</div>

    <div class="lbl">Submitted At</div>
    <div class="val">{$submittedAt}</div>
  </div>
  <div class="ftr">
    <p>Submitted via <a href="{$_SERVER['HTTP_HOST']}">Blue Eco Fast website</a>
       &nbsp;·&nbsp; © Blue Eco Fast Ltd, Rwanda</p>
  </div>
</div>
</body>
</html>
HTML;

$companyPlain = "NEW CAR ENQUIRY — " . SITE_NAME . "\n"
    . str_repeat('-', 50) . "\n"
    . "Name:          {$fullName}\n"
    . "Email:         {$email}\n"
    . "Phone:         {$phone}\n"
    . "Service:       {$service}\n"
    . "Preferred Cars:{$preferredCars}\n"
    . "Budget:        {$budgetLine}\n"
    . "How heard:     {$howHearLine}\n"
    . "Rating:        {$rating}/5\n"
    . "Message:       {$messageLine}\n"
    . "Submitted:     {$submittedAt}\n";

// ════════════════════════════════════════════════════════════════════════════
// EMAIL 2 — Customer auto-reply
// ════════════════════════════════════════════════════════════════════════════
$autoReplySubject = "✅ We received your enquiry — " . SITE_NAME;

$autoReplyHTML = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
  body  { font-family:Arial,sans-serif; background:#f4f7fb; padding:20px; }
  .wrap { max-width:580px; margin:0 auto; background:#fff; border-radius:14px; overflow:hidden; }
  .hdr  { background:linear-gradient(135deg,#0f2340,#1a56db,#2eb84e); padding:28px; text-align:center; }
  .hdr h1 { color:#fff; margin:0; font-size:1.4rem; }
  .body { padding:28px; color:#374151; line-height:1.65; }
  .box  { background:#f4f7fb; border-left:3px solid #2eb84e; padding:14px 16px;
          border-radius:8px; margin:18px 0; font-size:.92rem; }
  .box strong { color:#1e3a5f; }
  .btn  { display:inline-block; background:linear-gradient(90deg,#1a56db,#2eb84e);
          color:#fff; padding:13px 30px; border-radius:50px; text-decoration:none;
          font-weight:700; margin-top:18px; font-size:.95rem; }
  .ftr  { background:#0f2340; padding:16px 28px; text-align:center; }
  .ftr p { color:rgba(255,255,255,.45); font-size:.76rem; margin:0; }
</style>
</head>
<body>
<div class="wrap">
  <div class="hdr"><h1>Thank You, {$fullName}! 🎉</h1></div>
  <div class="body">
    <p>
      Thank you for contacting <strong>Blue Eco Fast Ltd</strong>.
      We have successfully received your car enquiry and our team will
      reach out to you <strong>within 24 hours</strong>.
    </p>
    <div class="box">
      <strong>Your Selected Vehicles:</strong><br>{$preferredCars}<br><br>
      <strong>Service Requested:</strong><br>{$service}<br><br>
      <strong>Your Satisfaction Rating:</strong><br>
      <span style="color:#f59e0b;font-size:1.2rem;">{$ratingStars}</span>
      &nbsp;{$rating}/5 — we truly appreciate your feedback!
    </div>
    <p>
      If you have any urgent questions in the meantime, please don't hesitate to reach us:
    </p>
    <p>
      📞 <a href="tel:+250788000000" style="color:#1a56db;">+250 788 000 000</a><br>
      📧 <a href="mailto:info@blueEcoFast.rw" style="color:#1a56db;">info@blueEcoFast.rw</a>
    </p>
    <a class="btn" href="https://blueEcoFast.rw">🚗 Visit Our Website</a>
  </div>
  <div class="ftr">
    <p>© Blue Eco Fast Ltd · Kigali, Rwanda · <em>Reimagine the Ride</em></p>
  </div>
</div>
</body>
</html>
HTML;

$autoReplyPlain = "Hi {$fullName},\n\n"
    . "Thank you for contacting Blue Eco Fast Ltd.\n\n"
    . "We received your enquiry for: {$preferredCars}.\n"
    . "Service: {$service}\n\n"
    . "Our team will contact you within 24 hours.\n\n"
    . "Questions? Call +250 788 000 000 or email info@blueEcoFast.rw\n\n"
    . "Best regards,\nBlue Eco Fast Team\nKigali, Rwanda";

// ════════════════════════════════════════════════════════════════════════════
// SEND BOTH EMAILS via the sendMail() helper in config.php
// ════════════════════════════════════════════════════════════════════════════

// 1. Notify the company (reply-to = customer so staff can reply directly)
$result1 = sendMail(
    toEmail:      COMPANY_EMAIL,
    toName:       COMPANY_NAME,
    subject:      $companySubject,
    htmlBody:     $companyHTML,
    plainBody:    $companyPlain,
    replyToEmail: $email,
    replyToName:  $fullName
);

// 2. Auto-reply to the customer
$result2 = sendMail(
    toEmail:   $email,
    toName:    $fullName,
    subject:   $autoReplySubject,
    htmlBody:  $autoReplyHTML,
    plainBody: $autoReplyPlain
);

// ── JSON response ─────────────────────────────────────────────────────────────
if ($result1['ok']) {
    echo json_encode([
        'success' => true,
        'message' => "Your enquiry has been sent successfully! "
                   . "Please check your inbox ({$email}) for a confirmation. "
                   . "Our team will contact you within 24 hours. 🚗⚡"
    ]);
} else {
    // result1 failed — log the error detail and inform the user
    $logEntry = date('Y-m-d H:i:s')
        . " | SEND FAIL | {$fullName} | {$email}"
        . " | Cars: {$preferredCars}"
        . " | Error: {$result1['error']}\n";
    @file_put_contents(__DIR__ . '/../logs/mail_errors.log', $logEntry, FILE_APPEND | LOCK_EX);

    echo json_encode([
        'success' => false,
        'message' => "There was a problem sending your enquiry. "
                   . "Please call us directly at " . COMPANY_PHONE
                   . " or email " . COMPANY_EMAIL . "."
    ]);
}
