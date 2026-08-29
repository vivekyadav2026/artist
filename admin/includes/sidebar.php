<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">🎨</div>
        <h2>RV Art Admin</h2>
    </div>
    
    <ul class="sidebar-menu">
        <li class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>">
            <a href="index.php">
                <span>📊</span> Dashboard Home
            </a>
        </li>
        <li class="<?php echo ($current_page == 'services.php') ? 'active' : ''; ?>">
            <a href="services.php">
                <span>🖌️</span> Art Collections
            </a>
        </li>
        <li class="<?php echo ($current_page == 'gallery.php') ? 'active' : ''; ?>">
            <a href="gallery.php">
                <span>🖼️</span> Artwork Portfolio
            </a>
        </li>
        <li class="<?php echo ($current_page == 'enquiries.php') ? 'active' : ''; ?>">
            <a href="enquiries.php">
                <span>📋</span> Enquiries Lead
            </a>
        </li>
        <li class="<?php echo ($current_page == 'testimonials.php') ? 'active' : ''; ?>">
            <a href="testimonials.php">
                <span>⭐</span> Reviews
            </a>
        </li>
        <li class="<?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
            <a href="settings.php">
                <span>⚙️</span> Site Settings &amp; SEO
            </a>
        </li>
        <li>
            <a href="../index.php" target="_blank">
                <span>🌐</span> Visit Front Site
            </a>
        </li>
    </ul>
    
    <div class="sidebar-footer">
        <p>&copy; <?php echo date('Y'); ?> RV Art Panel</p>
    </div>
</div>
