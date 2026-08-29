<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle File Upload Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $title = trim(strip_tags($_POST['title']));
    $category = trim(strip_tags($_POST['category']));
    
    if (empty($title) || empty($category)) {
        $message = "Please provide both project title and category.";
        $message_type = "error";
    } elseif (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
        $message = "Please select a valid image file to upload.";
        $message_type = "error";
    } else {
        $file_tmp = $_FILES['image']['tmp_name'];
        $file_name = $_FILES['image']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_ext, $allowed_extensions)) {
            $message = "Invalid file extension. Allowed extensions are: " . implode(', ', $allowed_extensions);
            $message_type = "error";
        } else {
            $upload_dir = '../uploads/gallery/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $new_file_name = uniqid('project_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;
            
            if (move_uploaded_file($file_tmp, $dest_path)) {
                try {
                    $db_path = 'uploads/gallery/' . $new_file_name;
                    $stmt = $pdo->prepare("INSERT INTO gallery (title, category, image_path) VALUES (?, ?, ?)");
                    $stmt->execute([$title, $category, $db_path]);
                    
                    $message = "Project image uploaded and added to gallery successfully!";
                    $message_type = "success";
                    $action = 'list';
                } catch (PDOException $e) {
                    $message = "Database insertion failed: " . $e->getMessage();
                    $message_type = "error";
                }
            } else {
                $message = "Failed to move file to the target uploads directory.";
                $message_type = "error";
            }
        }
    }
}

// Handle Delete Action
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT image_path FROM gallery WHERE id = ?");
        $stmt->execute([$id]);
        $image_path = $stmt->fetchColumn();
        
        if ($image_path && file_exists('../' . $image_path)) {
            @unlink('../' . $image_path);
        }
        
        $del_stmt = $pdo->prepare("DELETE FROM gallery WHERE id = ?");
        $del_stmt->execute([$id]);
        
        $message = "Gallery item deleted successfully.";
        $message_type = "success";
        $action = 'list';
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
}
?>

<div class="admin-header-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Manage Project Gallery</h2>
    <?php if ($action === 'list'): ?>
        <a href="gallery.php?action=add" class="btn-add">➕ Upload New Project Image</a>
    <?php else: ?>
        <a href="gallery.php" class="btn-action view">⬅️ Back to Gallery List</a>
    <?php endif; ?>
</div>

<?php if ($message): ?>
    <div class="admin-alert admin-alert-<?php echo $message_type; ?>">
        <?php echo htmlspecialchars($message); ?>
    </div>
<?php endif; ?>

<!-- LIST VIEW -->
<?php if ($action === 'list'): ?>
    <div class="content-box">
        <?php
        try {
            $stmt = $pdo->query("SELECT * FROM gallery ORDER BY id DESC");
            $gallery = $stmt->fetchAll();
        } catch (Exception $e) {
            $gallery = [];
        }
        ?>
        
        <?php if (empty($gallery)): ?>
            <p style="text-align: center; padding: 30px; color: var(--text-muted);">No images uploaded to the gallery yet. Click 'Upload New Project Image' above to start.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image Preview</th>
                        <th>Project Name / Title</th>
                        <th>Category</th>
                        <th>Uploaded Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($gallery as $item): ?>
                        <tr>
                            <td>
                                <?php if (!empty($item['image_path']) && file_exists('../' . $item['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($item['image_path']); ?>" class="image-preview" alt="Gallery preview">
                                <?php else: ?>
                                    <div class="image-preview" style="background:#334155; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:700;">📸 Image</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($item['title']); ?></strong></td>
                            <td><span class="status-pill contacted" style="background-color:#e0f2fe; color:#0369a1;"><?php echo htmlspecialchars($item['category']); ?></span></td>
                            <td><?php echo date('d M Y, h:i A', strtotime($item['created_at'])); ?></td>
                            <td>
                                <a href="gallery.php?action=delete&id=<?php echo $item['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this image? This will permanently delete the file from the server.');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<!-- UPLOAD FORM VIEW -->
<?php elseif ($action === 'add'): ?>
    <div class="content-box">
        <form method="POST" action="gallery.php?action=add" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="title">Project / Work Title *</label>
                    <input type="text" id="title" name="title" class="admin-form-control" placeholder="e.g. Living Room Texture, Building Exterior Shield" required>
                </div>
                <div class="admin-form-group">
                    <label for="category">Category *</label>
                    <select id="category" name="category" class="admin-form-control" required>
                        <option value="">Select Category</option>
                        <option value="Interior">Interior Painting</option>
                        <option value="Exterior">Exterior Painting</option>
                        <option value="Texture">Texture Design</option>
                        <option value="Commercial">Commercial / Office</option>
                    </select>
                </div>
            </div>

            <div class="admin-form-group">
                <label for="image">Upload Image File *</label>
                <input type="file" id="image" name="image" class="admin-form-control" accept="image/*" required>
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Allowed file types: JPG, JPEG, PNG, WEBP. Max size: 2MB. Recommended ratio is landscape (4:3) for grid alignment.</small>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Start Upload</button>
        </form>
    </div>
<?php endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
