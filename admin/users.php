<?php
// admin/users.php - Admin Registered Users Management

$adminPageTitle = 'Manage User Accounts';
require_once __DIR__ . '/header_sidebar.php';

if (isset($_GET['action']) && $_GET['action'] === 'toggle_status') {
    $usrId = (int)($_GET['id'] ?? 0);
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("UPDATE users SET status = IF(status='active', 'inactive', 'active') WHERE id = ? AND role_id != 1");
        $stmt->execute([$usrId]);
        set_flash('success', 'User account status toggled.');
    }
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT u.*, r.name as role_name FROM users u JOIN roles r ON u.role_id = r.id ORDER BY u.id DESC")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <h3>Registered Users Directory (<?= count($users) ?>)</h3>
</div>

<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Full Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Phone & City</th>
        <th>Status</th>
        <th>Registered Date</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><span class="card-badge" style="position:static;"><?= htmlspecialchars($u['role_name']) ?></span></td>
          <td><?= htmlspecialchars($u['phone'] ?: 'N/A') ?><br><small><?= htmlspecialchars($u['city'] ?: '') ?></small></td>
          <td><span style="color:<?= $u['status'] === 'active' ? '#2E7D32' : '#C62828'; ?>; font-weight:bold;"><?= htmlspecialchars($u['status']) ?></span></td>
          <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
          <td>
            <?php if ($u['role_name'] !== 'admin'): ?>
              <a href="users.php?action=toggle_status&id=<?= $u['id'] ?>&token=<?= generate_csrf_token() ?>" class="btn btn-outline btn-sm">
                <?= $u['status'] === 'active' ? 'Deactivate' : 'Activate' ?>
              </a>
            <?php else: ?>
              <span style="font-size:0.8rem; color:var(--text-muted);">Superadmin</span>
            <?php endif; ?>
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
