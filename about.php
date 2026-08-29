<?php
require_once __DIR__ . '/db.php';
include __DIR__ . '/includes/header.php';
?>

<!-- BREADCRUMB / HERO SUBPAGE BANNER -->
<section class="subpage-banner">
    <div class="container text-center">
        <span class="hero-tagline">Rakesh Verma</span>
        <h1>Biography &amp; Statement</h1>
        <p>Exploring structural depth, color transparent geometry, and modern interior layering.</p>
    </div>
</section>

<!-- MAIN BIOGRAPHY SECTION -->
<section class="py-section">
    <div class="container about-wrapper">
        <div class="about-collage">
            <div class="about-frame">
                <?php if (file_exists(__DIR__ . '/assets/images/rakesh-verma.jpg')): ?>
                    <img src="assets/images/rakesh-verma.jpg" alt="Rakesh Verma Profile Portrait">
                <?php elseif (file_exists(__DIR__ . '/assets/images/gallery-2.jpg')): ?>
                    <img src="assets/images/gallery-2.jpg" alt="Rakesh Verma Studio Profile Visual">
                <?php else: ?>
                    <div style="background:#132238; height:380px; display:flex; align-items:center; justify-content:center; color:white;">🎨 Artist Portrait</div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="about-info">
            <span class="badge" style="background-color: rgba(193, 68, 14, 0.08); color: var(--primary);">Creative Statement</span>
            <h2 style="margin-top: 10px;">"The Triangle as a Space of Tension and Rhythm"</h2>
            <p>My abstract canvas works focus on the geometric overlay of triangles. To me, the triangle is not just a shape—it is a node of tension, direction, and structural dialogue. By layering transparent acrylic glazes and stenciled pigment patterns, I build visual depth that challenges the flat canvas surface.</p>
            <p>Every piece is a balancing act of warm and cool tones, exploring how geometric overlays react to changing ambient light. I use high-grade linens, heavy-body acrylic pastes, and palette knives to give each painting a tactile, sculptural weight that fits harmoniously in contemporary architecture.</p>
            
            <div class="about-features" style="margin-top: 30px;">
                <div class="about-feature-item">
                    <span>✔</span> 10+ Years Dedicated Studio Work
                </div>
                <div class="about-feature-item">
                    <span>✔</span> Premium Canvas Curation
                </div>
                <div class="about-feature-item">
                    <span>✔</span> Custom Color Space Consults
                </div>
                <div class="about-feature-item">
                    <span>✔</span> Stretched or Rolled Canvas Delivery
                </div>
            </div>
        </div>
    </div>
</section>

<!-- EXHIBITIONS TIMELINE GRID -->
<section class="py-section bg-light" style="border-top: 1px solid var(--border); border-bottom: 1px solid var(--border);">
    <div class="container">
        <div class="section-header">
            <span class="badge">Career Highlights</span>
            <h2>Solo &amp; Group Exhibitions</h2>
            <p>Selected public showcasing, curated gallery displays, and fine art festival participations.</p>
        </div>
        
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-card-icon">🎨</div>
                <h3 style="font-size: 19px; margin-bottom: 6px;">"Geometric Shadows" Solo</h3>
                <span style="font-size: 11px; font-weight:700; color:var(--primary); text-transform:uppercase; display:block; margin-bottom: 10px;">Delhi Art Gallery — 2025</span>
                <p>A featured solo showcase displaying twenty-four large-format acrylic canvas works focusing on color transparency and stenciled triangle grids.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-card-icon">🏛️</div>
                <h3 style="font-size: 19px; margin-bottom: 6px;">Jehangir Annual Show</h3>
                <span style="font-size: 11px; font-weight:700; color:var(--primary); text-transform:uppercase; display:block; margin-bottom: 10px;">Jehangir Art Gallery, Mumbai — 2024</span>
                <p>Participated in the curated group exhibition presenting three core items from the 'Ember Triangles' textured acrylic series.</p>
            </div>
            
            <div class="feature-card">
                <div class="feature-card-icon">🌐</div>
                <h3 style="font-size: 19px; margin-bottom: 6px;">India Art Fair Feature</h3>
                <span style="font-size: 11px; font-weight:700; color:var(--primary); text-transform:uppercase; display:block; margin-bottom: 10px;">NSIC Grounds, Okhla — 2023</span>
                <p>Represented by ArtCuration Gallery showcasing modular triangular painting grids integrated with modern lounge room set mockups.</p>
            </div>
        </div>
    </div>
</section>

<?php
include __DIR__ . '/includes/footer.php';
?>
