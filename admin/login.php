<?php
session_start();

// Redirect to dashboard if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

require_once __DIR__ . '/../db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';
    
    if (empty($username) || empty($password)) {
        $error = "Please fill in both fields.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                // Set sessions
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid username or password.";
            }
        } catch (PDOException $e) {
            $error = "Database error. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RV Art Studio Admin - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700&family=Manrope:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="login-body">

<div class="login-card">
    <div class="login-logo">
        <div class="login-logo-icon">🔒</div>
        <h2>RV Art Panel</h2>
        <p>Enter your administrator credentials to login</p>
    </div>

    <?php if ($error): ?>
        <div class="admin-alert admin-alert-error">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="admin-form-group">
            <label for="username">Username</label>
            <input type="text" id="username" name="username" class="admin-form-control" placeholder="Enter username" required autofocus autocomplete="username">
        </div>

        <div class="admin-form-group">
            <label for="password">Password</label>
            <input type="password" id="password" name="password" class="admin-form-control" placeholder="Enter password" required autocomplete="current-password">
        </div>

        <button type="submit" class="btn-login">Login to Dashboard</button>
    </form>
</div>

</body>
</html>
