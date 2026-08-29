<?php
require_once __DIR__ . '/../db.php';

// Fetch global settings for header if not already defined
if (!isset($phone_number)) {
    $phone_number = getSetting('contact_phone', '+91 98765 43210');
}
if (!isset($whatsapp_url)) {
    $whatsapp_raw = getSetting('contact_whatsapp', '919876543210');
    $whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp_raw);
}

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Dynamic SEO Optimization -->
    <title><?php echo htmlspecialchars(getSetting('site_title', 'Rakesh Verma | Abstract Artist & Modern Painter')); ?></title>
    <meta name="description" content="<?php echo htmlspecialchars(getSetting('site_description', 'Original abstract paintings on canvas.')); ?>">
    <meta name="keywords" content="<?php echo htmlspecialchars(getSetting('site_keywords', 'abstract paintings, rakesh verma')); ?>">
    
    <!-- Google Fonts & Custom Styling -->
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,600;0,9..144,700;1,9..144,500&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Texture Paper Grain Overlay -->
    <div class="grain-overlay"></div>

    <!-- Announcement Bar -->
    <div class="announcement-bar">
        📢 New original canvas collection 'Ember Triangles' now available for acquisition
    </div>

    <!-- MINIMALIST SITE HEADER -->
    <header class="site-header">
        <div class="container header-wrapper">
            <div class="brand">
                <h2><a href="index.php">Rakesh Verma</a></h2>
                <span>Abstract Artist</span>
            </div>
            
            <nav class="main-nav">
                <ul>
                    <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><a href="index.php">Home</a></li>
                    <li class="<?php echo ($current_page == 'about.php') ? 'active' : ''; ?>"><a href="about.php">About</a></li>
                    <li class="<?php echo ($current_page == 'collections.php') ? 'active' : ''; ?>"><a href="collections.php">Collections</a></li>
                    <li class="<?php echo ($current_page == 'portfolio.php') ? 'active' : ''; ?>"><a href="portfolio.php">Portfolio</a></li>
                    <li class="<?php echo ($current_page == 'contact.php') ? 'active' : ''; ?>"><a href="contact.php">Contact</a></li>
                </ul>
                <a href="contact.php" class="btn-nav-cta">Request Commission</a>
            </nav>
            <button class="mobile-nav-toggle" aria-label="Toggle Menu">&#9776;</button>
        </div>
    </header>
