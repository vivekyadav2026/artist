<?php
// Ensure variables are defined
if (!isset($phone_number)) {
    $phone_number = getSetting('contact_phone', '+91 78893 50684');
}
if (!isset($whatsapp_url)) {
    $whatsapp_raw = getSetting('contact_whatsapp', '917889350684');
    $whatsapp_url = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsapp_raw);
}
?>
    <!-- FOOTER -->
    <footer>
        <div class="container footer-grid">
            <div class="footer-about">
                <div class="footer-logo">
                    <div class="footer-logo-icon">🎨</div>
                    <h3>Rakesh Verma</h3>
                </div>
                <p style="margin-bottom: 20px;">Contemporary abstract painter specializing in geometric patterns, structured overlays, and textured acrylic canvas series. Shipping original canvas art worldwide.</p>
                <p>🎨 Original art that inspires modern living spaces.</p>
                <div class="footer-socials" style="margin-top: 20px; display: flex; gap: 15px; font-size: 13.5px;">
                    <a href="https://www.instagram.com/rakesh_verma_1982/" target="_blank" style="color: var(--primary); font-weight:700; display:inline-flex; align-items:center; gap:5px;">📸 Instagram</a>
                    <a href="https://www.facebook.com/Rakeshvermaartist" target="_blank" style="color: #3b82f6; font-weight:700; display:inline-flex; align-items:center; gap:5px;">👥 Facebook</a>
                </div>
            </div>
            
            <div class="footer-col">
                <h4>Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="index.php">Home</a></li>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="collections.php">Art Collections</a></li>
                    <li><a href="portfolio.php">Artwork Portfolio</a></li>
                    <li><a href="contact.php">Contact Us</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Our Collections</h4>
                <ul class="footer-links">
                    <li><a href="collections.php">Original Canvas Paintings</a></li>
                    <!-- <li><a href="collections.php">Custom Art Commissions</a></li> -->
                    <li><a href="collections.php">Geometric &amp; Triangle Series</a></li>
                    <li><a href="collections.php">Office &amp; Hotel Projects</a></li>
                </ul>
            </div>
            
            <div class="footer-col">
                <h4>Support &amp; Contact</h4>
                <ul class="footer-contact-info">
                    <li>
                        <span>📞</span>
                        <span><?php echo htmlspecialchars($phone_number); ?></span>
                    </li>
                    <li>
                        <span>✉️</span>
                        <span><?php echo htmlspecialchars(getSetting('contact_email', 'contact@rakeshverma.art')); ?></span>
                    </li>
                    <li>
                        <span>🏢</span>
                        <span><?php echo htmlspecialchars(getSetting('contact_address', 'Noida, UP')); ?></span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="container footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Rakesh Verma Art Studio. All rights reserved. Managed via custom Admin Panel.</p>
            <div class="footer-bottom-links">
                <a href="admin/login.php">Admin Login Dashboard</a>
            </div>
        </div>
    </footer>

    <!-- FLOATING ACTIONS FOR WHATSAPP AND MOBILE QUICK CALL -->
    <div class="floating-actions">
        <a href="<?php echo htmlspecialchars($whatsapp_url); ?>" target="_blank" class="floating-btn floating-whatsapp" aria-label="Chat on WhatsApp">
            <!-- Inline SVG for WhatsApp -->
            <svg width="28" height="28" fill="currentColor" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946C.06 5.348 5.397.01 12.008.01c3.202.001 6.212 1.246 8.477 3.514 2.266 2.268 3.507 5.28 3.505 8.484-.004 6.657-5.34 11.997-11.953 11.997-2.005-.001-3.973-.502-5.724-1.455L0 24zm6.59-4.846c1.6.95 3.188 1.449 4.825 1.451 5.436 0 9.86-4.37 9.864-9.799.002-2.63-1.023-5.101-2.885-6.968C16.632 1.97 14.162.946 11.533.946c-5.438 0-9.863 4.374-9.867 9.803-.001 1.73.457 3.419 1.32 4.922l-.994 3.633 3.765-.972zm11.96-7.393c-.302-.15-1.785-.875-2.056-.974-.27-.099-.467-.149-.662.15-.195.298-.754.943-.925 1.14-.17.199-.341.224-.643.075-.3-.15-1.269-.465-2.417-1.485-.89-.787-1.492-1.76-1.666-2.059-.173-.3-.018-.462.13-.61.135-.133.303-.35.454-.524.152-.175.202-.299.303-.499.102-.2.05-.375-.025-.524-.075-.15-.662-1.588-.908-2.179-.24-.575-.484-.497-.662-.505-.171-.007-.367-.008-.563-.008-.196 0-.517.073-.787.367-.27.293-1.03 1.002-1.03 2.44 0 1.439 1.048 2.827 1.195 3.023.147.195 2.062 3.12 4.996 4.367.698.297 1.242.474 1.667.607.7.221 1.338.19 1.843.114.562-.083 1.786-.726 2.037-1.428.25-.7.25-1.299.176-1.427-.075-.128-.27-.203-.572-.353z"/></svg>
        </a>
        <a href="tel:<?php echo htmlspecialchars($phone_number); ?>" class="floating-btn floating-call" aria-label="Call Us Now">
            📞
        </a>
    </div>

    <!-- Custom Frontend JS -->
    <script src="assets/js/main.js"></script>
</body>
</html>
