<?php
// admin/messages.php - Admin Contact Messages Inbox

$adminPageTitle = 'Manage Contact Messages';
require_once __DIR__ . '/header_sidebar.php';

$action = $_GET['action'] ?? '';
$msgId = (int)($_GET['id'] ?? 0);

if ($action === 'mark_read' && $msgId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("UPDATE contact_messages SET is_read = 1 WHERE id = ?");
        $stmt->execute([$msgId]);
        set_flash('success', 'Message marked as read.');
    }
    header('Location: messages.php');
    exit;
}

if ($action === 'delete' && $msgId > 0) {
    if (verify_csrf_token($_GET['token'] ?? '')) {
        $stmt = $pdo->prepare("DELETE FROM contact_messages WHERE id = ?");
        $stmt->execute([$msgId]);
        set_flash('success', 'Message deleted.');
    }
    header('Location: messages.php');
    exit;
}

$messages = $pdo->query("SELECT * FROM contact_messages ORDER BY id DESC")->fetchAll();
?>

<div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px;">
  <h3>Inquiries & Contact Inbox (<?= count($messages) ?>)</h3>
</div>

<div class="table-responsive">
  <table class="custom-table">
    <thead>
      <tr>
        <th>Sender</th>
        <th>Subject</th>
        <th>Message Content</th>
        <th>Date</th>
        <th>Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($messages as $m): ?>
        <tr style="<?= !$m['is_read'] ? 'background-color: #FFFDE7;' : '' ?>">
          <td>
            <strong><?= htmlspecialchars($m['name']) ?></strong><br>
            <small><?= htmlspecialchars($m['email']) ?> | <?= htmlspecialchars($m['phone']) ?></small>
          </td>
          <td><strong><?= htmlspecialchars($m['subject']) ?></strong></td>
          <td style="max-width:300px;"><?= nl2br(htmlspecialchars($m['message'])) ?></td>
          <td><?= date('d M Y, h:i A', strtotime($m['created_at'])) ?></td>
          <td>
            <span style="color:<?= $m['is_read'] ? '#2E7D32' : '#C62828'; ?>; font-weight:bold;">
              <?= $m['is_read'] ? 'Read' : 'New Unread' ?>
            </span>
          </td>
          <td>
            <?php if (!$m['is_read']): ?>
              <a href="messages.php?action=mark_read&id=<?= $m['id'] ?>&token=<?= generate_csrf_token() ?>" class="btn btn-outline btn-sm">Mark Read</a>
            <?php endif; ?>
            <a href="messages.php?action=delete&id=<?= $m['id'] ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Delete message?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828;">Delete</a>
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
