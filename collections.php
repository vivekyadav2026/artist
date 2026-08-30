<?php
require_once __DIR__ . '/db.php';

// Fetch all services/collections
try {
    $services_stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $services_stmt->fetchAll();
} catch (Exception $e) {
    $services = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB / HERO SUBPAGE BANNER -->
<section class="subpage-banner">
    <div class="container text-center">
        <span class="hero-tagline">Curated Series</span>
        <h1>Art Collections &amp; Services</h1>
        <p>Original canvas releases and hotel/corporate projects.</p>
    </div>
</section>

<!-- SERVICES GRID -->
<section class="py-section">
    <div class="container">
        <div class="services-grid">
            <?php if (empty($services)): ?>
                <p style="grid-column: span 2; text-align: center; color: var(--text-light); padding: 40px 0;">No collections configured yet.</p>
            <?php else: ?>
                <?php foreach($services as $srv): 
                    $whatsapp_raw = getSetting('contact_whatsapp', '917889350684');
                    $whatsapp_num = preg_replace('/[^0-9]/', '', $whatsapp_raw);
                    $collection_msg = "Hello Rakesh Verma,\n\nI am interested in your art collection: *" . $srv['title'] . "*.\n\nPlease share more details and availability.";
                    $collection_wa_url = "https://wa.me/" . $whatsapp_num . "?text=" . urlencode($collection_msg);
                ?>
                    <div class="service-card">
                        <div class="service-img-wrapper">
                            <?php if (!empty($srv['image_path']) && file_exists(__DIR__ . '/' . $srv['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($srv['image_path']); ?>" alt="<?php echo htmlspecialchars($srv['title']); ?>" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <div class="placeholder-svg-bg" style="background: linear-gradient(135deg, #1e293b, #334155); height: 100%; display: flex; align-items: center; justify-content: center; color: white;">
                                    <span>🎨 <?php echo htmlspecialchars($srv['title']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="service-body">
                            <h3><?php echo htmlspecialchars($srv['title']); ?></h3>
                            <p><?php echo htmlspecialchars($srv['description']); ?></p>
                            <div class="service-footer">
                                <a href="<?php echo htmlspecialchars($collection_wa_url); ?>" target="_blank" class="btn btn-primary" style="width: 100%; text-align: center; text-transform: uppercase; font-size: 13px; font-weight:700;">Inquire on this Collection</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- FAQS ON COMMISSIONS (Commented Out)
<section class="py-section bg-light" style="border-top: 1px solid var(--border);">
    <div class="container">
        <div class="section-header">
            <span class="badge">Art Acquisition</span>
            <h2>Frequently Asked Questions</h2>
            <p>Important details regarding custom sizing, color coordination, framing, and delivery shipping.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <h3 style="font-size: 17px; margin-bottom: 8px;">How do custom commissions work?</h3>
                <p style="font-size: 13px; color: var(--text-muted); line-height:1.6;">You share the wall size and desired colors. Rakesh provides 2-3 paper sketches. Once approved, painting starts. It takes 3-4 weeks for layered acrylic textures to dry before secure shipping.</p>
            </div>
            <div class="feature-card">
                <h3 style="font-size: 17px; margin-bottom: 8px;">Are paintings framed?</h3>
                <p style="font-size: 13px; color: var(--text-muted); line-height:1.6;">By default, original canvases are shipped rolled in heavy mailing tubes to ensure safe transport. If requested, stretching or custom wood frame boxes can be done locally or estimated on demand.</p>
            </div>
            <div class="feature-card">
                <h3 style="font-size: 17px; margin-bottom: 8px;">Do they come with certificates?</h3>
                <p style="font-size: 13px; color: var(--text-muted); line-height:1.6;">Yes! All original canvas commissions and limited collection releases come with a registered, hand-signed Certificate of Authenticity specifying colors, dimensions, and index numbers.</p>
            </div>
        </div>
    </div>
</section>
-->

<?php
include __DIR__ . '/includes/footer.php';
?>
