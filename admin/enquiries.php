<?php
include __DIR__ . '/includes/header.php';

$message = '';
$message_type = '';

$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $action === 'update_status' && $id > 0) {
    $new_status = isset($_POST['status']) ? trim(strip_tags($_POST['status'])) : '';
    
    $allowed_statuses = ['Pending', 'Contacted', 'Completed'];
    if (in_array($new_status, $allowed_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE enquiries SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $id]);
            $message = "Lead status updated successfully to '$new_status'.";
            $message_type = "success";
            $action = 'view'; // remain on view page
        } catch (PDOException $e) {
            $message = "Database error: " . $e->getMessage();
            $message_type = "error";
        }
    }
}

// Handle Delete Lead
if ($action === 'delete' && $id > 0) {
    try {
        $stmt = $pdo->prepare("DELETE FROM enquiries WHERE id = ?");
        $stmt->execute([$id]);
        $message = "Enquiry lead deleted successfully.";
        $message_type = "success";
        $action = 'list';
    } catch (PDOException $e) {
        $message = "Database error: " . $e->getMessage();
        $message_type = "error";
    }
}
?>

<div class="admin-header-actions" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
    <h2>Enquiry Leads Manager</h2>
    <?php if ($action !== 'list'): ?>
        <a href="enquiries.php" class="btn-action view">⬅️ Back to Leads List</a>
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
            $stmt = $pdo->query("SELECT * FROM enquiries ORDER BY id DESC");
            $enquiries = $stmt->fetchAll();
        } catch (Exception $e) {
            $enquiries = [];
        }
        ?>
        
        <?php if (empty($enquiries)): ?>
            <p style="text-align: center; padding: 30px; color: var(--text-muted);">No enquiries received yet from the front-end website.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Service Required</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($enquiries as $enq): ?>
                        <tr>
                            <td><?php echo date('d M Y, h:i A', strtotime($enq['created_at'])); ?></td>
                            <td><strong><?php echo htmlspecialchars($enq['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($enq['phone']); ?></td>
                            <td><?php echo htmlspecialchars($enq['service']); ?></td>
                            <td>
                                <span class="status-pill <?php echo strtolower($enq['status']); ?>">
                                    <?php echo htmlspecialchars($enq['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div style="display:flex; gap:6px;">
                                    <a href="enquiries.php?action=view&id=<?php echo $enq['id']; ?>" class="btn-action view">View Details</a>
                                    <a href="enquiries.php?action=delete&id=<?php echo $enq['id']; ?>" class="btn-action delete" onclick="return confirm('Are you sure you want to permanently delete this lead?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

<!-- VIEW DETAILED LEAD VIEW -->
<?php elseif ($action === 'view' && $id > 0): ?>
    <?php
    $stmt = $pdo->prepare("SELECT * FROM enquiries WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();
    
    if (!$lead) {
        echo "<p>Lead details not found.</p>";
        include __DIR__ . '/includes/footer.php';
        exit;
    }
    ?>
    
    <div class="form-grid">
        <div class="content-box">
            <div class="content-box-header">
                <h3>Customer Information</h3>
            </div>
            <table class="admin-table">
                <tr>
                    <td><strong>Full Name:</strong></td>
                    <td><strong><?php echo htmlspecialchars($lead['name']); ?></strong></td>
                </tr>
                <tr>
                    <td><strong>Phone Number:</strong></td>
                    <td>
                        <a href="tel:<?php echo htmlspecialchars($lead['phone']); ?>" style="color:var(--primary); font-weight:700;">
                            <?php echo htmlspecialchars($lead['phone']); ?> 📞
                        </a>
                        <span style="margin: 0 10px; color:#cbd5e1;">|</span>
                        <a href="https://wa.me/<?php echo preg_replace('/[^0-9]/', '', $lead['phone']); ?>" target="_blank" style="color:#25d366; font-weight:700;">
                            Chat on WhatsApp 💬
                        </a>
                    </td>
                </tr>
                <tr>
                    <td><strong>Email Address:</strong></td>
                    <td>
                        <?php if ($lead['email']): ?>
                            <a href="mailto:<?php echo htmlspecialchars($lead['email']); ?>" style="text-decoration:underline;">
                                <?php echo htmlspecialchars($lead['email']); ?>
                            </a>
                        <?php else: ?>
                            <span style="color:var(--text-muted); font-style:italic;">Not Provided</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td><strong>Service Interested:</strong></td>
                    <td><span class="status-pill contacted" style="background:#e0f2fe; color:#0369a1;"><?php echo htmlspecialchars($lead['service']); ?></span></td>
                </tr>
                <tr>
                    <td><strong>Enquiry Date:</strong></td>
                    <td><?php echo date('l, d F Y - h:i A', strtotime($lead['created_at'])); ?></td>
                </tr>
            </table>
        </div>

        <div class="content-box">
            <div class="content-box-header">
                <h3>Enquiry Message &amp; Status</h3>
            </div>
            
            <div style="margin-bottom: 25px;">
                <label style="display:block; font-weight:700; color:var(--text-dark); margin-bottom:6px;">Message Detail:</label>
                <div style="background-color: var(--bg-main); padding: 15px; border-radius: 6px; font-size:14.5px; line-height:1.6; border: 1px solid var(--border); min-height:80px; white-space: pre-wrap;"><?php echo htmlspecialchars($lead['message'] ?: 'No message details provided.'); ?></div>
            </div>

            <form method="POST" action="enquiries.php?action=update_status&id=<?php echo $id; ?>">
                <div class="admin-form-group">
                    <label for="status">Change Lead Status</label>
                    <select id="status" name="status" class="admin-form-control">
                        <option value="Pending" <?php echo ($lead['status'] === 'Pending') ? 'selected' : ''; ?>>🔴 Pending (New Lead)</option>
                        <option value="Contacted" <?php echo ($lead['status'] === 'Contacted') ? 'selected' : ''; ?>>🔵 Contacted (In Progress)</option>
                        <option value="Completed" <?php echo ($lead['status'] === 'Completed') ? 'selected' : ''; ?>>🟢 Completed (Done / Signed)</option>
                    </select>
                </div>
                <button type="submit" class="btn-add" style="border:none; cursor:pointer;">💾 Save Status</button>
            </form>
        </div>
    </div>
<?php endif; ?>

<?php
include __DIR__ . '/includes/footer.php';
?>
