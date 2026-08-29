<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Add Testimonial Action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'add') {
    $name = trim(strip_tags($_POST['name']));
    $location = trim(strip_tags($_POST['location']));
    $rating = intval($_POST['rating']);
    $review = trim(strip_tags($_POST['review']));
    
    if (empty($name) || empty($review)) {
        $message = "Customer Name and Review text are required fields.";
        $message_type = "error";
    } elseif ($rating < 1 || $rating > 5) {
        $message = "Please select a rating between 1 and 5 stars.";
        $message_type = "error";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO testimonials (name, location, rating, review) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $location ?: null, $rating, $review]);
            
            $message = "Customer review added successfully!";
            $message_type = "success";
            $action = 'list';
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Handle Delete Testimonial Action
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM testimonials WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Testimonial deleted successfully.";
        $message_type = "success";
        $action = 'list';
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
}
?>

<div class="admin-header-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Manage Customer Reviews</h2>
    <?php if ($action === 'list'): ?>
        <a href="testimonials.php?action=add" class="btn-add">➕ Add New Review</a>
    <?php else: ?>
        <a href="testimonials.php" class="btn-action view">⬅️ Back to Reviews List</a>
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
            $stmt = $pdo->query("SELECT * FROM testimonials ORDER BY id DESC");
            $testimonials = $stmt->fetchAll();
        } catch (Exception $e) {
            $testimonials = [];
        }
        ?>
        
        <?php if (empty($testimonials)): ?>
            <p style="text-align: center; padding: 30px; color: var(--text-muted);">No reviews posted yet. Click 'Add New Review' to begin.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Customer Name</th>
                        <th>Location</th>
                        <th>Rating</th>
                        <th>Review Feedback</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($testimonials as $t): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($t['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($t['location'] ?: 'N/A'); ?></td>
                            <td style="color:var(--accent);">
                                <?php for($i=1; $i<=5; $i++): ?>
                                    <?php echo ($i <= $t['rating']) ? '★' : '☆'; ?>
                                <?php endfor; ?>
                            </td>
                            <td><small>"<?php echo htmlspecialchars(substr($t['review'], 0, 100)) . (strlen($t['review']) > 100 ? '...' : ''); ?>"</small></td>
                            <td>
                                <a href="testimonials.php?action=delete&id=<?php echo $t['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to delete this review?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<!-- ADD FORM VIEW -->
<?php elseif ($action === 'add'): ?>
    <div class="content-box">
        <form method="POST" action="testimonials.php?action=add">
            <div class="form-grid">
                <div class="admin-form-group">
                    <label for="name">Customer Name *</label>
                    <input type="text" id="name" name="name" class="admin-form-control" placeholder="e.g. Ramesh Saxena" required>
                </div>
                <div class="admin-form-group">
                    <label for="location">Customer Location / City</label>
                    <input type="text" id="location" name="location" class="admin-form-control" placeholder="e.g. Noida, Sec 15">
                </div>
            </div>

            <div class="admin-form-group">
                <label for="rating">Rating Stars *</label>
                <select id="rating" name="rating" class="admin-form-control" required>
                    <option value="5">★★★★★ (5 Stars)</option>
                    <option value="4">★★★★☆ (4 Stars)</option>
                    <option value="3">★★★☆☆ (3 Stars)</option>
                    <option value="2">★★☆☆☆ (2 Stars)</option>
                    <option value="1">★☆☆☆☆ (1 Star)</option>
                </select>
            </div>

            <div class="admin-form-group">
                <label for="review">Review Content *</label>
                <textarea id="review" name="review" class="admin-form-control" placeholder="Type the client feedback here..." required></textarea>
            </div>

            <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Add Testimonial</button>
        </form>
    </div>
<?php endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
