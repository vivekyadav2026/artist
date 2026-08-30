<?php
session_start();

// If config.php already exists and database is already installed, verify if we need to block reinstalling
$config_file = __DIR__ . '/config.php';
$already_installed = false;
if (file_exists($config_file)) {
    // We can try to include it and see if connection works
    @include $config_file;
    if (defined('DB_HOST') && defined('DB_NAME') && defined('DB_USER')) {
        $already_installed = true;
    }
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['install'])) {
    $db_host = trim($_POST['db_host']);
    $db_user = trim($_POST['db_user']);
    $db_pass = $_POST['db_pass'];
    $db_name = trim($_POST['db_name']);
    
    $admin_user = trim($_POST['admin_user']);
    $admin_pass = $_POST['admin_pass'];
    
    if (empty($db_host) || empty($db_user) || empty($db_name) || empty($admin_user) || empty($admin_pass)) {
        $error = "All fields except Database Password are required.";
    } else {
        try {
            // Connect without DB first to create database
            $pdo = new PDO("mysql:host=$db_host;charset=utf8mb4", $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Create Database
            $pdo->exec("CREATE DATABASE IF NOT EXISTS `$db_name` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            // Reconnect with DB
            $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);
            
            // Create Users Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `users` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `username` VARCHAR(50) NOT NULL UNIQUE,
                `password` VARCHAR(255) NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // Create Settings Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `settings` (
                `setting_key` VARCHAR(100) PRIMARY KEY,
                `setting_value` TEXT,
                `display_name` VARCHAR(255) NOT NULL,
                `group_name` VARCHAR(50) NOT NULL
            ) ENGINE=InnoDB;");
            
            // Create Services Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `services` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `description` TEXT NOT NULL,
                `image_path` VARCHAR(255) NOT NULL,
                `price_range` VARCHAR(100) DEFAULT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // Create Gallery Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `gallery` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `title` VARCHAR(255) NOT NULL,
                `category` VARCHAR(100) NOT NULL,
                `image_path` VARCHAR(255) NOT NULL,
                `medium` VARCHAR(150) DEFAULT NULL,
                `size` VARCHAR(100) DEFAULT NULL,
                `year` VARCHAR(50) DEFAULT NULL,
                `price` VARCHAR(100) DEFAULT NULL,
                `available` VARCHAR(50) DEFAULT 'Available',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // Create Enquiries Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `enquiries` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `phone` VARCHAR(20) NOT NULL,
                `email` VARCHAR(100) DEFAULT NULL,
                `service` VARCHAR(150) NOT NULL,
                `message` TEXT DEFAULT NULL,
                `status` VARCHAR(50) DEFAULT 'Pending',
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // Create Testimonials Table
            $pdo->exec("CREATE TABLE IF NOT EXISTS `testimonials` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `location` VARCHAR(100) DEFAULT NULL,
                `rating` INT DEFAULT 5,
                `review` TEXT NOT NULL,
                `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB;");
            
            // Insert Admin User (clear existing first if re-running)
            $pdo->exec("TRUNCATE TABLE `users`");
            $hashed_pass = password_hash($admin_pass, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO `users` (username, password) VALUES (?, ?)");
            $stmt->execute([$admin_user, $hashed_pass]);
            
            // Seed Default Settings
            $default_settings = [
                ['site_title', 'Rakesh Verma - Contemporary Abstract Artist', 'Site Title', 'seo'],
                ['site_description', 'Explore original hand-signed abstract paintings, geometric triangle series, and fine art collections by contemporary artist Rakesh Verma.', 'Site Meta Description', 'seo'],
                ['site_keywords', 'abstract art, geometric paintings, contemporary artist, triangle painting series, original canvas art, Rakesh Verma', 'Site Meta Keywords', 'seo'],
                ['contact_phone', '+91 78893 50684', 'Contact Phone', 'contact'],
                ['contact_email', 'contact@rakeshverma.art', 'Contact Email', 'contact'],
                ['contact_whatsapp', '917889350684', 'WhatsApp Number (with Country Code, no spaces/plus)', 'contact'],
                ['contact_address', 'Jawahar Navodaya Vidyalaya Road, Ghattiya, District Ujjain, Madhya Pradesh, 456550', 'Office Address', 'contact'],
                ['hero_heading', 'Transforming Spaces with Vibrant Geometry.', 'Hero Heading', 'about'],
                ['hero_subheading', 'Explore original hand-signed acrylic canvas works and geometric triangle series exploring depths, color transparency, and layered rhythm by Rakesh Verma.', 'Hero Subheading', 'about'],
                ['about_heading', 'About the Artist — Rakesh Verma', 'About Section Heading', 'about'],
                ['about_description', 'Rakesh Verma is a contemporary abstract painter exploring geometric rhythms, color overlays, and layered textures. Working primarily with acrylics, palette knives, and stencils on high-grade canvas, Rakesh\'s signature triangle series explores visual depth and structural patterns. His works reside in private collections and modern interiors.', 'About Section Description', 'about'],
                ['why_choose_us_headline', 'Why Collectors Choose Rakesh Verma Art', 'Why Choose Us Headline', 'about'],
                ['service_areas', 'Mumbai, Delhi NCR, Bengaluru, Hyderabad, Chennai, Kolkata, Pune, All India Delivery', 'Service Areas (Comma separated)', 'contact'],
                ['google_map_iframe', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3667.669864278484!2d75.7678971!3d23.2728033!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3963f4581c7fffff%3A0x6b772bb2b9db4db6!2sGhattiya%2C%20Madhya%20Pradesh!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'Google Map Embed HTML', 'contact']
            ];
            
            $pdo->exec("TRUNCATE TABLE `settings`");
            $set_stmt = $pdo->prepare("INSERT INTO `settings` (setting_key, setting_value, display_name, group_name) VALUES (?, ?, ?, ?)");
            foreach ($default_settings as $setting) {
                $set_stmt->execute($setting);
            }
            
            // Seed Default Services
            $pdo->exec("TRUNCATE TABLE `services`");
            $srv_stmt = $pdo->prepare("INSERT INTO `services` (title, description, image_path, price_range) VALUES (?, ?, ?, ?)");
            $srv_stmt->execute([
                'Original Canvas Paintings',
                'Explore signed original canvas paintings capturing the dimensions of time. Each abstract artwork serves as a visual bridge connecting the memories of the past, the raw presence of the present, and the vision of the future.',
                'assets/images/abstract-1.jpg',
                'On Request'
            ]);
            $srv_stmt->execute([
                'Custom Art Commissions',
                'Commission a bespoke abstract painting tailored to the size, color scheme, and mood of your home or corporate office walls.',
                'assets/images/abstract-2.jpg',
                'Based on Size'
            ]);
            $srv_stmt->execute([
                'Geometric & Triangle Series',
                'Explore the geometric triangle overlay prints and original acrylic works that play with light, transparency, and color tones.',
                'assets/images/abstract-3.jpg',
                'Starting ₹18,000'
            ]);
            $srv_stmt->execute([
                'Office & Hotel Projects',
                'Custom abstract series and giant feature canvases designed specifically to enhance commercial lobbies, hotel rooms, and corporate office boards.',
                'assets/images/abstract-4.jpg',
                'Bulk Estimation'
            ]);
            
            // Seed Default Testimonials
            $pdo->exec("TRUNCATE TABLE `testimonials`");
            $test_stmt = $pdo->prepare("INSERT INTO `testimonials` (name, location, rating, review) VALUES (?, ?, ?, ?)");
            $test_stmt->execute([
                'Aditya Sharma',
                'Noida, Sec 78',
                5,
                'The abstract paintings we acquired are the focal point of our living room. Rakesh\'s geometric design and color tones look absolutely premium and match our interiors perfectly!'
            ]);
            $test_stmt->execute([
                'Meenakshi Rao',
                'Gurgaon, Phase 3',
                5,
                'We commissioned a custom large canvas abstract artwork for our Gurgaon lobby. Rakesh was extremely collaborative, matching our color palette exactly. It was delivered on time and perfectly stretched.'
            ]);
            $test_stmt->execute([
                'Rakesh Singhal',
                'Delhi, Vasant Kunj',
                4,
                'Rakesh Verma\'s geometric series is a masterclass in modern abstract art. The transparency of layers and nodes of tension are mesmerizing. We are proud to have three of his works in our collection.'
            ]);
            
            // Seed Default Gallery
            $pdo->exec("TRUNCATE TABLE `gallery`");
            $gal_stmt = $pdo->prepare("INSERT INTO `gallery` (title, category, image_path, medium, size, year, price, available) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $gal_stmt->execute([
                'Ember Triangles (Red Acrylic Series)',
                'Texture',
                'assets/images/gallery-1.jpg',
                'Acrylic and Paste on Linen Canvas',
                '36 x 48 inches',
                '2025',
                '₹ 1,50,000',
                'Available'
            ]);
            $gal_stmt->execute([
                'Forest Canopy (Teal Geometric Stencil)',
                'Interior',
                'assets/images/gallery-2.jpg',
                'Acrylic Glaze on Canvas',
                '30 x 40 inches',
                '2024',
                '₹ 1,15,000',
                'Available'
            ]);
            $gal_stmt->execute([
                'Deep Oceans (Blue Geometric Rhythm)',
                'Interior',
                'assets/images/gallery-3.jpg',
                'Acrylic and Pigments on Linen',
                '48 x 48 inches',
                '2025',
                '₹ 1,80,000',
                'Available'
            ]);
            $gal_stmt->execute([
                'Prism Echoes (Warm/Cool Balance)',
                'Texture',
                'assets/images/gallery-4.jpg',
                'Heavy Gel and Acrylic on Canvas',
                '24 x 36 inches',
                '2025',
                '₹ 95,000',
                'Available'
            ]);
            $gal_stmt->execute([
                'Ember Triangles II',
                'Exterior',
                'assets/images/gallery-5.jpg',
                'Acrylic on Linen Canvas',
                '40 x 50 inches',
                '2026',
                '₹ 2,10,000',
                'Available'
            ]);
            $gal_stmt->execute([
                'Forest Canopy II',
                'Commercial',
                'assets/images/gallery-6.jpg',
                'Giclée Archival Abstract Print',
                '18 x 24 inches',
                '2026',
                '₹ 28,000',
                'Available'
            ]);

            // Write config.php
            $config_content = "<?php\n";
            $config_content .= "// Database Settings - Auto-generated by install.php\n";
            $config_content .= "define('DB_HOST', " . var_export($db_host, true) . ");\n";
            $config_content .= "define('DB_USER', " . var_export($db_user, true) . ");\n";
            $config_content .= "define('DB_PASS', " . var_export($db_pass, true) . ");\n";
            $config_content .= "define('DB_NAME', " . var_export($db_name, true) . ");\n";
            
            if (file_put_contents($config_file, $config_content) === false) {
                throw new Exception("Unable to write config.php. Please check directory write permissions.");
            }
            
            // Create uploads directories if not exist
            if (!is_dir(__DIR__ . '/uploads')) {
                mkdir(__DIR__ . '/uploads', 0777, true);
            }
            if (!is_dir(__DIR__ . '/uploads/services')) {
                mkdir(__DIR__ . '/uploads/services', 0777, true);
            }
            if (!is_dir(__DIR__ . '/uploads/gallery')) {
                mkdir(__DIR__ . '/uploads/gallery', 0777, true);
            }
            if (!is_dir(__DIR__ . '/assets/images')) {
                mkdir(__DIR__ . '/assets/images', 0777, true);
            }
            
            $success = "Database installation completed successfully! 'config.php' has been created.";
            $_SESSION['installed'] = true;
            
        } catch (PDOException $e) {
            $error = "Database Error: " . $e->getMessage();
        } catch (Exception $e) {
            $error = "System Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rakesh Verma Art Studio - Database Installer</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Manrope:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #056e41;
            --primary-dark: #034f2e;
            --accent: #f59e0b;
            --background: #f8fafc;
            --card-bg: #ffffff;
            --text-main: #1e293b;
            --text-muted: #64748b;
            --border: #e2e8f0;
        }
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        body {
            font-family: 'Manrope', sans-serif;
            background: var(--background);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .installer-card {
            background: var(--card-bg);
            border-radius: 12px;
            box-shadow: 0 10px 25px -5px rgba(0,0,0,0.05), 0 8px 10px -6px rgba(0,0,0,0.05);
            width: 100%;
            max-width: 580px;
            padding: 40px;
            border: 1px solid var(--border);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .header h1 {
            font-family: 'Outfit', sans-serif;
            font-size: 28px;
            color: var(--primary);
            font-weight: 700;
            margin-bottom: 8px;
        }
        .header p {
            color: var(--text-muted);
            font-size: 15px;
        }
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            font-size: 14.5px;
            line-height: 1.5;
        }
        .alert-error {
            background: #fef2f2;
            color: #b91c1c;
            border: 1px solid #fee2e2;
        }
        .alert-success {
            background: #f0fdf4;
            color: #15803d;
            border: 1px solid #dcfce7;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 16px;
        }
        label {
            display: block;
            font-size: 13.5px;
            font-weight: 600;
            color: var(--text-main);
            margin-bottom: 8px;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid var(--border);
            border-radius: 6px;
            font-family: 'Manrope', sans-serif;
            font-size: 14.5px;
            color: var(--text-main);
            transition: all 0.2s ease;
        }
        input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(5, 110, 65, 0.15);
        }
        .section-title {
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin: 25px 0 15px 0;
            padding-bottom: 6px;
            border-bottom: 1px solid var(--border);
        }
        button.btn-submit {
            display: block;
            width: 100%;
            background: var(--primary);
            color: white;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            padding: 14px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
            margin-top: 25px;
        }
        button.btn-submit:hover {
            background: var(--primary-dark);
        }
        .btn-success-link {
            display: inline-block;
            text-align: center;
            width: 100%;
            background: #10b981;
            color: white;
            text-decoration: none;
            font-family: 'Outfit', sans-serif;
            font-size: 16px;
            font-weight: 600;
            padding: 14px;
            border-radius: 6px;
            margin-top: 15px;
            transition: background 0.2s ease;
        }
        .btn-success-link:hover {
            background: #059669;
        }
    </style>
</head>
<body>

<div class="installer-card">
    <div class="header">
        <h1>Rakesh Verma Art Studio Setup Wizard</h1>
        <p>Configure your database and administrator account to proceed</p>
    </div>

    <?php if ($already_installed && !isset($_SESSION['installed'])): ?>
        <div class="alert alert-success" style="text-align: center;">
            <strong>Warning:</strong> A <code>config.php</code> file already exists. Re-running the installation will overwrite database tables and reset settings!
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            <strong>Success!</strong> <?php echo $success; ?>
            <div style="margin-top: 10px;">You can now browse the site and access the admin panel with your administrator credentials.</div>
        </div>
        <a href="index.php" class="btn-success-link">Go to Live Website</a>
        <a href="admin/login.php" class="btn-success-link" style="background: var(--primary); margin-top: 10px;">Go to Admin Panel</a>
    <?php else: ?>

        <form method="POST" action="">
            <div class="section-title">Database Settings</div>
            
            <div class="form-row">
                <div class="form-group">
                    <label for="db_host">Database Host</label>
                    <input type="text" id="db_host" name="db_host" value="localhost" required>
                </div>
                <div class="form-group">
                    <label for="db_name">Database Name</label>
                    <input type="text" id="db_name" name="db_name" value="painting_db" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="db_user">Database User</label>
                    <input type="text" id="db_user" name="db_user" value="root" required>
                </div>
                <div class="form-group">
                    <label for="db_pass">Database Password</label>
                    <input type="text" id="db_pass" name="db_pass" placeholder="Leave blank for root defaults">
                </div>
            </div>

            <div class="section-title">Admin Account Credentials</div>

            <div class="form-row">
                <div class="form-group">
                    <label for="admin_user">Username</label>
                    <input type="text" id="admin_user" name="admin_user" value="admin" required>
                </div>
                <div class="form-group">
                    <label for="admin_pass">Password</label>
                    <input type="text" id="admin_pass" name="admin_pass" value="admin123" required>
                </div>
            </div>

            <button type="submit" name="install" class="btn-submit">Install Database &amp; Configure</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
