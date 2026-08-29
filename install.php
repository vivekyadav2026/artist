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
                ['site_title', 'ColorLux Painters - Professional Painting Services', 'Site Title', 'seo'],
                ['site_description', 'Get premium interior, exterior, wall texture, waterproofing, and commercial painting services from experienced professional painters. 100% dust-free & high-quality finishes.', 'Site Meta Description', 'seo'],
                ['site_keywords', 'home painting services, house painters near me, wall texture painting, interior house painting, waterproofing painters', 'Site Meta Keywords', 'seo'],
                ['contact_phone', '+91 98765 43210', 'Contact Phone', 'contact'],
                ['contact_email', 'contact@colorluxpainters.com', 'Contact Email', 'contact'],
                ['contact_whatsapp', '919876543210', 'WhatsApp Number (with Country Code, no spaces/plus)', 'contact'],
                ['contact_address', '402, 4th Floor, Sector 62, Noida, Uttar Pradesh, 201301', 'Office Address', 'contact'],
                ['hero_heading', 'Flawless Paint. Premium Finish. Zero Stress.', 'Hero Heading', 'about'],
                ['hero_subheading', 'Bring your walls to life with Delhi NCR\'s most trusted home & commercial painting team. Get dust-free execution and a 1-year service warranty.', 'Hero Subheading', 'about'],
                ['about_heading', 'We Don\'t Just Paint Walls. We Paint Homes.', 'About Section Heading', 'about'],
                ['about_description', 'ColorLux Painters is Delhi NCR\'s leading professional painting solutions provider. For over a decade, we have helped homeowners and corporate spaces reimagine their environments. Our crew is trained in high-grade finishes, clean work practices, and modern techniques like designer wall textures and specialized wood styling.', 'About Section Description', 'about'],
                ['why_choose_us_headline', 'Why Property Owners Choose ColorLux', 'Why Choose Us Headline', 'about'],
                ['service_areas', 'Delhi, Noida, Greater Noida, Gurgaon, Ghaziabad, Faridabad', 'Service Areas (Comma separated)', 'contact'],
                ['google_map_iframe', '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d224345.97130282855!2d77.0688975924748!3d28.52728033785461!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x390cfd5b347eb62d%3A0x52c2b7494e204dce!2sNew%20Delhi%2C%20Delhi!5e0!3m2!1sen!2sin!4v1700000000000!5m2!1sen!2sin" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>', 'Google Map Embed HTML', 'contact']
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
                'Premium Interior Painting',
                'Give your living spaces a luxury touch. We offer emulsion painting, plastic paint finishes, and custom color consultations with zero-odor formulations.',
                'assets/images/service-interior.jpg',
                '₹12/sq.ft onwards'
            ]);
            $srv_stmt->execute([
                'Weather-Proof Exterior Painting',
                'Protect your house walls from heavy monsoon rains and scorching sun. We use elastic, anti-peeling paint shield technology with up to 7 years warranty.',
                'assets/images/service-exterior.jpg',
                '₹15/sq.ft onwards'
            ]);
            $srv_stmt->execute([
                'Designer Texture Walls',
                'Transform your main drawing room or master bedroom wall into a work of art. Stencils, metallic finishes, rustic spatulas, and modern clay finishes.',
                'assets/images/service-texture.jpg',
                '₹45/sq.ft onwards'
            ]);
            $srv_stmt->execute([
                'Waterproofing & Damp Repair',
                'Solve moisture, chalking, and dampness permanently. We perform base cement grouting, wall crack filling, and silicon sealant rubber-coating.',
                'assets/images/service-waterproofing.jpg',
                'On Inspection'
            ]);
            
            // Seed Default Testimonials
            $pdo->exec("TRUNCATE TABLE `testimonials`");
            $test_stmt = $pdo->prepare("INSERT INTO `testimonials` (name, location, rating, review) VALUES (?, ?, ?, ?)");
            $test_stmt->execute([
                'Aditya Sharma',
                'Noida, Sec 78',
                5,
                'The team finished our entire 3BHK flat in exactly 5 days. Minimal dust, quick cleanup, and the texture wall in our dining area looks absolutely premium!'
            ]);
            $test_stmt->execute([
                'Meenakshi Rao',
                'Gurgaon, Phase 3',
                5,
                'I was worried about painting odors due to my young kids, but their low-VOC paints made it so easy. The painters were polite and highly professional.'
            ]);
            $test_stmt->execute([
                'Rakesh Singhal',
                'Delhi, Vasant Kunj',
                4,
                'ColorLux did our commercial office exterior painting. Outstanding durability and neat detailing. Highly recommended painting contractor.'
            ]);
            
            // Seed Default Gallery
            $pdo->exec("TRUNCATE TABLE `gallery`");
            $gal_stmt = $pdo->prepare("INSERT INTO `gallery` (title, category, image_path) VALUES (?, ?, ?)");
            $gal_stmt->execute(['Elegant Living Room Emulsion', 'Interior', 'assets/images/gallery-1.jpg']);
            $gal_stmt->execute(['Modern High-Sheen Bedroom', 'Interior', 'assets/images/gallery-2.jpg']);
            $gal_stmt->execute(['Textured Feature Wall', 'Texture', 'assets/images/gallery-3.jpg']);
            $gal_stmt->execute(['Water-Resistant Building Exterior', 'Exterior', 'assets/images/gallery-4.jpg']);
            $gal_stmt->execute(['Metallic Accent Ceiling Finish', 'Texture', 'assets/images/gallery-5.jpg']);
            $gal_stmt->execute(['Corporate Cabin Matte Finish', 'Commercial', 'assets/images/gallery-6.jpg']);

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
    <title>ColorLux - Database Installer</title>
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
        <h1>ColorLux Setup Wizard</h1>
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
