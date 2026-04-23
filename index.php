<?php
session_start();
require_once __DIR__ . '/includes/config.php';
$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="Blue Eco Fast Ltd — Rwanda's leading Electric Vehicle sales, rental, eco taxi, fleet and corporate transport solutions. Reimagine the Ride.">
  <meta name="keywords" content="electric vehicles Rwanda, EV sales Kigali, eco taxi Rwanda, car rental Kigali, fleet management Rwanda, Blue Eco Fast">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:description" content="Sustainable, affordable and innovative transport solutions in Rwanda.">
  <meta property="og:type" content="website">
  <title><?= htmlspecialchars($pageTitle) ?></title>

  <!-- Bootstrap 5 -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <!-- Google Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
  <!-- Custom CSS -->
  <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- ════════════════════════════════════════════
     NAVBAR
════════════════════════════════════════════ -->
<nav class="navbar navbar-bef navbar-expand-lg">
  <div class="container">
    <a class="navbar-brand" href="#home">
      <img src="images/logo.png" alt="Blue Eco Fast Logo">
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMain">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navMain">
      <ul class="navbar-nav mx-auto gap-1">
        <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#services">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#fleet">Fleet</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About Us</a></li>
        <li class="nav-item"><a class="nav-link" href="#testimonials">Reviews</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
      </ul>
      <?php if (isset($_SESSION['id'])): ?>
        <div class="navbar-nav">
          <span class="navbar-text me-2">Welcome, <?= htmlspecialchars($_SESSION['email']) ?>!</span>
          <a class="nav-link" href="admin/dashboard.php">Admin</a>
          <a class="nav-link" href="logout.php">Logout</a>
        </div>
      <?php else: ?>
        <div class="navbar-nav">
          <a class="nav-link" href="login.php">Login</a>
        </div>
      <?php endif; ?>
    </div>
  </div>
</nav>

<!-- ════════════════════════════════════════════
     HERO SECTION
════════════════════════════════════════════ -->
<section class="hero-section" id="home">
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="hero-badge fade-in-up">
          <i class="bi bi-lightning-charge-fill"></i> Rwanda's #1 EV Mobility Company
        </div>
        <h1 class="fade-in-up delay-1">
          Reimagine<br><span>the Ride</span><br>Go Electric ⚡
        </h1>
        <p class="lead fade-in-up delay-2">
          Blue Eco Fast delivers sustainable, affordable, and innovative electric vehicle
          solutions across Rwanda — from sales and rentals to eco taxis and corporate fleets.
        </p>
        <div class="d-flex flex-wrap gap-3 fade-in-up delay-2">
          <a href="#fleet" class="btn-hero-primary">
            <i class="bi bi-ev-front-fill"></i> Explore Our Fleet
          </a>
          <a href="#enquiry" class="btn-hero-outline">
            <i class="bi bi-envelope-fill"></i> Get a Quote
          </a>
        </div>
        <div class="hero-stats fade-in-up delay-3">
          <div class="hero-stat-item">
            <div class="stat-num" data-count="200" data-suffix="+">0+</div>
            <div class="stat-label">EVs Delivered</div>
          </div>
          <div class="hero-stat-item">
            <div class="stat-num" data-count="98" data-suffix="%">0%</div>
            <div class="stat-label">Client Satisfaction</div>
          </div>
          <div class="hero-stat-item">
            <div class="stat-num" data-count="5" data-suffix="">0</div>
            <div class="stat-label">Services Offered</div>
          </div>
          <div class="hero-stat-item">
            <div class="stat-num" data-count="3" data-suffix=" yrs">0</div>
            <div class="stat-label">In Business</div>
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="hero-car-visual fade-in-up delay-2">
          <div class="eco-badge-float">⚡ 100% Electric</div>
          <div class="hero-car-card">
            <div class="car-placeholder"><img src="images/home image 1.jpeg" alt="Car" width="100%" height="100%"></div>
            <div class="hero-features mt-3">
              <span class="hero-feat-tag">🌿 Zero Emissions</span>
              <span class="hero-feat-tag">💰 Affordable</span>
              <span class="hero-feat-tag">🔋 Long Range</span>
            </div>
            <div class="text-center mt-3">
              <p class="text-white-50 mb-1" style="font-size:.8rem;">Starting From</p>
              <p class="fw-800 text-white mb-0" style="font-size:1.5rem;font-weight:800;">
                USD 5,000 <small style="font-size:.75rem;opacity:.65;">/ vehicle</small>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     WHY CHOOSE US
