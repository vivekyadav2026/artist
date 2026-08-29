<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'add' || $action === 'edit') {
        $title = trim(strip_tags($_POST['title']));
        $price_range = trim(strip_tags($_POST['price_range']));
        $description = trim(strip_tags($_POST['description']));
        
        if (empty($title) || empty($description)) {
            $message = "Title and Description are required fields.";
            $message_type = "error";
        } else {
            $image_path = '';
            
            // Handle Image Upload
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['image']['tmp_name'];
                $file_name = $_FILES['image']['name'];
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (!in_array($file_ext, $allowed_extensions)) {
                    $message = "Invalid image extension. Allowed: " . implode(', ', $allowed_extensions);
                    $message_type = "error";
                } else {
                    $upload_dir = '../uploads/services/';
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }
                    
                    $new_file_name = uniqid('service_', true) . '.' . $file_ext;
                    $dest_path = $upload_dir . $new_file_name;
                    
                    if (move_uploaded_file($file_tmp, $dest_path)) {
                        $image_path = 'uploads/services/' . $new_file_name;
                    } else {
                        $message = "Failed to upload image.";
                        $message_type = "error";
                    }
                }
            }
            
            // If no error, save to database
            if ($message_type !== 'error') {
                try {
                    if ($action === 'add') {
                        if (empty($image_path)) {
                            // Default placeholder path if no image is uploaded
                            $image_path = 'assets/images/service-default.jpg';
                        }
                        
                        $stmt = $pdo->prepare("INSERT INTO services (title, price_range, description, image_path) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$title, $price_range ?: null, $description, $image_path]);
                        $message = "Service added successfully!";
                        $message_type = "success";
                        $action = 'list'; // redirect to list
                    } else { // edit
                        if (!empty($image_path)) {
                            // Fetch old image to delete
                            $old_stmt = $pdo->prepare("SELECT image_path FROM services WHERE id = ?");
                            $old_stmt->execute([$id]);
                            $old_img = $old_stmt->fetchColumn();
                            
                            if ($old_img && $old_img !== 'assets/images/service-default.jpg' && file_exists('../' . $old_img)) {
                                @unlink('../' . $old_img);
                            }
                            
                            $stmt = $pdo->prepare("UPDATE services SET title = ?, price_range = ?, description = ?, image_path = ? WHERE id = ?");
                            $stmt->execute([$title, $price_range ?: null, $description, $image_path, $id]);
                        } else {
                            $stmt = $pdo->prepare("UPDATE services SET title = ?, price_range = ?, description = ? WHERE id = ?");
                            $stmt->execute([$title, $price_range ?: null, $description, $id]);
                        }
                        
                        $message = "Service updated successfully!";
                        $message_type = "success";
                        $action = 'list'; // redirect to list
                    }
                } catch (PDOException $e) {
                    $message = "Database error: " . $e->getMessage();
                    $message_type = "error";
                }
            }
        }
    }
}

