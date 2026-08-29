<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

// Handle Settings Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    try {
        $pdo->beginTransaction();
        
        $settings_to_update = [
            'site_title', 'site_description', 'site_keywords',
            'contact_phone', 'contact_email', 'contact_whatsapp', 'contact_address',
            'hero_heading', 'hero_subheading', 'about_heading', 'about_description',
            'why_choose_us_headline', 'service_areas', 'google_map_iframe',
            'stat_artworks', 'stat_experience', 'stat_satisfied', 'stat_shows',
            'hero_canvas_title', 'hero_canvas_medium',
            'about_feature_1', 'about_feature_2', 'about_feature_3', 'about_feature_4',
            'why_collect_title_1', 'why_collect_desc_1',
            'why_collect_title_2', 'why_collect_desc_2',
            'why_collect_title_3', 'why_collect_desc_3',
            'skill_stat_paintings', 'skill_stat_sold', 'skill_stat_collectors',
            'instagram_reel_url'
        ];
        
        $up_stmt = $pdo->prepare("UPDATE settings SET setting_value = ? WHERE setting_key = ?");
        
        foreach ($settings_to_update as $key) {
            if (isset($_POST[$key])) {
                // If it's a map iframe, allow raw code but sanitize others if required
                // Standard text fields get sanitized
                if ($key === 'google_map_iframe') {
                    $value = $_POST[$key]; // Allow map HTML iframe
                } else {
                    $value = trim(strip_tags($_POST[$key]));
                }
                $up_stmt->execute([$value, $key]);
            }
        }
        
        $pdo->commit();
        $message = "Website settings updated successfully!";
        $message_type = "success";
    } catch (Exception $e) {
        $pdo->rollBack();
        $message = "Failed to save settings: " . $e->getMessage();
        $message_type = "error";
    }
}