════════════════════════════════════════════ -->
<section class="why-section" id="why">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-label">Why Blue Eco Fast</div>
      <h2 class="section-title">Driving Rwanda's <span>Green Future</span></h2>
      <div class="section-divider mx-auto"></div>
      <p class="text-muted mx-auto" style="max-width:540px;">We combine sustainability, affordability, and technology to deliver reliable transport while reducing carbon emissions across East Africa.</p>
    </div>
    <div class="row g-4">
      <?php
      $whyCards = [
        ['🌿', 'Eco-Friendly', 'All our vehicles are electric or hybrid, significantly reducing CO₂ emissions and protecting Rwanda\'s environment.'],
        ['💰', 'Affordable Plans', 'Flexible pricing, financing options, and corporate packages tailored to every budget — individual or enterprise.'],
        ['🔋', 'Long-Range EVs', 'Our vehicles offer excellent range per charge, ideal for Kigali\'s daily commute and inter-city travel.'],
        ['🛠️', 'Full Maintenance', 'End-to-end fleet management with servicing, insurance coordination, and driver training included.'],
        ['📱', 'Smart Technology', 'GPS tracking, telematics, and real-time reporting keep your fleet efficient and your business informed.'],
        ['🏆', 'Proven Track Record', '200+ vehicles delivered with 98% client satisfaction — trusted by corporates, SMEs and government bodies.'],
      ];
      foreach ($whyCards as $c): ?>
      <div class="col-md-6 col-lg-4">
        <div class="why-card fade-in-up">
          <div class="why-icon"><?= $c[0] ?></div>
          <h5><?= $c[1] ?></h5>
          <p><?= $c[2] ?></p>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     SERVICES
════════════════════════════════════════════ -->
<section class="services-section" id="services">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-label">What We Offer</div>
      <h2 class="section-title">Our <span class="green">Services</span></h2>
      <div class="section-divider mx-auto"></div>
      <p class="text-muted mx-auto" style="max-width:500px;">Five dedicated services designed to transform how Rwanda moves — sustainably, efficiently, and affordably.</p>
    </div>
    <div class="row g-4">
      <?php
      $services = [
        ['⚡', 'Electric Vehicle Sales', 'EV Sales', 'Purchase brand-new, certified electric vehicles with full warranty, test drives, and flexible financing options tailored for the Rwandan market.', 'Browse EVs'],
        ['🚗', 'Vehicle Rental Services', 'Rental', 'Daily, weekly, and monthly EV rentals for individuals, tourists, and businesses. Modern fleet with comprehensive insurance coverage.', 'Rent Now'],
        ['🚕', 'Eco Taxi Services', 'EV Taxi', 'Book clean, comfortable, electric taxis across Kigali. Our eco taxi app links you to professional drivers in zero-emission vehicles.', 'Book a Ride'],
        ['📊', 'Fleet Management', 'Corporate', 'End-to-end fleet solutions: procurement, GPS tracking, maintenance scheduling, driver management, and monthly performance reporting.', 'Learn More'],
        ['🏢', 'Corporate Transport', 'Corporate', 'Bespoke corporate mobility packages — airport transfers, staff commuting, executive transport, and event shuttle services.', 'Get Quote'],
      ];
      foreach ($services as $s): ?>
      <div class="col-md-6 col-lg-4">
        <div class="service-card fade-in-up">
          <div class="service-img-top">
            <span style="font-size:4rem;"><?= $s[0] ?></span>
            <span class="service-tag"><?= $s[2] ?></span>
          </div>
          <div class="service-body">
            <h5><?= $s[1] ?></h5>
            <p><?= $s[3] ?></p>
            <a href="#enquiry" class="btn-service"><?= $s[4] ?> <i class="bi bi-arrow-right"></i></a>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     FLEET / CARS
