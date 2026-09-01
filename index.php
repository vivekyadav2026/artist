<?php
require_once __DIR__ . '/db.php';

// Fetch all services
try {
    $services_stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $services_stmt->fetchAll();
} catch (Exception $e) {
    $services = [];
}

// Fetch all gallery items
try {
    $gallery_stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
    $gallery = $gallery_stmt->fetchAll();
} catch (Exception $e) {
    $gallery = [];
}

// Fetch all testimonials
try {
    $testimonials_stmt = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC");
    $testimonials = $testimonials_stmt->fetchAll();
} catch (Exception $e) {
    $testimonials = [];
}

// Fetch all instagram videos
try {
    $instagram_stmt = $pdo->query("SELECT * FROM instagram_videos ORDER BY id DESC");
    $instagram_videos = $instagram_stmt->fetchAll();
} catch (Exception $e) {
    $instagram_videos = [];
}

// Fetch service areas array
$service_areas_raw = getSetting('service_areas', 'Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai, Kolkata, Pune, All India Delivery');
$service_areas = array_map('trim', explode(',', $service_areas_raw));

// Dynamic Phone & Whatsapp links
$phone_number = getSetting('contact_phone', '+91 78893 50684');
$whatsapp_raw = getSetting('contact_whatsapp', '917889350684');
$whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp_raw);

