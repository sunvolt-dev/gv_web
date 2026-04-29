<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $name = trim((string)($_POST['ca_name'] ?? ''));
        $parent = $_POST['ca_parent'] !== '' ? (int)$_POST['ca_parent'] : null;
        $order = (int)($_POST['ca_order'] ?? 0);
        if ($name !== '') {
            db_exec('INSERT INTO categories (ca_name, ca_parent, ca_order) VALUES (?, ?, ?)',
                    [$name, $parent, $order]);
            $_SESSION['flash'] = "카테고리 '$name' 등록 완료";
        }
    } elseif ($action === 'update') {
        $id = (int)$_POST['ca_id'];
        $name = trim((string)($_POST['ca_name'] ?? ''));
        $order = (int)($_POST['ca_order'] ?? 0);
        if ($name !== '') {
            db_exec('UPDATE categories SET ca_name=?, ca_order=? WHERE ca_id=?', [$name, $order, $id]);
            $_SESSION['flash'] = "카테고리 #$id 수정 완료";
        }
    } elseif ($action === 'delete') {
        $id = (int)$_POST['ca_id'];
        // 하위 또는 상품이 있으면 거부
        $has_children = (int)db_one('SELECT COUNT(*) c FROM categories WHERE ca_parent=?', [$id])['c'];
        $has_products = (int)db_one('SELECT COUNT(*) c FROM products WHERE ca_id=?', [$id])['c'];
        if ($has_children || $has_products) {
            $_SESSION['flash_err'] = "삭제 불가: 하위 카테고리 또는 등록된 상품이 있습니다.";
        } else {
            db_exec('DELETE FROM categories WHERE ca_id=?', [$id]);
            $_SESSION['flash'] = "카테고리 #$id 삭제 완료";
        }
    }
    header('Location: /admin/categories.php');
    exit;
}

$tree = category_tree();
$roots = $tree[0] ?? [];

$admin_page = 'categories';
$admin_title = '카테고리 관리';
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

<div class="grid lg:grid-cols-[1fr_360px] gap-6">

  <!-- 카테고리 트리 -->
  <div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-4 border-b">
      <h2 class="font-extrabold text-primary">카테고리 목록</h2>
    </div>
    <div class="divide-y">
      <?php if (empty($roots)): ?>
        <div class="p-8 text-center text-gray-400">등록된 카테고리가 없습니다.</div>
      <?php else: foreach ($roots as $r): ?>
        <div class="px-5 py-4 hover:bg-gray-50">
          <form method="post" class="flex items-center gap-2">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="ca_id" value="<?= (int)$r['ca_id'] ?>">
            <span class="text-xs text-gray-400 w-12">#<?= (int)$r['ca_id'] ?></span>
            <input type="text" name="ca_name" value="<?= h($r['ca_name']) ?>"
                   class="flex-1 h-9 px-3 text-sm border border-gray-300 rounded font-bold text-primary">
            <input type="number" name="ca_order" value="<?= (int)$r['ca_order'] ?>"
                   class="w-16 h-9 px-2 text-sm border border-gray-300 rounded">
            <button class="text-xs px-3 py-1.5 rounded bg-primary text-white">저장</button>
          </form>
          <form method="post" class="inline" onsubmit="return confirm('이 카테고리와 하위를 삭제하시겠습니까?');">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="ca_id" value="<?= (int)$r['ca_id'] ?>">
            <button class="text-xs text-red-500 ml-12 mt-1">✕ 삭제</button>
          </form>
          <?php if (!empty($tree[$r['ca_id']])): ?>
          <div class="mt-2 ml-12 space-y-1.5">
            <?php foreach ($tree[$r['ca_id']] as $sub): ?>
            <form method="post" class="flex items-center gap-2">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="ca_id" value="<?= (int)$sub['ca_id'] ?>">
              <span class="text-xs text-gray-400 w-12">#<?= (int)$sub['ca_id'] ?></span>
              <input type="text" name="ca_name" value="<?= h($sub['ca_name']) ?>"
                     class="flex-1 h-8 px-3 text-xs border border-gray-300 rounded">
              <input type="number" name="ca_order" value="<?= (int)$sub['ca_order'] ?>"
                     class="w-16 h-8 px-2 text-xs border border-gray-300 rounded">
              <button class="text-xs px-2 py-1 rounded bg-gray-200 hover:bg-gray-300">저장</button>
            </form>
            <?php endforeach; ?>
          </div>
          <?php endif; ?>
        </div>
      <?php endforeach; endif; ?>
    </div>
  </div>

  <!-- 추가 폼 -->
  <div class="bg-white rounded-xl border p-5">
    <h2 class="font-extrabold text-primary mb-4">카테고리 추가</h2>
    <form method="post" class="space-y-3">
      <input type="hidden" name="action" value="add">
      <div>
        <label class="text-xs font-semibold text-gray-600">카테고리명 *</label>
        <input type="text" name="ca_name" required
               class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg
                      focus:outline-none focus:ring-2 focus:ring-accent">
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">상위 카테고리</label>
        <select name="ca_parent"
                class="mt-1 w-full h-10 px-2 border border-gray-300 rounded-lg">
          <option value="">없음 (대분류)</option>
          <?php foreach ($roots as $r): ?>
          <option value="<?= (int)$r['ca_id'] ?>"><?= h($r['ca_name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="text-xs font-semibold text-gray-600">정렬 순서</label>
        <input type="number" name="ca_order" value="99"
               class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg">
      </div>
      <button class="w-full h-10 rounded-lg bg-primary text-white font-bold hover:bg-primary-light">
        추가
      </button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
