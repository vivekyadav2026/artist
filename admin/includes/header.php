<?php
session_start();

// Authentication guard
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../../db.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RV Art Studio Admin - Dashboard</title>
    <!-- Fonts & CSS -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>

    <!-- Sidebar Include -->
    <?php include __DIR__ . '/sidebar.php'; ?>

    <!-- Main Workspace wrapper -->
    <div class="main-content">
        
        <!-- Header Bar -->
        <header class="admin-header">
            <div class="page-title">
                <h1>Control Panel Dashboard</h1>
            </div>
            
            <div class="user-profile">
                <div class="user-info">
                    <h4><?php echo htmlspecialchars($_SESSION['admin_username']); ?></h4>
                    <span>Administrator</span>
                </div>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </header>