?>
<?php
include __DIR__ . '/includes/header.php';
?>

    <!-- HERO SECTION (Elegant Gallery Split Layout) -->
    <section class="hero" id="home">
        <div class="container hero-wrapper">
            <div class="hero-content">
                <span class="hero-tagline">★ Original Fine Art</span>
                <h1><?php echo htmlspecialchars(getSetting('hero_heading', 'Transforming Spaces with Vibrant Geometry.')); ?></h1>
                <p><?php echo htmlspecialchars(getSetting('hero_subheading', 'Explore original hand-signed acrylic canvas works and geometric triangle series exploring depths, color transparency, and layered rhythm by Rakesh Verma.')); ?></p>
                <div class="hero-actions">
                    <a href="portfolio.php" class="btn btn-primary">View Portfolio</a>
                    <!-- <a href="contact.php" class="btn btn-outline">Commission Art</a> -->
                </div>
            </div>
            
            <!-- Framed Canvas Mockup -->
            <div class="hero-canvas-frame">
                <div class="canvas-wrapper">
                    <?php if (file_exists(__DIR__ . '/assets/images/gallery-1.jpg')): ?>
                        <img src="assets/images/gallery-1.jpg" alt="Featured Canvas Painting: <?php echo htmlspecialchars(getSetting('hero_canvas_title', 'Ember Triangles I')); ?>">
                    <?php else: ?>
                        <div style="background:#132238; height:100%; display:flex; align-items:center; justify-content:center; color:white; font-family:var(--font-heading);">🎨 Canvas</div>
                    <?php endif; ?>
                </div>
                <div class="canvas-label">
                    <span><?php echo htmlspecialchars(getSetting('hero_canvas_title', 'Ember Triangles I')); ?></span>
                    <span><?php echo htmlspecialchars(getSetting('hero_canvas_medium', 'Acrylic on Linen Canvas')); ?></span>
                </div>
            </div>
        </div>
    </section>

    <!-- ACHIEVEMENTS STATS SECTION -->
    <section class="stats-section">
        <div class="container stats-grid">
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('stat_artworks', '250+')); ?></div>
                <div class="stat-label">Artworks Created</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('stat_experience', '10+')); ?></div>
                <div class="stat-label">Years of Experience</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('stat_satisfied', '98%')); ?></div>
                <div class="stat-label">Satisfied Collectors</div>
            </div>
            <div class="stat-item">
                <div class="stat-number"><?php echo htmlspecialchars(getSetting('stat_shows', '15+')); ?></div>
                <div class="stat-label">Solo &amp; Group Shows</div>
            </div>
        </div>
    </section>

    <!-- ABOUT US SECTION -->
    <section class="py-section" id="about">
        <div class="container about-wrapper">
            <div class="about-images-collage">
                <div class="about-img-main" style="width: 85%; height: 380px; overflow: hidden; border-radius: 12px; background: #e2e8f0;">
                    <?php if (file_exists(__DIR__ . '/assets/images/rakesh-verma.jpg')): ?>
                        <img src="assets/images/rakesh-verma.jpg?v=<?php echo filemtime(__DIR__ . '/assets/images/rakesh-verma.jpg'); ?>" style="width:100%; height:100%; object-fit:cover; object-position:top;" alt="Rakesh Verma portrait">
                    <?php elseif (file_exists(__DIR__ . '/assets/images/service-interior.jpg')): ?>
                        <img src="assets/images/service-interior.jpg" style="width:100%; height:100%; object-fit:cover;" alt="About Us banner image">
                    <?php else: ?>
                        <div class="placeholder-svg-bg">
                            <span>🏠 Quality Projects</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="about-img-badge">
                    <h4><?php echo htmlspecialchars(getSetting('stat_experience', '20+')); ?></h4>
                    <p>Years of Delivering Brilliance</p>
                </div>
            </div>
            
            <div class="about-info">
                <span class="badge">Meet the Artist</span>
                <h2><?php echo htmlspecialchars(getSetting('about_heading', 'About the Artist — Rakesh Verma')); ?></h2>
                <p><?php echo nl2br(htmlspecialchars(getSetting('about_description', 'Rakesh Verma is a contemporary abstract painter exploring geometric rhythms, color overlays, and layered textures. Working primarily with acrylics, palette knives, and stencils on high-grade canvas, Rakesh\'s signature triangle series explores visual depth and structural patterns. His works reside in private collections and modern interiors.'))); ?></p>
                
                <div class="about-features">
                    <div class="about-feature-item">
                        <span>✔</span> <?php echo htmlspecialchars(getSetting('about_feature_1', 'Original Signed Artworks')); ?>
                    </div>
                    <div class="about-feature-item">
                        <span>✔</span> <?php echo htmlspecialchars(getSetting('about_feature_2', 'Archivable Linen Canvas')); ?>
                    </div>
                    <div class="about-feature-item">
                        <span>✔</span> <?php echo htmlspecialchars(getSetting('about_feature_3', 'Professional Acrylics & Oils')); ?>
                    </div>
                    <div class="about-feature-item">
                        <span>✔</span> <?php echo htmlspecialchars(getSetting('about_feature_4', 'Worldwide Secure Shipping')); ?>
                    </div>
                </div>
                
                <a href="#contact" class="btn btn-primary">Book Consultation</a>
            </div>
        </div>
    </section>

    <!-- OUR SKILL WHAT WE ACHIEVED SECTION (Navy Split block from reference) -->
    <section class="skills-achieved-section">
        <div class="container skills-wrapper">
            <div class="skills-content">
                <h2>Our Skill What We Achieved</h2>
                <p>Over the past 20 years, Rakesh Verma has consistently delivered fine art masterpieces. We measure our achievements in the emotional depth of each brush stroke and in the satisfaction of global art collectors who acquire our works.</p>
                
                <div class="skills-stats-grid">
                    <div class="skill-stat-box">
                        <h4><?php echo htmlspecialchars(getSetting('skill_stat_paintings', '250+')); ?></h4>
                        <span>Paintings Made</span>
                    </div>
                    <div class="skill-stat-box">
                        <h4><?php echo htmlspecialchars(getSetting('skill_stat_sold', '120+')); ?></h4>
                        <span>Canvas Sold</span>
                    </div>
                    <div class="skill-stat-box">
                        <h4><?php echo htmlspecialchars(getSetting('skill_stat_collectors', '35+')); ?></h4>
                        <span>Global Collectors</span>
                    </div>
                </div>
            </div>
            
            <div class="skills-img-box">
                <?php if (file_exists(__DIR__ . '/assets/images/gallery-1.jpg')): ?>
                    <img src="assets/images/gallery-1.jpg" style="width:100%; height:320px; object-fit:cover;" alt="Achieved projects illustration frame">
                <?php else: ?>
                    <div style="background:#102a43; height:320px; display:flex; align-items:center; justify-content:center; color:white;">🎨 Framed Masterpiece</div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- PAINTING SERVICES SECTION -->
    <section class="py-section bg-light" id="services">
        <div class="container">
            <div class="section-header">
                <span class="badge">What We Do</span>
                <h2>Our Art Collections</h2>
                <p>Explore signed acrylic canvas series and textured spatula patterns.</p>
            </div>
            
            <div class="services-grid">
                <?php if (empty($services)): ?>
                    <p style="grid-column: span 4; text-align: center; color: var(--text-light);">No services configured yet. Add them in the Admin Panel!</p>
                <?php else: ?>
                    <?php foreach($services as $srv): ?>
                        <div class="service-card">
                            <div class="service-img-wrapper">
                                <?php if (!empty($srv['image_path']) && file_exists(__DIR__ . '/' . $srv['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($srv['image_path']); ?>" alt="<?php echo htmlspecialchars($srv['title']); ?>">
                                <?php else: ?>
                                    <div class="placeholder-svg-bg">
                                        <span>🎨 <?php echo htmlspecialchars($srv['title']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="service-body">
                                <h3><?php echo htmlspecialchars($srv['title']); ?></h3>
                                <p><?php echo htmlspecialchars($srv['description']); ?></p>
                                <div class="service-footer">
                                    <a href="#contact" class="btn">Enquire Now</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- SECOND ENQUIRY CTA FORM BANNER -->
    <section class="enquiry-row-cta">
        <div class="container enquiry-row-grid">
            <div class="enquiry-row-form-card">
                <h3>For Any Query</h3>
                <form class="enquiry-form" method="POST">
                    <div class="form-message"></div>
                    <input type="text" name="name" class="form-input" placeholder="Your Name" required>
                    <input type="tel" name="phone" class="form-input" placeholder="Mobile Number" required>
                    <textarea name="message" class="form-input" placeholder="Tell us about your requirements" style="height:80px;"></textarea>
                    <button type="submit" class="btn btn-primary" style="width:100%;">Submit Request</button>
                </form>
            </div>
            
            <div class="enquiry-row-text">
                <h2>Acquire Original Canvas Art</h2>
                <p>Contact the artist directly to check painting dimensions, pricing, and framing options. We offer digital visualizations showing how the abstract canvas will look on your living room walls before finalizing the purchase!</p>
                <div style="display:flex; gap:15px; flex-wrap:wrap;">
                    <a href="tel:<?php echo htmlspecialchars($phone_number); ?>" class="btn btn-accent">Call: <?php echo htmlspecialchars($phone_number); ?></a>
                    <a href="<?php echo htmlspecialchars($whatsapp_url); ?>" target="_blank" class="btn" style="background:#25d366; color:white;">WhatsApp Us</a>
                </div>
            </div>
        </div>
    </section>

    <!-- PROJECT WORK GALLERY SECTION -->
    <section class="py-section" id="gallery">
        <div class="container">
            <div class="section-header">
                <span class="badge">Our Portfolio</span>
                <h2>Abstract Art Collections</h2>
                <p>Explore original triangle pattern overlays, textured acrylics, and blue geometric series by Rakesh Verma.</p>
            </div>
            
            <!-- Category Filter Tabs -->
            <div class="gallery-filters">
                <button class="filter-btn active" data-filter="all">All Artworks</button>
                <button class="filter-btn" data-filter="interior">Teal &amp; Cool Tones</button>
                <button class="filter-btn" data-filter="exterior">Orange &amp; Red Tones</button>
                <button class="filter-btn" data-filter="texture">Textured Acrylics</button>
                <button class="filter-btn" data-filter="commercial">Canvas Prints</button>
            </div>
            
            <div class="gallery-grid">
                <?php if (empty($gallery)): ?>
                    <p style="grid-column: span 3; text-align: center; color: var(--text-light);">No project gallery images uploaded yet.</p>
                <?php else: ?>
                    <?php foreach($gallery as $item): 
                        // Construct direct WhatsApp enquiry link
                        $whatsapp_raw = getSetting('contact_whatsapp', '917889350684');
                        $whatsapp_num = preg_replace('/[^0-9]/', '', $whatsapp_raw);
                        
                        $artwork_msg = "Hello Rakesh Verma,\n\n";
                        $artwork_msg .= "I am interested in acquiring your artwork: *" . $item['title'] . "*\n";
                        $artwork_msg .= "*Medium*: " . ($item['medium'] ?: '—') . "\n";
                        $artwork_msg .= "*Size*: " . ($item['size'] ?: '—') . "\n";
                        $artwork_msg .= "*Price*: " . ($item['price'] ?: 'Inquire') . "\n";
                        $artwork_msg .= "*Availability*: " . ($item['available'] ?: 'Available') . "\n\n";
                        $artwork_msg .= "Please let me know how to proceed.";
                        
                        $artwork_wa_url = "https://wa.me/" . $whatsapp_num . "?text=" . urlencode($artwork_msg);
                    ?>
                        <div class="gallery-item" data-category="<?php echo htmlspecialchars(strtolower($item['category'])); ?>" style="padding: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: white;">
                            <div class="gallery-item-inner" style="position: relative; overflow: hidden; aspect-ratio: 4/3; height: auto !important; border-radius: 4px;">
                                <?php if (!empty($item['image_path']) && file_exists(__DIR__ . '/' . $item['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
                                <?php else: ?>
                                    <div class="placeholder-svg-bg" style="background: linear-gradient(135deg, #1e293b, #334155);">
                                        <span>📸 <?php echo htmlspecialchars($item['title']); ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="gallery-info" style="padding: 12px 2px 2px 2px;">
                                <h4 style="font-size: 17px; margin: 0 0 6px 0; font-family: var(--font-heading); color: var(--text-ink); line-height: 1.3;"><?php echo htmlspecialchars($item['title']); ?></h4>
                                
                                <!-- Portfolio Details -->
                                <div style="font-size: 12px; color: var(--text-muted); margin-bottom: 8px; line-height: 1.5; display: flex; flex-direction: column; gap: 2px;">
                                    <span><strong>Medium:</strong> <?php echo htmlspecialchars($item['medium'] ?: '—'); ?></span>
                                    <span><strong>Size:</strong> <?php echo htmlspecialchars($item['size'] ?: '—'); ?></span>
                                    <span><strong>Year:</strong> <?php echo htmlspecialchars($item['year'] ?: '—'); ?></span>
                                </div>

                                <div style="font-size: 11px; margin-bottom: 10px;">
                                    <span style="background-color: #f1f5f9; padding: 3px 8px; border-radius: 12px; text-transform: uppercase; font-weight: 700; color: #475569; font-size: 9.5px; letter-spacing: 0.03em;">
                                        <?php 
                                            if (strtolower($item['category']) === 'interior') echo 'Teal & Cool Tones';
                                            elseif (strtolower($item['category']) === 'exterior') echo 'Orange & Red Tones';
                                            elseif (strtolower($item['category']) === 'texture') echo 'Textured Acrylics';
                                            elseif (strtolower($item['category']) === 'commercial') echo 'Canvas Prints';
                                            else echo htmlspecialchars($item['category']);
                                        ?>
                                    </span>
                                </div>

                                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 8px; border-top: 1px solid var(--border); padding-top: 10px;">
                                    <div>
                                        <span style="font-size: 14px; font-weight: 700; color: var(--primary); display: block; line-height: 1.2;">
                                            <?php echo htmlspecialchars(!empty($item['price']) ? $item['price'] : 'Inquire'); ?>
                                        </span>
                                        <span style="font-size: 9.5px; font-weight: 700; text-transform: uppercase; color: <?php echo (isset($item['available']) && strtolower($item['available']) === 'sold') ? '#b91c1c' : ((isset($item['available']) && strtolower($item['available']) === 'reserved') ? '#d97706' : '#15803d'); ?>;">
                                            ● <?php echo htmlspecialchars(isset($item['available']) ? $item['available'] : 'Available'); ?>
                                        </span>
                                    </div>
                                    <a href="<?php echo htmlspecialchars($artwork_wa_url); ?>" target="_blank" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center; padding-bottom: 2px;">Inquire &rarr;</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- WHY CHOOSE US SECTION -->
    <section class="py-section bg-light" id="why-choose-us">
        <div class="container">
            <div class="section-header">
                <span class="badge">Art Authenticity</span>
                <h2><?php echo htmlspecialchars(getSetting('why_choose_us_headline', 'Why Collect Rakesh Verma Artworks')); ?></h2>
                <p>We provide museum-grade execution and a secure curation experience for contemporary art collectors.</p>
            </div>
            
            <div class="features-grid">
                <div class="feature-card">
                    <div class="feature-card-icon">⚡</div>
                    <h3><?php echo htmlspecialchars(getSetting('why_collect_title_1', 'Certified Originality')); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('why_collect_desc_1', 'Each original painting is hand-signed by Rakesh Verma and comes with a registered, stamped Certificate of Authenticity.')); ?></p>
                </div>
                <!--
                <div class="feature-card">
                    <div class="feature-card-icon">👥</div>
                    <h3><?php echo htmlspecialchars(getSetting('why_collect_title_2', 'Bespoke Art Sizing')); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('why_collect_desc_2', 'Commission works designed specifically to complement the dimensions and color schemes of your residential or commercial walls.')); ?></p>
                </div>
                -->
                <div class="feature-card">
                    <div class="feature-card-icon">⭐</div>
                    <h3><?php echo htmlspecialchars(getSetting('why_collect_title_3', 'Archival Quality Materials')); ?></h3>
                    <p><?php echo htmlspecialchars(getSetting('why_collect_desc_3', 'Created using premium-grade linen canvas and fade-resistant pigment acrylics to guarantee lifetime durability without color degradation.')); ?></p>
                </div>
            </div>
        </div>
    </section>

    <!-- WATCH THE CREATIVE PROCESS (INSTAGRAM SLIDER INTEGRATION) -->
    <?php if (!empty($instagram_videos)): ?>
    <section class="py-section bg-light" id="studio-video" style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border); overflow: hidden;">
        <div class="container">
            <div class="section-header" style="text-align: center; margin-bottom: 45px;">
                <span class="badge">Studio Reels</span>
                <h2>Watch the Creative Process</h2>
                <p>Follow along with the layers, details, and palette knife strokes directly from Rakesh Verma's studio.</p>
            </div>
            
            <!-- Horizontal Slider Container -->
            <div class="video-slider-container" style="position: relative; max-width: 1080px; margin: 0 auto; padding: 0 15px;">
                <div class="video-slider-track" style="display: flex; gap: 24px; overflow-x: auto; scroll-behavior: smooth; padding-bottom: 15px; -webkit-overflow-scrolling: touch; scroll-snap-type: x mandatory;">
                    <?php foreach($instagram_videos as $vid): 
                        $instagram_embed_url = '';
                        if (preg_match('/(?:p|reel|tv)\/([a-zA-Z0-9-_]+)/', $vid['url'], $matches)) {
                            $instagram_embed_url = "https://www.instagram.com/p/" . $matches[1] . "/embed/";
                        }
                        if (!empty($instagram_embed_url)):
                    ?>
                        <div class="video-slide-item" style="flex: 0 0 326px; scroll-snap-align: start; box-shadow: var(--shadow-md); border-radius: 8px; overflow: hidden; border: 1px solid var(--border); background: white;">
                            <div style="background: var(--bg-paper); padding: 12px 16px; border-bottom: 1px solid var(--border);">
                                <span style="font-size: 11px; font-weight: 700; color: var(--primary); text-transform: uppercase; letter-spacing: 0.05em; display: block; margin-bottom: 2px;">Process Video</span>
                                <strong style="font-size: 13.5px; color: var(--text-ink); display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;"><?php echo htmlspecialchars($vid['title']); ?></strong>
                            </div>
                            <iframe src="<?php echo htmlspecialchars($instagram_embed_url); ?>" class="instagram-media" allowtransparency="true" allowfullscreen="true" frameborder="0" height="500" scrolling="no" style="background: white; border: none; width: 100%; display: block;"></iframe>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    ?>
                </div>
                
                <!-- Helper Slider Control Arrows (Hidden on mobile touch screens for native swiping) -->
                <button onclick="document.querySelector('.video-slider-track').scrollBy({left: -350, behavior: 'smooth'})" class="slider-arrow prev" style="position: absolute; left: -25px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: white; border: 1px solid var(--border); box-shadow: var(--shadow-md); font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; color: var(--text-ink); transition: all 0.2s;">&larr;</button>
                <button onclick="document.querySelector('.video-slider-track').scrollBy({left: 350, behavior: 'smooth'})" class="slider-arrow next" style="position: absolute; right: -25px; top: 50%; transform: translateY(-50%); width: 44px; height: 44px; border-radius: 50%; background: white; border: 1px solid var(--border); box-shadow: var(--shadow-md); font-size: 16px; cursor: pointer; display: flex; align-items: center; justify-content: center; z-index: 10; color: var(--text-ink); transition: all 0.2s;">&rarr;</button>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <!-- TESTIMONIALS / REVIEWS SECTION (Vibrant Split Layout from reference) -->
    <section class="testimonials-section" id="reviews">
        <div class="container">
            <div class="testimonials-wrapper">
                <div class="testimonials-left-banner">
                    <span class="badge" style="background:rgba(255,255,255,0.15); color:white;">Reviews</span>
                    <h2>What Art Collectors Say</h2>
                    <p>Hear from art collectors and interior decorators who have acquired original canvas pieces by Rakesh Verma.</p>
                </div>
                
                <div class="testimonials-slider-box">
                    <div class="testimonials-grid">
                        <?php if (empty($testimonials)): ?>
                            <p style="text-align: center; color: var(--text-light);">No customer reviews available yet.</p>
                        <?php else: ?>
                            <?php foreach(array_slice($testimonials, 0, 1) as $t): ?>
                                <div class="testimonial-card">
                                    <div class="rating">
                                        <?php for($i=1; $i<=5; $i++): ?>
                                            <?php echo ($i <= $t['rating']) ? '★' : '☆'; ?>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="review-text">"<?php echo htmlspecialchars($t['review']); ?>"</p>
                                    <div class="reviewer-info">
                                        <div class="reviewer-avatar">
                                            <?php echo htmlspecialchars(substr($t['name'], 0, 1)); ?>
                                        </div>
                                        <div class="reviewer-details">
                                            <h4><?php echo htmlspecialchars($t['name']); ?></h4>
                                            <span><?php echo htmlspecialchars($t['location']); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- COURSE WORKFLOW / PROCESS SECTION (Commented Out)
    <section class="py-section bg-light">
        <div class="container">
            <div class="section-header">
                <span class="badge">Workflow</span>
                <h2>Art Commissioning Process</h2>
                <p>Four simple steps from discussing your abstract art vision to hanging the custom canvas on your wall.</p>
            </div>
            
            <div class="process-wrapper">
                <div class="process-step">
                    <div class="process-num">1</div>
                    <h3>Art Consultation</h3>
                    <p>Discuss your desired canvas dimensions, color palettes, and texture preferences.</p>
                </div>
                <div class="process-step">
                    <div class="process-num">2</div>
                    <h3>Concept Layout</h3>
                    <p>We share small digital previews or paper sketches to align on geometric balance.</p>
                </div>
                <div class="process-step">
                    <div class="process-num">3</div>
                    <h3>Canvas Creation</h3>
                    <p>Rakesh Verma hand-paints the canvas, layering colors and structured overlays.</p>
                </div>
                <div class="process-step">
                    <div class="process-num">4</div>
                    <h3>Secure Handover</h3>
                    <p>Your finished canvas is securely packaged and delivered to your doorstep.</p>
                </div>
            </div>
        </div>
    </section>
    -->

    <!-- SERVICE AREAS TAG CLOUD SECTION -->
    <section class="py-section areas-section">
        <div class="container text-center">
            <span class="badge" style="background:rgba(255,255,255,0.15); color:#ffffff; margin-bottom: 20px;">📍 Locations</span>
            <h2 style="margin-bottom: 12px; color: white;">Art Gallery &amp; All India Delivery</h2>
            <p style="margin-bottom: 40px; max-width: 600px; margin-left: auto; margin-right: auto;">We offer safe packaging, transit insurance, and doorstep delivery across all states and Union Territories in India:</p>
            
            <div class="areas-grid">
                <?php foreach($service_areas as $area): ?>
                    <a href="#contact" class="area-tag">
                        <span>📍</span>
                        <?php echo htmlspecialchars($area); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- CONTACT US SECTION & FORM -->
    <section class="py-section" id="contact">
        <div class="container">
            <div class="contact-wrapper">
                <div class="contact-details">
                    <span class="badge">Get in Touch</span>
                    <h2>Talk Directly to the Artist</h2>
                    <p>Have questions about canvas dimensions, custom abstract prints, or original releases? Send us a message or schedule a callback.</p>
                    
                    <div class="contact-info-list">
                        <div class="contact-info-item">
                            <div class="contact-info-icon">📞</div>
                            <div class="contact-info-content">
                                <h4>Call Directly</h4>
                                <p><?php echo htmlspecialchars($phone_number); ?></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon">💬</div>
                            <div class="contact-info-content">
                                <h4>WhatsApp Message</h4>
                                <p><a href="<?php echo htmlspecialchars($whatsapp_url); ?>" target="_blank" style="color:var(--primary); font-weight:700;">Start Live Chat</a></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon">✉️</div>
                            <div class="contact-info-content">
                                <h4>Email Enquiries</h4>
                                <p><?php echo htmlspecialchars(getSetting('contact_email', 'contact@rakeshverma.art')); ?></p>
                            </div>
                        </div>
                        <div class="contact-info-item">
                            <div class="contact-info-icon">🏢</div>
                            <div class="contact-info-content">
                                <h4>Registered Office</h4>
                                <p><?php echo htmlspecialchars(getSetting('contact_address', 'Noida Sec 62, Uttar Pradesh')); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Contact Form Panel -->
                <div class="contact-form-panel">
                    <h3>Send Enquiry Message</h3>
                    <p>We generally revert with phone callbacks within 30 minutes.</p>
                    <form class="enquiry-form" method="POST">
                        <div class="form-message"></div>
                        <div class="form-grid-row">
                            <div class="admin-form-group" style="margin-bottom:0;">
                                <input type="text" name="name" class="form-input" placeholder="Your Name *" required>
                            </div>
                            <div class="admin-form-group" style="margin-bottom:0;">
                                <input type="tel" name="phone" class="form-input" placeholder="Your Phone *" required>
                            </div>
                        </div>
                        <input type="email" name="email" class="form-input" placeholder="Your Email Address">
                        
                        <select name="service" class="form-input">
                            <option value="General Consultation">Interested In *</option>
                            <?php foreach($services as $srv): ?>
                                <option value="<?php echo htmlspecialchars($srv['title']); ?>"><?php echo htmlspecialchars($srv['title']); ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <textarea name="message" class="form-input" placeholder="Type your message details here..." required></textarea>
                        
                        <button type="submit" class="btn btn-primary">Send Message Now</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- GOOGLE MAP DYNAMIC WRAPPER -->
    <div class="map-wrapper">
        <?php echo getSetting('google_map_iframe', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3503.7431264879685!2d77.31342677613589!3d28.57748438659174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce45bf9485bdf%3A0xe54d6d37ad751ec!2sNoida%20Sector%2018!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'); ?>
    </div>

<?php
include __DIR__ . '/includes/footer.php';
?>