// Handle Service Deletion
if ($action === 'delete' && $id > 0) {
    try {
        // Fetch image path to delete file
        $img_stmt = $pdo->prepare("SELECT image_path FROM services WHERE id = ?");
        $img_stmt->execute([$id]);
        $img_path = $img_stmt->fetchColumn();
        
        if ($img_path && $img_path !== 'assets/images/service-default.jpg' && file_exists('../' . $img_path)) {
            @unlink('../' . $img_path);
        }
        
        $del_stmt = $pdo->prepare("DELETE FROM services WHERE id = ?");
        $del_stmt->execute([$id]);
        
        $message = "Service deleted successfully.";
        $message_type = "success";
        $action = 'list';
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
}

// Render Templates
?>

<div class="admin-header-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Manage Art Collections</h2>
    <?php if ($action === 'list'): ?>
        <a href="services.php?action=add" class="btn-add">➕ Add New Collection</a>
    <?php else: ?>
        <a href="services.php" class="btn-action view">⬅️ Back to Collections List</a>
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
            $stmt = $pdo->query("SELECT * FROM services ORDER BY id ASC");
            $services = $stmt->fetchAll();
        } catch (Exception $e) {
            $services = [];
        }
        ?>

        <?php if (empty($services)): ?>
            <p style="text-align: center; padding: 30px; color: var(--text-muted);">No collections added yet. Click 'Add New Collection' to begin.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Image</th>
                        <th>Collection Title</th>
                        <th>Price Range</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($services as $srv): ?>
                        <tr>
                            <td>
                                <?php if (!empty($srv['image_path']) && file_exists('../' . $srv['image_path'])): ?>
                                    <img src="../<?php echo htmlspecialchars($srv['image_path']); ?>" class="image-preview" alt="Collection Thumbnail">
                                 <?php else: ?>
                                    <div class="image-preview" style="background:#c1440e; display:flex; align-items:center; justify-content:center; color:white; font-size:12px; font-weight:700;">🎨 Art</div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo htmlspecialchars($srv['title']); ?></strong></td>
                            <td><code><?php echo htmlspecialchars($srv['price_range'] ?: 'N/A'); ?></code></td>
                            <td><small><?php echo htmlspecialchars(substr($srv['description'], 0, 120)) . (strlen($srv['description']) > 120 ? '...' : ''); ?></small></td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="services.php?action=edit&id=<?php echo $srv['id']; ?>" class="btn-action edit">Edit</a>
                                    <a href="services.php?action=delete&id=<?php echo $srv['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this collection? This will delete the collection image as well.');">Delete</a>
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
        <form method="POST" action="services.php?action=add" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="title">Collection Title *</label>
                    <input type="text" id="title" name="title" class="admin-form-control" placeholder="e.g. Ember Triangles Series" required>
                </div>
                <div class="admin-form-group">
                    <label for="price_range">Price/Rates Range</label>
                    <input type="text" id="price_range" name="price_range" class="admin-form-control" placeholder="e.g. Starting ₹18,000">
                </div>
            </div>

            <div class="admin-form-group">
                <label for="description">Detailed Description *</label>
                <textarea id="description" name="description" class="admin-form-control" placeholder="Describe the collection theme, medium, style, series context..." required></textarea>
            </div>

            <div class="admin-form-group">
                <label for="image">Collection Banner Image</label>
                <input type="file" id="image" name="image" class="admin-form-control" accept="image/*">
                <small style="color:var(--text-muted); display:block; margin-top:5px;">Allowed file types: JPG, JPEG, PNG, WEBP. Max size: 2MB. Image will be automatically resized/fit to frontend ratios.</small>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Add Collection</button>
        </form>
    </div>

<!-- EDIT TEMPLATE -->
<?php elseif ($action === 'edit' && $id > 0): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $srv = $stmt->fetch();
    if (!$srv) {
        echo "<p>Collection not found.</p>";
        include __DIR__ . '/includes/footer.php';
        exit;
    }
    ?>
    <div class="content-box">
        <form method="POST" action="services.php?action=edit&id=<?php echo $id; ?>" enctype="multipart/form-data">
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="title">Collection Title *</label>
                    <input type="text" id="title" name="title" class="admin-form-control" value="<?php echo htmlspecialchars($srv['title']); ?>" required>
                </div>
                <div class="admin-form-group">
                    <label for="price_range">Price/Rates Range</label>
                    <input type="text" id="price_range" name="price_range" class="admin-form-control" value="<?php echo htmlspecialchars($srv['price_range']); ?>">
                </div>
            </div>

            <div class="admin-form-group">
                <label for="description">Detailed Description *</label>
                <textarea id="description" name="description" class="admin-form-control" required><?php echo htmlspecialchars($srv['description']); ?></textarea>
            </div>

            <div class="admin-form-group" style="display:flex; gap:20px; align-items:center;">
                <?php if (!empty($srv['image_path']) && file_exists('../' . $srv['image_path'])): ?>
                    <div>
                        <label>Current Image Preview</label>
                        <img src="../<?php echo htmlspecialchars($srv['image_path']); ?>" style="width:120px; height:90px; object-fit:cover; border-radius:4px; border:1px solid var(--border);" alt="Current Banner">
                    </div>
                <?php endif; ?>
                <div style="flex-grow:1;">
                    <label for="image">Change Banner Image (Optional)</label>
                    <input type="file" id="image" name="image" class="admin-form-control" accept="image/*">
                    <small style="color:var(--text-muted); display:block; margin-top:5px;">Leave empty to keep current image. Allowed types: JPG, JPEG, PNG, WEBP. Max size: 2MB.</small>
                </div>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Save Changes</button>
        </form>
    </div>
<?php endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