════════════════════════════════════════════ -->
<section class="fleet-section" id="fleet">
  <div class="container">
    <div class="text-center mb-4">
      <div class="section-label">Our Vehicles</div>
      <h2 class="section-title">Explore the <span>Fleet</span></h2>
      <div class="section-divider mx-auto"></div>
    </div>

    <!-- Filter Buttons -->
    <?php
    try {
        $pdo = getDB();
        $categories = $pdo->query("SELECT DISTINCT category FROM cars WHERE category IS NOT NULL AND category != '' ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);
        $cars = $pdo->query("SELECT * FROM cars ORDER BY name")->fetchAll();
    } catch (PDOException $e) {
        $categories = ['Sedan', 'SUV', 'Van / Minibus', 'Eco Taxi'];
        $cars = []; // Fallback to empty if DB error
    }
    ?>
    <div class="car-filter-btns justify-content-center">
      <button class="filter-btn active" data-filter="all">All Vehicles</button>
      <?php foreach ($categories as $category): ?>
        <button class="filter-btn" data-filter="<?= htmlspecialchars($category) ?>"><?= htmlspecialchars($category) ?></button>
      <?php endforeach; ?>
    </div>

    <div class="row g-4">
      <?php
      foreach ($cars as $c): ?>
      <div class="col-sm-6 col-lg-3">
        <div class="car-card" data-cat="<?= htmlspecialchars($c['category']) ?>">
          <div class="car-img">
            <img src="<?= htmlspecialchars($c['image']) ?>" alt="Cart" width="100%" height="100%">
            <span class="car-ev-tag"><?= htmlspecialchars($c['fuel_type']) ?></span>
          </div>
          <div class="car-body">
            <h6><?= htmlspecialchars($c['name']) ?></h6>
            <div class="car-meta">
              <span><i class="bi bi-car-front"></i> <?= htmlspecialchars($c['type']) ?></span>
              <span><i class="bi bi-battery-charging"></i> <?= htmlspecialchars($c['range_km']) ?></span>
              <span><i class="bi bi-people"></i> <?= htmlspecialchars($c['seats']) ?> seats</span>
            </div>
            <div class="d-flex align-items-center justify-content-between mt-1">
              <div class="car-price"><?= htmlspecialchars($c['price']) ?><br><small>Starting price</small></div>
            </div>
            <button class="btn-car-enquire" data-car="<?= htmlspecialchars($c['name']) ?>">
              <i class="bi bi-envelope-fill"></i> Enquire Now
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     ABOUT / MISSION / VISION
════════════════════════════════════════════ -->
<section class="about-section" id="about">
  <div class="container">
    <div class="row g-5 align-items-center">
      <div class="col-lg-5">
        <div class="about-visual">
          <div style="font-size:5rem;margin-bottom:1rem;">⚡🌿</div>
          <h3 style="font-weight:800;margin-bottom:1.5rem;">Reimagine the Ride</h3>
          <div class="mission-card">
            <h6>🎯 Our Mission</h6>
            <p>To transform urban mobility in Rwanda by providing sustainable, affordable, and innovative transport solutions that reduce environmental impact and improve customer experience.</p>
          </div>
          <div class="mission-card">
            <h6>🔭 Our Vision</h6>
            <p>To become a leading green mobility company in East Africa, driving the transition towards electric and smart transportation systems.</p>
          </div>
          <div class="mt-3 text-center">
            <span style="color:rgba(255,255,255,0.55);font-size:.82rem;">Kigali, Rwanda · East Africa</span>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <div class="section-label">About Blue Eco Fast</div>
        <h2 class="section-title mb-2">Forward-Thinking<br><span>Mobility for Rwanda</span></h2>
        <div class="section-divider"></div>
        <p class="text-muted mb-4">Blue Eco Fast Ltd is a forward-thinking mobility company specialising in vehicle sales, rental services, and eco-friendly taxi solutions with a strong focus on Electric Vehicles (EVs) in Rwanda. We are committed to building a greener, smarter East Africa.</p>

        <?php
        $points = [
          ['🌍', 'Rwanda-First Approach', 'Designed for Kigali\'s roads, climate, and community — every solution is locally relevant.'],
          ['🔋', 'Full EV Ecosystem', 'From purchase to charging support, maintenance, and resale — we cover the entire EV ownership journey.'],
          ['🤝', 'Trusted Partnerships', 'Aligned with top EV manufacturers and local financial institutions for seamless transactions.'],
          ['📈', 'Growing Network', 'Expanding service centres and charging infrastructure across Rwanda and the wider East African region.'],
        ];
        foreach ($points as $p): ?>
        <div class="about-point">
          <div class="about-point-icon"><?= $p[0] ?></div>
          <div>
            <h6><?= $p[1] ?></h6>
            <p><?= $p[2] ?></p>
          </div>
        </div>
        <?php endforeach; ?>

        <a href="#enquiry" class="btn-hero-primary d-inline-flex mt-2">
          <i class="bi bi-arrow-right-circle-fill"></i> Work With Us
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     TESTIMONIALS
════════════════════════════════════════════ -->
<section class="testi-section" id="testimonials">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-label" style="background:rgba(46,184,78,0.15);border-color:rgba(46,184,78,0.3);color:#7effa0;">Client Reviews</div>
      <h2 class="section-title text-white">What Our <span>Customers Say</span></h2>
      <div class="section-divider mx-auto"></div>
    </div>
    <div class="row g-4">
      <?php
$testimonials = [
  [
    'text' => 'We purchased two BYD EVs through Blue Eco Fast and the process was seamless. Excellent after-sales support and professional team.',
    'name' => 'Jean-Paul M.',
    'role' => 'CEO, Kigali Tech Hub',
    'avatar' => 'J',
    'rating' => 5
  ],
  [
    'text' => 'The Eco Taxi service is a game-changer for Kigali. Clean, quiet, and always on time. I use it daily for my commute.',
    'name' => 'Amina R.',
    'role' => 'Marketing Manager',
    'avatar' => 'A',
    'rating' => 5
  ],
  [
    'text' => 'Blue Eco Fast managed our corporate fleet of 12 EVs. The GPS tracking and monthly reports are incredibly useful for our business.',
    'name' => 'David K.',
    'role' => 'Operations Director, Telecom Rwanda',
    'avatar' => 'D',
    'rating' => 5
  ],
  [
    'text' => 'Rented a car for a week-long project and the experience was flawless. The team was responsive and the vehicle was immaculate.',
    'name' => 'Sophie N.',
    'role' => 'NGO Project Lead',
    'avatar' => 'S',
    'rating' => 4
  ],
  [
    'text' => 'As a hotel, we use Blue Eco Fast for airport shuttle services. Guests love the quiet, eco-friendly rides. Highly recommended!',
    'name' => 'Robert H.',
    'role' => 'Hotel Manager, Kigali',
    'avatar' => 'R',
    'rating' => 5
  ],
  [
    'text' => 'Best EV dealership in Rwanda. No pressure sales, honest pricing, and excellent test drive experience. Love my new BYD Seal!',
    'name' => 'Grace U.',
    'role' => 'Private Buyer',
    'avatar' => 'G',
    'rating' => 5
  ],
];

// Helper function for stars
function renderStars(int $rating): string {
  $rating = max(0, min(5, $rating)); // clamp between 0–5
  return str_repeat('★', $rating) . str_repeat('☆', 5 - $rating);
}

foreach ($testimonials as $t):
  $text   = htmlspecialchars($t['text']);
  $name   = htmlspecialchars($t['name']);
  $role   = htmlspecialchars($t['role']);
  $avatar = strtoupper(htmlspecialchars($t['avatar']));
  $stars  = renderStars($t['rating']);
?>
<div class="col-md-6 col-lg-4">
  <div class="testi-card fade-in-up">
    <div class="testi-stars"><?= $stars ?></div>
    <p>"<?= $text ?>"</p>
    <div class="testi-author">
      <div class="testi-avatar"><?= $avatar ?></div>
      <div>
        <h6><?= $name ?></h6>
        <small><?= $role ?></small>
      </div>
    </div>
  </div>
</div>
<?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     ENQUIRY FORM (WITH RATING GATE)
════════════════════════════════════════════ -->
<section class="enquiry-section" id="enquiry">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-9">
        <div class="text-center mb-4">
          <div class="section-label">Car Preference Enquiry</div>
          <h2 class="section-title">Tell Us Your <span>Dream Ride</span></h2>
          <div class="section-divider mx-auto"></div>
          <p class="text-muted">Rate your experience with Blue Eco Fast and fill in your details below to send us your preferred car selections.</p>
        </div>

        <div class="form-card">

          <!-- STEP 1: SATISFACTION RATING -->
          <div class="mb-4 pb-4 border-bottom">
            <h5 class="fw-700 mb-1" style="color:#1e3a5f;">⭐ How satisfied are you with Blue Eco Fast?</h5>
            <p class="text-muted" style="font-size:.9rem;">Click a star to rate us.</p>
            <div class="rating-stars" id="ratingStars">
              <span class="star" data-val="1" title="Poor">★</span>
              <span class="star" data-val="2" title="Fair">★</span>
              <span class="star" data-val="3" title="Good">★</span>
              <span class="star" data-val="4" title="Very Good">★</span>
              <span class="star" data-val="5" title="Excellent">★</span>
            </div>
            <div class="star-feedback" id="starFeedback"></div>
          </div>

          <!-- STEP 2: ENQUIRY FORM -->
          <div id="enquiryFormWrap">
            <div id="formAlert" class="alert-result"></div>

            <form id="enquiryForm" novalidate>
              <!-- Honeypot anti-spam -->
              <input type="text" name="website" style="display:none;" tabindex="-1" autocomplete="off">
              <input type="hidden" name="rating" id="ratingInput" value="0">

              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label" for="fullName">Full Name <span class="text-danger">*</span></label>
                  <input type="text" class="form-control" id="fullName" name="full_name" placeholder="e.g. Jean-Paul Mugisha" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="emailAddr">Email Address <span class="text-danger">*</span></label>
                  <input type="email" class="form-control" id="emailAddr" name="email" placeholder="you@example.com" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="phoneNum">Phone Number <span class="text-danger">*</span></label>
                  <input type="tel" class="form-control" id="phoneNum" name="phone" placeholder="+250 7XX XXX XXX" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="serviceInt">Service of Interest <span class="text-danger">*</span></label>
                  <select class="form-select" id="serviceInt" name="service" required>
                    <option value="">— Select a service —</option>
                    <option>Electric Vehicle Sales</option>
                    <option>Vehicle Rental Services</option>
                    <option>Eco Taxi Services</option>
                    <option>Fleet Management Solutions</option>
                    <option>Corporate Transport Solutions</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label">Preferred Cars / Models <span class="text-danger">*</span></label>
                  <p class="text-muted mb-2" style="font-size:.83rem;">Select all models you're interested in:</p>
                  <div class="car-checkbox-group">
                    <?php
                    try {
                        $pdo = getDB();
                        $carModels = $pdo->query("SELECT name FROM cars ORDER BY name")->fetchAll(PDO::FETCH_COLUMN);
                    } catch (PDOException $e) {
                        $carModels = ['BYD Atto 3', 'BYD Seal', 'BYD e6 (MPV)', 'NETA V', 'MG ZS EV', 'NETA S', 'BYD T3 (Van)', 'Chery Omoda', 'Other / Custom'];
                    }
                    foreach ($carModels as $model): ?>
                    <label class="car-checkbox-item">
                      <input type="checkbox" name="cars[]" value="<?= htmlspecialchars($model) ?>">
                      <?= htmlspecialchars($model) ?>
                    </label>
                    <?php endforeach; ?>
                  </div>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="budget">Budget Range</label>
                  <select class="form-select" id="budget" name="budget">
                    <option value="">— Select budget —</option>
                    <option>Under USD 20,000</option>
                    <option>USD 20,000 – 30,000</option>
                    <option>USD 30,000 – 50,000</option>
                    <option>USD 50,000 – 100,000</option>
                    <option>Above USD 100,000</option>
                    <option>Open / Flexible</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label class="form-label" for="howHear">How did you hear about us?</label>
                  <select class="form-select" id="howHear" name="how_hear">
                    <option value="">— Select —</option>
                    <option>Social Media</option>
                    <option>Google Search</option>
                    <option>Word of Mouth</option>
                    <option>Advertisement</option>
                    <option>Partner / Referral</option>
                    <option>Other</option>
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label" for="msgBox">Additional Message</label>
                  <textarea class="form-control" id="msgBox" name="message" rows="4" placeholder="Tell us more about your needs, timeline, number of vehicles, etc."></textarea>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn-submit-enquiry" id="submitBtn">
                    🚀 Send My Car Enquiry
                  </button>
                  <p class="text-muted text-center mt-2" style="font-size:.78rem;">We respect your privacy. Your information will only be used to respond to your enquiry.</p>
                </div>
              </div>
            </form>
          </div>

        </div><!-- /form-card -->
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     CONTACT
════════════════════════════════════════════ -->
<section class="contact-section" id="contact">
  <div class="container">
    <div class="text-center mb-5">
      <div class="section-label">Get In Touch</div>
      <h2 class="section-title">Contact <span>Blue Eco Fast</span></h2>
      <div class="section-divider mx-auto"></div>
    </div>
    <div class="row g-4 align-items-stretch">
      <div class="col-lg-5">
        <div class="contact-info-card">
          <h4 style="font-weight:800;margin-bottom:1.5rem;">We'd love to hear from you 🚗⚡</h4>

          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div>
              <h6>Office Address</h6>
              <p>Kigali, Rwanda<br>KN 5 Road, Nyarugenge</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-telephone-fill"></i></div>
            <div>
              <h6>Phone / WhatsApp</h6>
              <p>+250 788 000 000<br>+250 722 000 000</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-envelope-fill"></i></div>
            <div>
              <h6>Email</h6>
              <p>info@blueEcoFast.rw<br>sales@blueEcoFast.rw</p>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i class="bi bi-clock-fill"></i></div>
            <div>
              <h6>Business Hours</h6>
              <p>Mon – Fri: 8:00 AM – 6:00 PM<br>Sat: 9:00 AM – 4:00 PM</p>
            </div>
          </div>

          <div class="social-links">
            <a href="#" class="social-link" title="Facebook"><i class="bi bi-facebook"></i></a>
            <a href="#" class="social-link" title="Twitter/X"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="social-link" title="Instagram"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-link" title="LinkedIn"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="social-link" title="WhatsApp"><i class="bi bi-whatsapp"></i></a>
            <a href="#" class="social-link" title="YouTube"><i class="bi bi-youtube"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-7">
        <!-- Embedded Map placeholder (replace src with real Google Maps embed) -->
        <div style="border-radius:20px;overflow:hidden;height:100%;min-height:380px;box-shadow:0 8px 32px rgba(26,86,219,0.10);">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3987.4788427059!2d30.0574!3d-1.9441!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x19dca3b9d1ddf7b1%3A0x7c7c0a2b3ef0e6d2!2sKigali%2C%20Rwanda!5e0!3m2!1sen!2srw!4v1700000000000"
            width="100%" height="100%" style="border:0;display:block;"
            allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade">
          </iframe>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ════════════════════════════════════════════
     FOOTER
════════════════════════════════════════════ -->
<footer class="footer-bef">
  <div class="container">
    <div class="row g-4">
      <div class="col-lg-4 col-md-6">
        <div class="footer-brand">
          <img src="images/logo.png" alt="Blue Eco Fast">
          <p>Rwanda's leading electric vehicle mobility company. Sustainable, affordable, and innovative transport for a greener East Africa.</p>
          <div class="social-links mt-3">
            <a href="#" class="social-link"><i class="bi bi-facebook"></i></a>
            <a href="#" class="social-link"><i class="bi bi-twitter-x"></i></a>
            <a href="#" class="social-link"><i class="bi bi-instagram"></i></a>
            <a href="#" class="social-link"><i class="bi bi-linkedin"></i></a>
            <a href="#" class="social-link"><i class="bi bi-whatsapp"></i></a>
          </div>
        </div>
      </div>
      <div class="col-lg-2 col-md-6 col-6">
        <div class="footer-heading">Quick Links</div>
        <ul class="footer-links">
          <li><a href="#home">Home</a></li>
          <li><a href="#services">Services</a></li>
          <li><a href="#fleet">Fleet</a></li>
          <li><a href="#about">About Us</a></li>
          <li><a href="#testimonials">Reviews</a></li>
          <li><a href="#contact">Contact</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6 col-6">
        <div class="footer-heading">Our Services</div>
        <ul class="footer-links">
          <li><a href="#services">EV Sales</a></li>
          <li><a href="#services">Vehicle Rental</a></li>
          <li><a href="#services">Eco Taxi</a></li>
          <li><a href="#services">Fleet Management</a></li>
          <li><a href="#services">Corporate Transport</a></li>
          <li><a href="#enquiry">Get a Quote</a></li>
        </ul>
      </div>
      <div class="col-lg-3 col-md-6">
        <div class="footer-heading">Contact Us</div>
        <ul class="footer-links">
          <li><a href="tel:+250788000000"><i class="bi bi-telephone me-1"></i>+250 788 000 000</a></li>
          <li><a href="mailto:info@blueEcoFast.rw"><i class="bi bi-envelope me-1"></i>info@blueEcoFast.rw</a></li>
          <li><i class="bi bi-geo-alt me-1"></i>Kigali, Rwanda</li>
          <li><i class="bi bi-clock me-1"></i>Mon–Fri: 8am – 6pm</li>
        </ul>
        <div class="mt-3">
          <a href="#enquiry" class="btn-enquire-nav" style="display:inline-block;padding:.5rem 1.3rem;border-radius:50px;background:linear-gradient(90deg,#1a56db,#2eb84e);color:#fff;font-weight:700;font-size:.85rem;text-decoration:none;">
            🚗 Get a Quote
          </a>
        </div>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <strong style="color:#2eb84e;">Blue Eco Fast Ltd</strong>. All rights reserved. Kigali, Rwanda.</p>
      <p>Designed with 💚 for a greener Rwanda · <em>Reimagine the Ride</em></p>
    </div>
  </div>
</footer>

<!-- Back to top -->
<button id="backToTop" title="Back to top"><i class="bi bi-arrow-up"></i></button>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="js/main.js"></script>
</body>
</html>
