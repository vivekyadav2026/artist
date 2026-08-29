<?php
include __DIR__ . '/includes/header.php';

// Fetch statistics
$services_count = 0;
$gallery_count = 0;
$enquiries_total = 0;
$enquiries_pending = 0;

try {
    // Services Count
    $services_count = $pdo->query("SELECT COUNT(*) FROM services")->fetchColumn();
    // Gallery Count
    $gallery_count = $pdo->query("SELECT COUNT(*) FROM gallery")->fetchColumn();
    // Total Enquiries
    $enquiries_total = $pdo->query("SELECT COUNT(*) FROM enquiries")->fetchColumn();
    // Pending Enquiries
    $enquiries_pending = $pdo->query("SELECT COUNT(*) FROM enquiries WHERE status = 'Pending'")->fetchColumn();
    
    // Fetch 5 recent enquiries
    $recent_stmt = $pdo->query("SELECT * FROM enquiries ORDER BY id DESC LIMIT 5");
    $recent_enquiries = $recent_stmt->fetchAll();
} catch (Exception $e) {
    $recent_enquiries = [];
}
?>

<div class="stats-row">
    <div class="stat-card">
        <div class="stat-card-info">
            <h3>Pending Leads</h3>
            <p><?php echo $enquiries_pending; ?></p>
        </div>
        <div class="stat-card-icon" style="background-color: #fee2e2; color: #ef4444;">📋</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-info">
            <h3>Total Enquiries</h3>
            <p><?php echo $enquiries_total; ?></p>
        </div>
        <div class="stat-card-icon" style="background-color: #dbeafe; color: #3b82f6;">📬</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-info">
            <h3>Art Collections</h3>
            <p><?php echo $services_count; ?></p>
        </div>
        <div class="stat-card-icon">🖌️</div>
    </div>
    <div class="stat-card">
        <div class="stat-card-info">
            <h3>Portfolio Artworks</h3>
            <p><?php echo $gallery_count; ?></p>
        </div>
        <div class="stat-card-icon" style="background-color: #fef3c7; color: #f59e0b;">🖼️</div>
    </div>
</div>

<div class="content-box">
    <div class="content-box-header">
        <h3>Recent Enquiry Leads</h3>
        <a href="enquiries.php" class="btn-action view">View All Leads</a>
    </div>

    <?php if (empty($recent_enquiries)): ?>
        <p style="text-align: center; padding: 20px 0; color: var(--text-muted);">No enquiries received yet.</p>
    <?php else: ?>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Customer Name</th>
                    <th>Phone Number</th>
                    <th>Requested Service</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recent_enquiries as $enq): ?>
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
                            <a href="enquiries.php?action=view&id=<?php echo $enq['id']; ?>" class="btn-action edit">
                                Manage
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div class="form-grid">
    <div class="content-box">
        <div class="content-box-header">
            <h3>Quick Configuration Details</h3>
        </div>
        <table class="admin-table">
            <tr>
                <td><strong>Primary Phone:</strong></td>
                <td><?php echo htmlspecialchars(getSetting('contact_phone')); ?></td>
            </tr>
            <tr>
                <td><strong>WhatsApp Contact:</strong></td>
                <td><?php echo htmlspecialchars(getSetting('contact_whatsapp')); ?></td>
            </tr>
            <tr>
                <td><strong>Site Email:</strong></td>
                <td><?php echo htmlspecialchars(getSetting('contact_email')); ?></td>
            </tr>
            <tr>
                <td><strong>Service Locations:</strong></td>
                <td><small><?php echo htmlspecialchars(getSetting('service_areas')); ?></small></td>
            </tr>
        </table>
        <div style="margin-top: 15px;">
            <a href="settings.php" class="btn-action edit" style="display:block; text-align:center;">Edit Business Details</a>
        </div>
    </div>

    <div class="content-box">
        <div class="content-box-header">
            <h3>About Custom Admin Panel</h3>
        </div>
        <p style="font-size: 14px; line-height: 1.6; margin-bottom: 12px;">Welcome to your dynamic website administration system. Using this control panel you can:</p>
        <ul style="font-size: 13.5px; line-height: 1.8; margin-left: 20px; color: var(--text-body);">
            <li>Add, edit, or delete professional paint services.</li>
            <li>Upload high-quality portfolio images of completed works.</li>
            <li>Instantly change contact numbers, addresses, and map embeds.</li>
            <li>Review inquiries sent by prospective clients online.</li>
            <li>Maintain testimonials and feedback details.</li>
        </ul>
    </div>
</div>

<?php
include __DIR__ . '/includes/footer.php';
?>