// Handle Password/Username Changes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_credentials'])) {
    $new_user = trim(strip_tags($_POST['new_username']));
    $curr_pass = $_POST['current_password'];
    $new_pass = $_POST['new_password'];
    $conf_pass = $_POST['confirm_password'];
    
    if (empty($new_user) || empty($curr_pass)) {
        $message = "Username and Current Password are required.";
        $message_type = "error";
    } else {
        try {
            // Verify current password first
            $admin_id = $_SESSION['admin_id'];
            $chk_stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
            $chk_stmt->execute([$admin_id]);
            $hashed_pass = $chk_stmt->fetchColumn();
            
            if (!password_verify($curr_pass, $hashed_pass)) {
                $message = "Incorrect current password. Verification failed.";
                $message_type = "error";
            } else {
                // Check if username is being changed, and update
                $user_up_stmt = $pdo->prepare("UPDATE users SET username = ? WHERE id = ?");
                $user_up_stmt->execute([$new_user, $admin_id]);
                $_SESSION['admin_username'] = $new_user;
                
                // If password fields are filled, update password
                if (!empty($new_pass)) {
                    if ($new_pass !== $conf_pass) {
                        $message = "New password and confirmation password do not match.";
                        $message_type = "error";
                    } else {
                        $new_hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
                        $pass_up_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $pass_up_stmt->execute([$new_hashed_pass, $admin_id]);
                        
                        $message = "Username and Password credentials updated successfully!";
                        $message_type = "success";
                    }
                } else {
                    $message = "Username updated successfully!";
                    $message_type = "success";
                }
            }
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Reload Settings
$settings = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Fail silently
}
?>

<?php if ($message): ?>
    <div class="admin-alert admin-alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<div class="form-grid">
    <!-- LEFT COLUMN: GENERAL SETTINGS -->
    <div class="content-box" style="grid-column: span 2;">
        <div class="content-box-header">
            <h3>Website Content &amp; SEO Configuration</h3>
        </div>
        
        <form method="POST" action="settings.php">
            
            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin-bottom:15px; border-bottom:2px solid var(--primary); padding-bottom:5px;">🌐 Search Engine Optimization (SEO)</div>
            
            <div class="admin-form-group">
                <label for="site_title">Website Title (Browser Tab Heading)</label>
                <input type="text" id="site_title" name="site_title" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['site_title']) ? $settings['site_title'] : ''); ?>" required>
            </div>
            
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="site_description">Meta Description (For Search Snippets)</label>
                    <textarea id="site_description" name="site_description" class="admin-form-control" style="height:80px;"><?php echo htmlspecialchars(isset($settings['site_description']) ? $settings['site_description'] : ''); ?></textarea>
                </div>
                <div class="admin-form-group">
                    <label for="site_keywords">Meta Keywords (Comma Separated)</label>
                    <textarea id="site_keywords" name="site_keywords" class="admin-form-control" style="height:80px;"><?php echo htmlspecialchars(isset($settings['site_keywords']) ? $settings['site_keywords'] : ''); ?></textarea>
                </div>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">📞 Contact Information &amp; Channels</div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="contact_phone">Phone Number (For Display)</label>
                    <input type="text" id="contact_phone" name="contact_phone" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['contact_phone']) ? $settings['contact_phone'] : ''); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="contact_whatsapp">WhatsApp Number (e.g. 919876543210 - with country code, no + or spaces)</label>
                    <input type="text" id="contact_whatsapp" name="contact_whatsapp" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['contact_whatsapp']) ? $settings['contact_whatsapp'] : ''); ?>" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="contact_email">Email Address</label>
                    <input type="email" id="contact_email" name="contact_email" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['contact_email']) ? $settings['contact_email'] : ''); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="service_areas">Service Locations (Comma Separated)</label>
                    <input type="text" id="service_areas" name="service_areas" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['service_areas']) ? $settings['service_areas'] : ''); ?>" required>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="contact_address">Office Postal Address</label>
                <input type="text" id="contact_address" name="contact_address" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['contact_address']) ? $settings['contact_address'] : ''); ?>" required>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">🖌️ Hero &amp; About Us Text Content</div>

            <div class="admin-form-group">
                <label for="hero_heading">Hero Banner Heading</label>
                <input type="text" id="hero_heading" name="hero_heading" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['hero_heading']) ? $settings['hero_heading'] : ''); ?>" required>
            </div>
            
            <div class="admin-form-group">
                <label for="hero_subheading">Hero Banner Subheading</label>
                <input type="text" id="hero_subheading" name="hero_subheading" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['hero_subheading']) ? $settings['hero_subheading'] : ''); ?>" required>
            </div>

            <div class="admin-form-group">
                <label for="about_heading">About Section Main Heading</label>
                <input type="text" id="about_heading" name="about_heading" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['about_heading']) ? $settings['about_heading'] : ''); ?>" required>
            </div>

            <div class="admin-form-group">
                <label for="about_description">About Section Detailed Description</label>
                <textarea id="about_description" name="about_description" class="admin-form-control" style="height:120px;" required><?php echo htmlspecialchars(isset($settings['about_description']) ? $settings['about_description'] : ''); ?></textarea>
            </div>

            <div class="admin-form-group">
                <label for="why_choose_us_headline">Why Choose Us Section Headline</label>
                <input type="text" id="why_choose_us_headline" name="why_choose_us_headline" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_choose_us_headline']) ? $settings['why_choose_us_headline'] : ''); ?>" required>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">🗺️ Google Map Embed</div>

            <div class="admin-form-group">
                <label for="google_map_iframe">Google Maps Embed Code (HTML <code>&lt;iframe&gt;</code> tag)</label>
                <textarea id="google_map_iframe" name="google_map_iframe" class="admin-form-control" style="height:100px;" required><?php echo htmlspecialchars(isset($settings['google_map_iframe']) ? $settings['google_map_iframe'] : ''); ?></textarea>
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Go to Google Maps, click Share, select Embed map, and copy the full HTML iframe tag paste here.</small>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">📊 Key Achievements &amp; Statistics</div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="stat_artworks">Stat 1 (e.g. 250+)</label>
                    <input type="text" id="stat_artworks" name="stat_artworks" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['stat_artworks']) ? $settings['stat_artworks'] : '250+'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="stat_experience">Stat 2 (e.g. 10+)</label>
                    <input type="text" id="stat_experience" name="stat_experience" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['stat_experience']) ? $settings['stat_experience'] : '10+'); ?>" required>
                </div>
            </div>
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="stat_satisfied">Stat 3 (e.g. 98%)</label>
                    <input type="text" id="stat_satisfied" name="stat_satisfied" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['stat_satisfied']) ? $settings['stat_satisfied'] : '98%'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="stat_shows">Stat 4 (e.g. 15+)</label>
                    <input type="text" id="stat_shows" name="stat_shows" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['stat_shows']) ? $settings['stat_shows'] : '15+'); ?>" required>
                </div>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">🖌️ Achievements Counter Panel (Midnight Blue Box)</div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="skill_stat_paintings">Paintings Made (e.g. 250+)</label>
                    <input type="text" id="skill_stat_paintings" name="skill_stat_paintings" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['skill_stat_paintings']) ? $settings['skill_stat_paintings'] : '250+'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="skill_stat_sold">Canvas Sold (e.g. 120+)</label>
                    <input type="text" id="skill_stat_sold" name="skill_stat_sold" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['skill_stat_sold']) ? $settings['skill_stat_sold'] : '120+'); ?>" required>
                </div>
            </div>
            <div class="admin-form-group">
                <label for="skill_stat_collectors">Global Collectors (e.g. 35+)</label>
                <input type="text" id="skill_stat_collectors" name="skill_stat_collectors" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['skill_stat_collectors']) ? $settings['skill_stat_collectors'] : '35+'); ?>" required>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">✨ Homepage Highlights &amp; About Bullet Features</div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="hero_canvas_title">Hero Featured Canvas Title</label>
                    <input type="text" id="hero_canvas_title" name="hero_canvas_title" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['hero_canvas_title']) ? $settings['hero_canvas_title'] : 'Ember Triangles I'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="hero_canvas_medium">Hero Featured Canvas Medium</label>
                    <input type="text" id="hero_canvas_medium" name="hero_canvas_medium" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['hero_canvas_medium']) ? $settings['hero_canvas_medium'] : 'Acrylic on Linen Canvas'); ?>" required>
                </div>
            </div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="about_feature_1">About Bullet 1</label>
                    <input type="text" id="about_feature_1" name="about_feature_1" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['about_feature_1']) ? $settings['about_feature_1'] : 'Original Signed Artworks'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="about_feature_2">About Bullet 2</label>
                    <input type="text" id="about_feature_2" name="about_feature_2" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['about_feature_2']) ? $settings['about_feature_2'] : 'Archivable Linen Canvas'); ?>" required>
                </div>
            </div>
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="about_feature_3">About Bullet 3</label>
                    <input type="text" id="about_feature_3" name="about_feature_3" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['about_feature_3']) ? $settings['about_feature_3'] : 'Professional Acrylics & Oils'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="about_feature_4">About Bullet 4</label>
                    <input type="text" id="about_feature_4" name="about_feature_4" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['about_feature_4']) ? $settings['about_feature_4'] : 'Worldwide Secure Shipping'); ?>" required>
                </div>
            </div>

            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">🌟 Why Collect Art Features (Cards)</div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="why_collect_title_1">Card 1 Title</label>
                    <input type="text" id="why_collect_title_1" name="why_collect_title_1" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_title_1']) ? $settings['why_collect_title_1'] : 'Certified Originality'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="why_collect_desc_1">Card 1 Text</label>
                    <input type="text" id="why_collect_desc_1" name="why_collect_desc_1" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_desc_1']) ? $settings['why_collect_desc_1'] : 'Each original painting is hand-signed by Rakesh Verma and comes with a registered, stamped Certificate of Authenticity.'); ?>" required>
                </div>
            </div>
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="why_collect_title_2">Card 2 Title</label>
                    <input type="text" id="why_collect_title_2" name="why_collect_title_2" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_title_2']) ? $settings['why_collect_title_2'] : 'Bespoke Art Sizing'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="why_collect_desc_2">Card 2 Text</label>
                    <input type="text" id="why_collect_desc_2" name="why_collect_desc_2" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_desc_2']) ? $settings['why_collect_desc_2'] : 'Commission works designed specifically to complement the dimensions and color schemes of your residential or commercial walls.'); ?>" required>
                </div>
            </div>
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="why_collect_title_3">Card 3 Title</label>
                    <input type="text" id="why_collect_title_3" name="why_collect_title_3" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_title_3']) ? $settings['why_collect_title_3'] : 'Archival Quality Materials'); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="why_collect_desc_3">Card 3 Text</label>
                    <input type="text" id="why_collect_desc_3" name="why_collect_desc_3" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['why_collect_desc_3']) ? $settings['why_collect_desc_3'] : 'Created using premium-grade linen canvas and fade-resistant pigment acrylics to guarantee lifetime durability without color degradation.'); ?>" required>
                </div>
            </div>
            <div style="font-family:var(--font-heading); font-size:16px; font-weight:700; color:var(--primary); margin:25px 0 15px 0; border-bottom:2px solid var(--primary); padding-bottom:5px;">🎥 Studio Video &amp; Reels Integration</div>

            <div class="admin-form-group" style="margin-bottom: 30px;">
                <label for="instagram_reel_url">Instagram Video / Reel URL</label>
                <input type="url" id="instagram_reel_url" name="instagram_reel_url" class="admin-form-control" value="<?php echo htmlspecialchars(isset($settings['instagram_reel_url']) ? $settings['instagram_reel_url'] : ''); ?>" placeholder="e.g. https://www.instagram.com/reel/Ct5tUj8g2p3/">
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Paste any public Instagram Video, Reel, or Post link. The system will automatically convert it into a video embed frame.</small>
            </div>

            <button type="submit" name="save_settings" class="btn-add" style="border:none; cursor:pointer;">💾 Save Website Settings</button>
        </form>
    </div>

    <!-- RIGHT COLUMN: ADMIN CREDENTIALS -->
    <div class="content-box" style="grid-column: span 2;">
        <div class="content-box-header">
            <h3>Change Admin Login Credentials</h3>
        </div>
        
        <form method="POST" action="settings.php">
            <div class="admin-form-group">
                <label for="new_username">Administrator Username *</label>
                <input type="text" id="new_username" name="new_username" class="admin-form-control" value="<?php echo htmlspecialchars($_SESSION['admin_username']); ?>" required>
            </div>

            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="new_password">New Password (leave blank to keep current)</label>
                    <input type="password" id="new_password" name="new_password" class="admin-form-control" placeholder="Enter new password" autocomplete="new-password">
                </div>
                <div class="admin-form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="admin-form-control" placeholder="Confirm new password" autocomplete="new-password">
                </div>
            </div>

            <div class="admin-form-group" style="background:#fff7ed; padding:15px; border-radius:6px; border:1px solid #ffedd5; margin-top:20px;">
                <label for="current_password" style="color:#c2410c;">Current Password Verification *</label>
                <input type="password" id="current_password" name="current_password" class="admin-form-control" placeholder="Enter your current password to authorize changes" required autocomplete="current-password">
                <small style="color:#ca8a04; display:block; margin-top:5px;">Required whenever updating username or password credentials.</small>
            </div>

            <button type="submit" name="change_credentials" class="btn-add" style="border:none; cursor:pointer; background-color:#ca8a04;">🔒 Update Credentials</button>
        </form>
    </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>
