<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Add Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $title = trim(strip_tags($_POST['title']));
    $url = trim(strip_tags($_POST['url']));
    
    if (empty($title) || empty($url)) {
        $message = "Please provide both video title and Instagram URL.";
        $message_type = "error";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $message = "Please provide a valid Instagram URL.";
        $message_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO instagram_videos (title, url) VALUES (?, ?)");
            $stmt->execute([$title, $url]);
            
            $message = "Instagram video added successfully!";
            $message_type = "success";
            $action = 'list';
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Handle Edit Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'edit' && $id > 0) {
    $title = trim(strip_tags($_POST['title']));
    $url = trim(strip_tags($_POST['url']));
    
    if (empty($title) || empty($url)) {
        $message = "Please provide both video title and Instagram URL.";
        $message_type = "error";
    } elseif (!filter_var($url, FILTER_VALIDATE_URL)) {
        $message = "Please provide a valid Instagram URL.";
        $message_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE instagram_videos SET title = ?, url = ? WHERE id = ?");
            $stmt->execute([$title, $url, $id]);
            
            $message = "Instagram video updated successfully!";
            $message_type = "success";
            $action = 'list';
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Handle Delete Action
if ($action === 'delete' && $id > 0) {
    try {
        $del_stmt = $pdo->prepare("DELETE FROM instagram_videos WHERE id = ?");
        $del_stmt->execute([$id]);
        
        $message = "Instagram video deleted successfully.";
        $message_type = "success";
        $action = 'list';
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
}
?>

<div class="admin-header-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Manage Instagram Reels / Videos</h2>
    <?php if ($action === 'list'): ?>
        <a href="instagram.php?action=add" class="btn-add">➕ Add Instagram Video</a>
    <?php else: ?>
        <a href="instagram.php" class="btn-action view">⬅️ Back to Video List</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="admin-alert admin-alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- LIST TEMPLATE -->
<?php if ($action === 'list'): ?>
    <div class="content-box">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM instagram_videos ORDER BY id DESC");
            $videos = $stmt->fetchAll();
        } catch (Exception $e) {
            $videos = [];
        }
        ?>

        <?php if (empty($videos)): ?>
            <p style="text-align: center; padding: 30px; color: var(--text-muted);">No Instagram videos added yet. Click 'Add Instagram Video' to start.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Video Title</th>
                        <th>Instagram URL</th>
                        <th>Preview Embed Link</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($videos as $vid): 
                        $embed_url = '';
                        if (preg_match('/(?:p|reel|tv)\/([a-zA-Z0-9-_]+)/', $vid['url'], $matches)) {
                            $embed_url = "https://www.instagram.com/p/" . $matches[1] . "/embed/";
                        }
                    ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($vid['title']); ?></strong></td>
                            <td><a href="<?php echo htmlspecialchars($vid['url']); ?>" target="_blank" style="color:var(--primary); text-decoration:underline; font-size:13px;"><?php echo htmlspecialchars($vid['url']); ?></a></td>
                            <td>
                                <?php if (!empty($embed_url)): ?>
                                    <code><?php echo htmlspecialchars($embed_url); ?></code>
                                <?php else: ?>
                                    <span style="color:#ef4444; font-size:12px;">Invalid Shortcode URL</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="instagram.php?action=edit&id=<?php echo $vid['id']; ?>" class="btn-action edit">Edit</a>
                                    <a href="instagram.php?action=delete&id=<?php echo $vid['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this Instagram video link?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<!-- ADD TEMPLATE -->
<?php elseif ($action === 'add'): ?>
    <div class="content-box">
        <form method="POST" action="instagram.php?action=add">
            <div class="admin-form-group">
                <label for="title">Video / Post Title *</label>
                <input type="text" id="title" name="title" class="admin-form-control" placeholder="e.g. Acrylic Pouring Process Clip" required>
            </div>
            
            <div class="admin-form-group">
                <label for="url">Instagram Reel / Post URL *</label>
                <input type="url" id="url" name="url" class="admin-form-control" placeholder="e.g. https://www.instagram.com/reel/Ct5tUj8g2p3/" required>
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Paste the link to your Instagram Reel or Post. Make sure the account is public.</small>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Add Video Link</button>
        </form>
    </div>

<!-- EDIT TEMPLATE -->
<?php elseif ($action === 'edit' && $id > 0): 
    $stmt = $pdo->prepare("SELECT * FROM instagram_videos WHERE id = ?");
    $stmt->execute([$id]);
    $vid = $stmt->fetch();
    if (!$vid) {
        echo "<div class='admin-alert admin-alert-error'>Instagram video link not found.</div>";
    } else {
?>
    <div class="content-box">
        <form method="POST" action="instagram.php?action=edit&id=<?php echo $id; ?>">
            <div class="admin-form-group">
                <label for="title">Video / Post Title *</label>
                <input type="text" id="title" name="title" class="admin-form-control" value="<?php echo htmlspecialchars($vid['title']); ?>" required>
            </div>
            
            <div class="admin-form-group">
                <label for="url">Instagram Reel / Post URL *</label>
                <input type="url" id="url" name="url" class="admin-form-control" value="<?php echo htmlspecialchars($vid['url']); ?>" required>
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Paste the link to your Instagram Reel or Post. Make sure the account is public.</small>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Save Changes</button>
        </form>
    </div>
<?php } endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
