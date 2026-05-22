<?php
require_once __DIR__ . '/auth.php';
require_admin();

$config_path   = dirname(__DIR__, 2) . '/includes/config.php';
$override_path = dirname(__DIR__, 2) . '/themes/color_overrides.json';
$theme_keys    = array_keys(all_themes());

/** 오버라이드 JSON 읽기/쓰기 */
function _load_overrides(string $path): array {
    if (!is_file($path)) return [];
    $j = json_decode((string)file_get_contents($path), true);
    return is_array($j) ? $j : [];
}
function _save_overrides(string $path, array $data): bool {
    return (bool)file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'apply') {
        $new = preg_replace('/[^a-z]/', '', (string)($_POST['theme'] ?? ''));
        if (in_array($new, $theme_keys, true)) {
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
    }
    elseif ($action === 'colors') {
        $t = preg_replace('/[^a-z]/', '', (string)($_POST['theme'] ?? ''));
        if (in_array($t, $theme_keys, true)) {
            $ov = _load_overrides($override_path);
            $pri = trim((string)($_POST['primary'] ?? ''));
            $acc = trim((string)($_POST['accent'] ?? ''));
            $hex = '/^#[0-9A-Fa-f]{6}$/';
            if (preg_match($hex, $pri) && preg_match($hex, $acc)) {
                $ov[$t] = ['primary' => $pri, 'accent' => $acc];
                _save_overrides($override_path, $ov);
                $_SESSION['flash'] = "'$t' 테마 색상이 저장되었습니다.";
            } else {
                $_SESSION['flash_err'] = '올바른 색상 코드(#RRGGBB)가 아닙니다.';
            }
        }
    }
    elseif ($action === 'reset_colors') {
        $t = preg_replace('/[^a-z]/', '', (string)($_POST['theme'] ?? ''));
        $ov = _load_overrides($override_path);
        unset($ov[$t]);
        _save_overrides($override_path, $ov);
        $_SESSION['flash'] = "'$t' 테마 색상을 기본값으로 되돌렸습니다.";
    }
    header('Location: /admin/theme.php');
    exit;
}

$themes    = all_themes();
$current   = active_theme();
$overrides = _load_overrides($override_path);

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

<div class="mb-4">
  <h2 class="font-extrabold text-primary">레이아웃 테마</h2>
  <p class="text-xs text-gray-500 mt-1">
    레이아웃 교체 + 테마별 주요 색상(메인·포인트) 직접 편집. 현재 테마:
    <span class="font-bold text-primary"><?= h($themes[$current] ?? $current) ?></span>
  </p>
</div>

<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
  <?php foreach ($themes as $key => $label):
    $meta = require dirname(__DIR__, 2) . "/themes/$key/theme.php";
    $base = $meta['colors'] ?? [];
    // 효과적 색상 = 오버라이드 우선
    $pc = $overrides[$key]['primary'] ?? ($base['primary']['DEFAULT'] ?? '#0A2540');
    $ac = $overrides[$key]['accent']  ?? ($base['accent']['DEFAULT']  ?? '#FFC107');
    $is_current  = $key === $current;
    $customized  = isset($overrides[$key]);
  ?>
  <div class="border-2 rounded-xl overflow-hidden <?= $is_current ? 'border-primary' : 'border-gray-200' ?>"
       x-data="{ openColor: false }">
    <!-- 색상 미리보기 -->
    <div class="h-20 flex" style="background:<?= h($pc) ?>">
      <div class="w-1/3" style="background:<?= h($ac) ?>"></div>
      <div class="flex-1 flex items-center justify-center text-white font-extrabold"><?= h($label) ?></div>
    </div>
    <div class="p-4">
      <div class="flex items-center gap-1.5 mb-1 flex-wrap">
        <span class="font-bold text-primary"><?= h($label) ?></span>
        <?php if ($is_current): ?>
        <span class="px-1.5 py-0.5 text-[10px] rounded bg-primary text-white font-bold">사용 중</span>
        <?php endif; ?>
        <?php if ($customized): ?>
        <span class="px-1.5 py-0.5 text-[10px] rounded bg-amber-100 text-amber-700 font-bold">색상 수정됨</span>
        <?php endif; ?>
      </div>
      <p class="text-xs text-gray-500 mb-3 min-h-[3em]"><?= h($meta['description'] ?? '') ?></p>

      <!-- 미리보기 / 적용 -->
      <div class="flex gap-2 mb-2">
        <a href="/?preview_theme=<?= h($key) ?>" target="_blank"
           class="flex-1 text-center text-xs py-2 rounded border border-gray-300 hover:bg-gray-50">미리보기</a>
        <?php if (!$is_current): ?>
        <form method="post" class="flex-1">
          <input type="hidden" name="action" value="apply">
          <input type="hidden" name="theme" value="<?= h($key) ?>">
          <button class="w-full text-xs py-2 rounded bg-primary text-white font-bold hover:bg-primary-light">이 테마 적용</button>
        </form>
        <?php else: ?>
        <span class="flex-1 text-center text-xs py-2 rounded bg-gray-100 text-gray-400">적용됨</span>
        <?php endif; ?>
      </div>

      <!-- 색상 편집 토글 -->
      <button type="button" @click="openColor = !openColor"
              class="w-full text-xs py-1.5 text-primary font-semibold hover:underline">
        🎨 색상 편집 <span x-text="openColor ? '▲' : '▼'"></span>
      </button>

      <div x-show="openColor" x-cloak class="mt-2 pt-3 border-t space-y-3">
        <form method="post" class="space-y-3">
          <input type="hidden" name="action" value="colors">
          <input type="hidden" name="theme" value="<?= h($key) ?>">
          <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-gray-600">메인 색상 (primary)</label>
            <input type="color" name="primary" value="<?= h($pc) ?>"
                   class="w-14 h-8 rounded border border-gray-300 cursor-pointer">
          </div>
          <div class="flex items-center justify-between">
            <label class="text-xs font-semibold text-gray-600">포인트 색상 (accent)</label>
            <input type="color" name="accent" value="<?= h($ac) ?>"
                   class="w-14 h-8 rounded border border-gray-300 cursor-pointer">
          </div>
          <p class="text-[10px] text-gray-400">밝은/어두운 변형은 자동 생성됩니다.</p>
          <button class="w-full text-xs py-2 rounded bg-accent text-primary font-bold hover:bg-accent-dark">색상 저장</button>
        </form>
        <?php if ($customized): ?>
        <form method="post">
          <input type="hidden" name="action" value="reset_colors">
          <input type="hidden" name="theme" value="<?= h($key) ?>">
          <button class="w-full text-xs py-1.5 text-red-500 hover:text-red-700">기본 색상으로 초기화</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endforeach; ?>
</div>

<p class="mt-5 text-[11px] text-gray-400">
  ※ 테마 적용 → <code>includes/config.php</code> 변경 · 색상 저장 → <code>themes/color_overrides.json</code> 기록.
  미리보기는 적용 없이 해당 테마로 새 탭에서 열립니다.
</p>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
