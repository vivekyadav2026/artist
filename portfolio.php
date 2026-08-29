<?php
require_once __DIR__ . '/db.php';

// Fetch all gallery items
try {
    $gallery_stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
    $gallery = $gallery_stmt->fetchAll();
} catch (Exception $e) {
    $gallery = [];
}

include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB / HERO SUBPAGE BANNER -->
<section class="subpage-banner">
    <div class="container text-center">
        <span class="hero-tagline">Exhibition Portfolio</span>
        <h1>Artwork Portfolio Gallery</h1>
        <p>Explore original triangular overlay designs, structured glaze scrapings, and textured canvases.</p>
    </div>
</section>

<!-- PROJECT WORK GALLERY SECTION -->
<section class="py-section">
    <div class="container">
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
                <p style="grid-column: span 3; text-align: center; color: var(--text-light); padding: 40px 0;">No project gallery images uploaded yet.</p>
            <?php else: ?>
                <?php foreach($gallery as $item): ?>
                    <div class="gallery-item" data-category="<?php echo htmlspecialchars(strtolower($item['category'])); ?>" style="padding: 10px; border-radius: var(--radius-sm); border: 1px solid var(--border); background: white;">
                        <div class="gallery-item-inner" style="position: relative; overflow: hidden; aspect-ratio: 4/3; border-radius: 4px;">
                            <?php if (!empty($item['image_path']) && file_exists(__DIR__ . '/' . $item['image_path'])): ?>
                                <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" style="width:100%; height:100%; object-fit:cover; display:block;">
                            <?php else: ?>
                                <div class="placeholder-svg-bg" style="background: linear-gradient(135deg, #1e293b, #334155); height:100%; display:flex; align-items:center; justify-content:center; color:white;">
                                    <span>📸 <?php echo htmlspecialchars($item['title']); ?></span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="gallery-info" style="padding: 12px 2px 2px 2px;">
                            <h4 style="font-size: 16px; margin: 0 0 6px 0; font-family: var(--font-heading); color: var(--text-ink); line-height: 1.3;"><?php echo htmlspecialchars($item['title']); ?></h4>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 8px;">
                                <span style="font-size: 11px; color: var(--text-muted); font-weight: 600; text-transform: uppercase; letter-spacing: 0.03em;">
                                    <?php 
                                        if (strtolower($item['category']) === 'interior') echo 'Teal & Cool Tones';
                                        elseif (strtolower($item['category']) === 'exterior') echo 'Orange & Red Tones';
                                        elseif (strtolower($item['category']) === 'texture') echo 'Textured Acrylics';
                                        elseif (strtolower($item['category']) === 'commercial') echo 'Canvas Prints';
                                        else echo htmlspecialchars($item['category']);
                                    ?>
                                </span>
                                <a href="contact.php?artwork=<?php echo urlencode($item['title']); ?>" style="font-size: 12px; font-weight: 700; color: var(--primary); text-decoration: none; display: flex; align-items: center;">Inquire &rarr;</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
