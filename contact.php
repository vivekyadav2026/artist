<?php
require_once __DIR__ . '/db.php';

// Fetch all services/collections for the select dropdown
try {
    $services_stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
    $services = $services_stmt->fetchAll();
} catch (Exception $e) {
    $services = [];
}

// Prefill values
$prefilled_service = isset($_GET['service']) ? trim($_GET['service']) : '';
$prefilled_artwork = isset($_GET['artwork']) ? trim($_GET['artwork']) : '';
$message_placeholder = '';
if (!empty($prefilled_artwork)) {
    $message_placeholder = "Hi Rakesh, I am interested in acquiring the original painting: \"" . htmlspecialchars($prefilled_artwork) . "\". Please share pricing and shipping details.";
}

include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB / HERO SUBPAGE BANNER -->
<section class="subpage-banner">
    <div class="container text-center">
        <span class="hero-tagline">Acquisitions</span>
        <h1>Contact &amp; Commission</h1>
        <p>Send a message to discuss original canvas sales, custom murals, or framing estimates.</p>
    </div>
</section>

<!-- CONTACT SECTION & FORM -->
<section class="py-section">
    <div class="container">
        <div class="contact-wrapper">
            <div class="contact-details">
                <span class="badge" style="background-color: rgba(193, 68, 14, 0.08); color: var(--primary);">Get in Touch</span>
                <h2 style="margin-top: 10px;">Talk Directly to the Artist</h2>
                <p>Have questions about canvas dimensions, custom abstract prints, or commissioning a geometric series? Send us a message or schedule a callback.</p>
                
                <div class="contact-info-list" style="margin-top: 30px;">
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
                            <p><?php echo htmlspecialchars(getSetting('contact_email', 'info@colorluxpainters.com')); ?></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">🏢</div>
                        <div class="contact-info-content">
                            <h4>Registered Studio</h4>
                            <p><?php echo htmlspecialchars(getSetting('contact_address', 'Noida Sec 62, Uttar Pradesh')); ?></p>
                        </div>
                    </div>
                    <div class="contact-info-item">
                        <div class="contact-info-icon">🌐</div>
                        <div class="contact-info-content">
                            <h4>Social Channels</h4>
                            <p style="display:flex; gap:12px; margin-top:5px; font-size:14px; font-weight:normal;">
                                <a href="https://www.instagram.com/rakesh_verma_1982/" target="_blank" style="color:var(--primary); font-weight:700;">📸 Instagram</a>
                                <a href="https://www.facebook.com/Rakeshvermaartist" target="_blank" style="color:#3b82f6; font-weight:700;">👥 Facebook</a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Contact Form Panel -->
            <div class="contact-form-panel">
                <h3>Send Enquiry Message</h3>
                <p>We generally revert with phone callbacks within 30 minutes.</p>
                <form class="enquiry-form" method="POST" action="submit-enquiry.php">
                    <div class="form-message"></div>
                    <div class="form-grid-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div class="admin-form-group" style="margin-bottom:0;">
                            <input type="text" name="name" class="form-input" placeholder="Your Name *" required style="width:100%; padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm);">
                        </div>
                        <div class="admin-form-group" style="margin-bottom:0;">
                            <input type="tel" name="phone" class="form-input" placeholder="Your Phone *" required style="width:100%; padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm);">
                        </div>
                    </div>
                    <input type="email" name="email" class="form-input" placeholder="Your Email Address" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm); margin-bottom: 15px;">
                    
                    <select name="service" class="form-input" style="width:100%; padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm); margin-bottom: 15px; background:white;">
                        <option value="General Consultation">Interested In *</option>
                        <?php foreach($services as $srv): ?>
                            <option value="<?php echo htmlspecialchars($srv['title']); ?>" <?php echo ($prefilled_service === $srv['title']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($srv['title']); ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="Custom Commissions" <?php echo ($prefilled_service === 'Custom Commissions') ? 'selected' : ''; ?>>Custom Commissions</option>
                    </select>
                    
                    <textarea name="message" class="form-input" placeholder="Type your message details here..." required style="width:100%; height:120px; padding:12px; border:1px solid var(--border); border-radius:var(--radius-sm); margin-bottom: 15px; resize:vertical;"><?php echo htmlspecialchars($message_placeholder); ?></textarea>
                    
                    <button type="submit" class="btn btn-primary" style="width:100%;">Send Message Now</button>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- GOOGLE MAP DYNAMIC WRAPPER -->
<div class="map-wrapper" style="border-top: 1px solid var(--border); line-height: 0;">
    <?php echo getSetting('google_map_iframe', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3503.7431264879685!2d77.31342677613589!3d28.57748438659174!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390ce45bf9485bdf%3A0xe54d6d37ad751ec!2sNoida%20Sector%2018!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>'); ?>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>
