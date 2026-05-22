<?php
require_once __DIR__ . '/auth.php';
require_admin();

$config_path = dirname(__DIR__, 2) . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = preg_replace('/[^a-z]/', '', (string)($_POST['theme'] ?? ''));
    $themes = array_keys(all_themes());
    if (in_array($new, $themes, true)) {
        $src = file_get_contents($config_path);
        $out = preg_replace("/'theme'\s*=>\s*'[a-z]*'/", "'theme' => '$new'", $src, 1, $cnt);
        if ($cnt > 0 && $out !== null) {
            file_put_contents($config_path, $out);
            $_SESSION['flash'] = "테마가 '$new' (으)로 변경되었습니다.";
        } else {
            $_SESSION['flash_err'] = 'config.php에서 theme 항목을 찾지 못했습니다.';
        }
    } else {
        $_SESSION['flash_err'] = '알 수 없는 테마입니다.';
    }
    header('Location: /admin/theme.php');
    exit;
}

$themes  = all_themes();
$current = active_theme();

$admin_page = 'theme';
$admin_title = '테마 설정';
require __DIR__ . '/_layout_top.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div><?php unset($_SESSION['flash']); endif; ?>
<?php if (!empty($_SESSION['flash_err'])): ?>
<div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
  ⚠ <?= h($_SESSION['flash_err']) ?>
</div><?php unset($_SESSION['flash_err']); endif; ?>

<div class="bg-white rounded-xl border p-5 md:p-6">
  <h2 class="font-extrabold text-primary mb-1">레이아웃 테마</h2>
  <p class="text-xs text-gray-500 mb-5">
    사이트 전체 레이아웃·디자인을 한 번에 교체합니다. 데이터·기능은 그대로 유지됩니다.
    현재 테마: <span class="font-bold text-primary"><?= h($themes[$current] ?? $current) ?></span>
  </p>

  <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($themes as $key => $label):
      $meta = require dirname(__DIR__, 2) . "/themes/$key/theme.php";
      $cols = $meta['colors'] ?? [];
      $pc   = $cols['primary']['DEFAULT'] ?? '#0A2540';
      $ac   = $cols['accent']['DEFAULT'] ?? '#FFC107';
      $is_current = $key === $current;
    ?>
    <div class="border-2 rounded-xl overflow-hidden <?= $is_current ? 'border-primary' : 'border-gray-200' ?>">
      <!-- 색상 미리보기 -->
      <div class="h-24 flex" style="background:<?= h($pc) ?>">
        <div class="w-1/3" style="background:<?= h($ac) ?>"></div>
        <div class="flex-1 flex items-center justify-center text-white font-extrabold text-lg">
          <?= h($label) ?>
        </div>
      </div>
      <div class="p-4">
        <div class="flex items-center gap-2 mb-1">
          <span class="font-bold text-primary"><?= h($label) ?></span>
          <?php if ($is_current): ?>
          <span class="px-1.5 py-0.5 text-[10px] rounded bg-primary text-white font-bold">사용 중</span>
          <?php endif; ?>
        </div>
        <p class="text-xs text-gray-500 mb-3 min-h-[3em]"><?= h($meta['description'] ?? '') ?></p>
        <div class="flex gap-2">
          <a href="/?view=pc" target="_blank"
             class="flex-1 text-center text-xs py-2 rounded border border-gray-300 hover:bg-gray-50">미리보기</a>
          <?php if (!$is_current): ?>
          <form method="post" class="flex-1">
            <input type="hidden" name="theme" value="<?= h($key) ?>">
            <button class="w-full text-xs py-2 rounded bg-primary text-white font-bold hover:bg-primary-light">
              이 테마 적용
            </button>
          </form>
          <?php else: ?>
          <span class="flex-1 text-center text-xs py-2 rounded bg-gray-100 text-gray-400">적용됨</span>
          <?php endif; ?>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <p class="mt-5 text-[11px] text-gray-400">
    ※ 테마 적용 시 <code>includes/config.php</code>의 theme 값이 변경됩니다.
    미리보기는 적용 전 현재 테마 기준으로 열립니다 — 적용 후 새로고침해 확인하세요.
  </p>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
