# 🚗⚡ BLUE ECO FAST — Marketing Website
**Reimagine the Ride**

A professional PHP marketing website for Blue Eco Fast Ltd, Rwanda's leading EV mobility company.

---

## 📁 File Structure

```
blueEcoFast/
├── index.php               ← Main website (single-page layout)
├── css/
│   └── style.css           ← Custom styles (Bootstrap 5 + custom)
├── js/
│   └── main.js             ← jQuery + AJAX interactions
├── images/
│   └── logo.png            ← Company logo
├── includes/
│   └── config.php          ← Site config & email settings
├── ajax/
│   └── send_enquiry.php    ← AJAX endpoint: validates & emails enquiry
├── logs/
│   └── README.txt          ← Mail error log goes here
└── README.md               ← This file
```

---

## 🚀 Setup Instructions

### 1. Upload to your web server
Upload all files to your PHP-enabled hosting (e.g. cPanel, Apache, Nginx).

### 2. Configure email & site details
Edit `includes/config.php`:
```php
define('COMPANY_EMAIL', 'info@yourrealdomain.rw');  // ← your email
define('SITE_URL',      'https://yourrealdomain.rw'); // ← your domain
```

### 3. Set folder permissions
```bash
chmod 755 logs/
```

### 4. Test the enquiry form
- Open the site in a browser
- Click a car's "Enquire Now" button
- Rate with 4★ or 5★ to unlock the form
- Fill and submit — check your inbox!

---

## 📧 Email Flow

1. Customer rates satisfaction (4★+ unlocks the form)
2. Customer fills form & selects preferred cars
3. On submit → jQuery AJAX POSTs to `ajax/send_enquiry.php`
4. PHP validates all fields (server-side)
5. Sends **email to company** (rich HTML with all details)
6. Sends **auto-reply to customer** (confirmation email)
7. AJAX returns JSON → jQuery shows success/error message

---

## 🛠️ Tech Stack

| Layer      | Technology                     |
|------------|-------------------------------|
| Backend    | PHP 8+ (pure, no framework)   |
| Frontend   | Bootstrap 5.3, jQuery 3.7     |
| AJAX       | jQuery $.ajax → PHP JSON API  |
| Email      | PHP mail() (SMTP-upgradeable) |
| Icons      | Bootstrap Icons 1.11          |
| Fonts      | Google Fonts (Inter)          |

---

## 📧 SMTP Upgrade (Optional)

To use Gmail or another SMTP provider instead of PHP mail():

1. Install [PHPMailer](https://github.com/PHPMailer/PHPMailer) via Composer:
   ```bash
   composer require phpmailer/phpmailer
   ```
2. Uncomment the SMTP constants in `includes/config.php`
3. Replace the `mail()` calls in `ajax/send_enquiry.php` with PHPMailer

---

## 🌿 Features

- ✅ Fully responsive (mobile-first)
- ✅ Sticky navbar with scroll effect
- ✅ Animated hero with stats counter
- ✅ 5-service showcase section
- ✅ Filterable EV fleet grid
- ✅ About / Mission / Vision section
- ✅ 6 customer testimonials
- ✅ Star-rating gated enquiry form
- ✅ Car preference checkboxes
- ✅ AJAX form submission (no page reload)
- ✅ Company email + customer auto-reply
- ✅ Contact section with embedded map
- ✅ Footer with all links
- ✅ Back-to-top button
- ✅ Spam honeypot protection

---

## 📞 Support

Blue Eco Fast Ltd · Kigali, Rwanda  
📧 info@blueEcoFast.rw · 📞 +250 788 000 000
