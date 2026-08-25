<?php
// admin/videos.php - Manage Homepage YouTube Videos
$adminPageTitle = 'Manage YouTube Videos';
require_once __DIR__ . '/header_sidebar.php';

$errors = [];

// Fetch current URLs from site settings
$rawUrls = get_setting('homepage_youtube_urls', '');
$videoUrls = json_decode($rawUrls, true);
if (!is_array($videoUrls)) {
    $videoUrls = [];
}

// Ensure the key exists in database with fallback if empty
if (empty($videoUrls)) {
    $oldSingleUrl = get_setting('homepage_youtube_url', 'https://www.youtube.com/watch?v=pRsrn9THN8Q');
    if (!empty($oldSingleUrl)) {
        $videoUrls = [$oldSingleUrl];
    }
}

// Action: Delete a video
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $idx = (int)($_GET['idx'] ?? -1);
    if (verify_csrf_token($_GET['token'] ?? '') && isset($videoUrls[$idx])) {
        unset($videoUrls[$idx]);
        $videoUrls = array_values($videoUrls);
        
        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES ('homepage_youtube_urls', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmt->execute([json_encode($videoUrls)]);
        
        // Update fallback key
        $stmtFallback = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES ('homepage_youtube_url', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmtFallback->execute([!empty($videoUrls) ? $videoUrls[0] : '']);
        
        set_flash('success', 'YouTube video deleted successfully.');
    } else {
        set_flash('error', 'Failed to delete video or invalid token.');
    }
    header('Location: videos.php');
    exit;
}

// Action: Move video up or down
if (isset($_GET['action']) && ($_GET['action'] === 'move_up' || $_GET['action'] === 'move_down')) {
    $idx = (int)($_GET['idx'] ?? -1);
    if (verify_csrf_token($_GET['token'] ?? '') && isset($videoUrls[$idx])) {
        if ($_GET['action'] === 'move_up' && $idx > 0) {
            $tmp = $videoUrls[$idx];
            $videoUrls[$idx] = $videoUrls[$idx - 1];
            $videoUrls[$idx - 1] = $tmp;
        } elseif ($_GET['action'] === 'move_down' && $idx < count($videoUrls) - 1) {
            $tmp = $videoUrls[$idx];
            $videoUrls[$idx] = $videoUrls[$idx + 1];
            $videoUrls[$idx + 1] = $tmp;
        }
        
        $stmt = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES ('homepage_youtube_urls', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmt->execute([json_encode($videoUrls)]);
        
        // Update fallback key
        $stmtFallback = $pdo->prepare("
            INSERT INTO site_settings (setting_key, setting_value)
            VALUES ('homepage_youtube_url', ?)
            ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
        ");
        $stmtFallback->execute([$videoUrls[0]]);
        
        set_flash('success', 'Video display order updated.');
    }
    header('Location: videos.php');
    exit;
}

// Action: Add new video
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Security token error.';
    } else {
        $newUrl = trim((string)($_POST['video_url'] ?? ''));
        if (empty($newUrl)) {
            $errors[] = 'Please enter a YouTube video URL.';
        } else {
            $vidId = get_youtube_video_id($newUrl);
            if (empty($vidId) || strlen($vidId) !== 11) {
                $errors[] = 'Invalid YouTube URL. Please enter a valid watch link or short link (e.g. https://youtu.be/xxx or https://youtube.com/watch?v=xxx).';
            } else {
                $videoUrls[] = $newUrl;
                
                $stmt = $pdo->prepare("
                    INSERT INTO site_settings (setting_key, setting_value)
                    VALUES ('homepage_youtube_urls', ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmt->execute([json_encode(array_values($videoUrls))]);
                
                // Update fallback key
                $stmtFallback = $pdo->prepare("
                    INSERT INTO site_settings (setting_key, setting_value)
                    VALUES ('homepage_youtube_url', ?)
                    ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)
                ");
                $stmtFallback->execute([$videoUrls[0]]);
                
                set_flash('success', 'New YouTube presentation video added successfully.');
                header('Location: videos.php');
                exit;
            }
        }
    }
}
?>

<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap:30px; align-items:start;">
  
  <!-- Add Video Form Card -->
  <div style="background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <h3 style="margin-bottom:20px; display:flex; align-items:center; gap:8px;">
      <span>📺</span> Add YouTube Video
    </h3>
    
    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger" style="padding: 12px; border-radius: 8px; background: #FFEBEE; color: #C62828; border: 1px solid #FFCDD2; margin-bottom: 15px; font-size: 0.9rem;">
        <ul style="margin: 0; padding-left: 20px;">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>
    
    <form method="POST" action="videos.php">
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="add">
      
      <div class="form-group">
        <label class="form-label">YouTube Link / URL</label>
        <input type="url" name="video_url" class="form-control" placeholder="https://www.youtube.com/watch?v=..." required>
        <small style="color:var(--text-muted); display:block; margin-top:6px; line-height:1.4;">
          Paste the YouTube watch URL or shareable link. The system will automatically fetch the thumbnail and embed it on the homepage.
        </small>
      </div>
      
      <button type="submit" class="btn btn-primary btn-block" style="width:100%;">➕ Add Video Link</button>
    </form>
  </div>
  
  <!-- Videos List Table Card -->
  <div style="grid-column: span 2; background:white; padding:25px; border-radius:var(--radius-md); box-shadow:var(--shadow-sm); border:1px solid var(--border-light);">
    <h3 style="margin-bottom:20px;">Current Presentation Videos (<?= count($videoUrls) ?>)</h3>
    
    <?php if (empty($videoUrls)): ?>
      <p style="color:var(--text-muted); text-align:center; padding:30px 0;">No presentation videos added yet. Add one in the left panel!</p>
    <?php else: ?>
      <div class="table-responsive">
        <table class="custom-table" style="width:100%;">
          <thead>
            <tr>
              <th style="width:140px;">Thumbnail</th>
              <th>Video Details</th>
              <th style="width:120px; text-align:center;">Order</th>
              <th style="width:160px; text-align:right;">Actions</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($videoUrls as $idx => $url): 
              $vidId = get_youtube_video_id($url);
              $thumbUrl = "https://img.youtube.com/vi/" . $vidId . "/mqdefault.jpg";
            ?>
              <tr>
                <td>
                  <a href="<?= htmlspecialchars($url) ?>" target="_blank" style="position:relative; display:block; border-radius:6px; overflow:hidden; border:1px solid var(--border-light);">
                    <img src="<?= $thumbUrl ?>" style="width:100%; aspect-ratio:16/9; object-fit:cover; display:block;" alt="Thumbnail">
                    <span style="position:absolute; bottom:4px; right:4px; background:rgba(0,0,0,0.8); color:white; font-size:0.65rem; padding:1px 4px; border-radius:3px;">YouTube ↗</span>
                  </a>
                </td>
                <td>
                  <div style="font-weight:600; margin-bottom:4px; color:var(--primary-dark);">Video #<?= $idx + 1 ?></div>
                  <div style="font-size:0.85rem; color:var(--text-muted); word-break:break-all;">
                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" style="color:var(--primary-green);"><?= htmlspecialchars($url) ?></a>
                  </div>
                  <div style="font-size:0.75rem; color:var(--text-muted); margin-top:2px;">Video ID: <code><?= htmlspecialchars($vidId) ?></code></div>
                </td>
                <td style="text-align:center;">
                  <div style="display:inline-flex; gap:4px;">
                    <a href="videos.php?action=move_up&idx=<?= $idx ?>&token=<?= generate_csrf_token() ?>" class="btn btn-outline btn-sm <?= $idx === 0 ? 'disabled' : '' ?>" style="padding:4px 8px; <?= $idx === 0 ? 'pointer-events:none; opacity:0.3;' : '' ?>" title="Move Up">▲</a>
                    <a href="videos.php?action=move_down&idx=<?= $idx ?>&token=<?= generate_csrf_token() ?>" class="btn btn-outline btn-sm <?= $idx === count($videoUrls) - 1 ? 'disabled' : '' ?>" style="padding:4px 8px; <?= $idx === count($videoUrls) - 1 ? 'pointer-events:none; opacity:0.3;' : '' ?>" title="Move Down">▼</a>
                  </div>
                </td>
                <td style="text-align:right;">
                  <div style="display:inline-flex; gap:6px;">
                    <a href="<?= htmlspecialchars($url) ?>" target="_blank" class="btn btn-outline btn-sm" style="padding:6px 12px; font-size:0.8rem;">View 🌐</a>
                    <a href="videos.php?action=delete&idx=<?= $idx ?>&token=<?= generate_csrf_token() ?>" onclick="return confirm('Remove this YouTube video link from the presentation?')" class="btn btn-outline btn-sm" style="color:#C62828; border-color:#C62828; padding:6px 12px; font-size:0.8rem;">Remove 🗑️</a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
  
</div>

</main>
</div>
</body>
</html>
