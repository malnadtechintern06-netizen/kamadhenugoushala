<?php
// admin/adoptions.php - Admin Cow Adoptions Management

$adminPageTitle = 'Manage Cow Adoptions';
require_once __DIR__ . '/header_sidebar.php';

// Handle Status Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    if (verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $adpId = (int)$_POST['adoption_id'];
        $newStatus = sanitize($_POST['status']);
        $stmt = $pdo->prepare("UPDATE adoptions SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $adpId]);
        set_flash('success', 'Adoption status updated.');
    }
    header('Location: adoptions.php');
    exit;
}

$adoptions = $pdo->query("
    SELECT a.*, c.name as cow_name, c.tag_number as cow_tag 
    FROM adoptions a 
    JOIN cows c ON a.cow_id = c.id 
    ORDER BY a.id DESC
")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <h3>Cow Adoptions Log (<?= count($adoptions) ?>)</h3>
</div>

<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Adoption #</th>
        <th>Cow</th>
        <th>Adopter Name</th>
        <th>Contact</th>
        <th>Duration</th>
        <th>Total Amount</th>
        <th>Status</th>
        <th>Update</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($adoptions as $a): ?>
        <tr>
          <td><strong><?= htmlspecialchars($a['adoption_number']) ?></strong></td>
          <td><?= htmlspecialchars($a['cow_name']) ?> (Tag #<?= htmlspecialchars($a['cow_tag']) ?>)</td>
          <td><?= htmlspecialchars($a['adopter_name']) ?></td>
          <td><?= htmlspecialchars($a['email']) ?><br><small><?= htmlspecialchars($a['phone']) ?></small></td>
          <td><?= $a['duration_months'] ?> Months</td>
          <td style="font-weight:bold; color:var(--primary-dark);"><?= format_currency($a['total_amount']) ?></td>
          <td><span style="color:<?= $a['status'] === 'Active' ? '#2E7D32' : '#C62828'; ?>; font-weight:bold;"><?= htmlspecialchars($a['status']) ?></span></td>
          <td>
            <form method="POST" action="adoptions.php" style="display:flex; gap:5px;">
              <?= csrf_field() ?>
              <input type="hidden" name="update_status" value="1">
              <input type="hidden" name="adoption_id" value="<?= $a['id'] ?>">
              <select name="status" class="form-control" style="padding:4px 8px; font-size:0.82rem;" onchange="this.form.submit()">
                <option value="Active" <?= $a['status'] === 'Active' ? 'selected' : '' ?>>Active</option>
                <option value="Expired" <?= $a['status'] === 'Expired' ? 'selected' : '' ?>>Expired</option>
                <option value="Cancelled" <?= $a['status'] === 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
              </select>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

</main>
</div>
</body>
</html>
