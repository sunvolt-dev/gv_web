<?php
require_once __DIR__ . '/auth.php';
require_admin();

// 삭제 처리
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['it_id'] ?? 0);
    if ($id) {
        db_exec('DELETE FROM products WHERE it_id = ?', [$id]);
        $_SESSION['flash'] = "상품 #$id 삭제 완료";
    }
    header('Location: /admin/products.php');
    exit;
}

$q = trim((string)($_GET['q'] ?? ''));
$ca = (int)($_GET['ca_id'] ?? 0);

$where = '1=1';
$params = [];
if ($q) { $where .= " AND p.it_name LIKE ?"; $params[] = "%$q%"; }
if ($ca) {
    $ids = descendant_category_ids($ca);
    $ph = implode(',', array_fill(0, count($ids), '?'));
    $where .= " AND p.ca_id IN ($ph)";
    $params = array_merge($params, $ids);
}

$rows = db_all(
    "SELECT p.*, c.ca_name FROM products p
     LEFT JOIN categories c ON c.ca_id = p.ca_id
     WHERE $where
     ORDER BY p.it_id DESC", $params
);

$admin_page = 'products';
$admin_title = '상품 관리';
require __DIR__ . '/_layout_top.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div>
<?php unset($_SESSION['flash']); endif; ?>

<div class="bg-white rounded-xl border overflow-hidden">

  <div class="px-5 py-4 border-b flex flex-wrap items-center gap-3 justify-between">
    <form method="get" class="flex gap-2 items-center flex-1 max-w-md">
      <input type="text" name="q" value="<?= h($q) ?>" placeholder="상품명 검색"
             class="flex-1 h-9 px-3 text-sm border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-accent">
      <select name="ca_id" class="h-9 px-2 text-sm border border-gray-300 rounded-lg">
        <option value="">전체 카테고리</option>
        <?php foreach (get_categories() as $c): ?>
        <option value="<?= (int)$c['ca_id'] ?>" <?= $ca == $c['ca_id'] ? 'selected' : '' ?>>
          <?= str_repeat('— ', $c['ca_parent'] ? 1 : 0) ?><?= h($c['ca_name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
      <button class="h-9 px-4 text-sm font-bold rounded-lg bg-primary text-white">검색</button>
    </form>

    <a href="/admin/product_form.php"
       class="h-9 px-4 inline-flex items-center text-sm font-bold rounded-lg bg-accent text-primary hover:bg-accent-dark">
      + 새 상품 등록
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs text-gray-500">
        <tr>
          <th class="text-left px-5 py-3">ID</th>
          <th class="text-left px-5 py-3">이미지</th>
          <th class="text-left px-5 py-3">상품명</th>
          <th class="text-left px-5 py-3">카테고리</th>
          <th class="text-right px-5 py-3">정가</th>
          <th class="text-right px-5 py-3">할인가</th>
          <th class="text-right px-5 py-3">재고</th>
          <th class="text-center px-5 py-3">상태</th>
          <th class="px-5 py-3"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php if (empty($rows)): ?>
        <tr><td colspan="9" class="text-center py-12 text-gray-400">등록된 상품이 없습니다.</td></tr>
        <?php else: foreach ($rows as $r): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3 text-gray-500"><?= (int)$r['it_id'] ?></td>
          <td class="px-5 py-3">
            <img src="<?= h(image_url($r['it_img1'])) ?>"
                 class="w-12 h-12 rounded object-cover bg-gray-100">
          </td>
          <td class="px-5 py-3">
            <a href="/admin/product_form.php?it_id=<?= (int)$r['it_id'] ?>"
               class="font-semibold hover:text-primary line-clamp-1 max-w-xs">
              <?= h($r['it_name']) ?>
            </a>
            <div class="flex gap-1 mt-1">
              <?php if ($r['it_best']): ?><span class="px-1.5 py-0.5 text-[10px] rounded bg-primary text-accent font-bold">BEST</span><?php endif; ?>
              <?php if ($r['it_new']):  ?><span class="px-1.5 py-0.5 text-[10px] rounded bg-accent text-primary font-bold">NEW</span><?php endif; ?>
            </div>
          </td>
          <td class="px-5 py-3 text-xs text-gray-600"><?= h($r['ca_name']) ?></td>
          <td class="px-5 py-3 text-right text-gray-500"><?= number_format((int)$r['it_price']) ?></td>
          <td class="px-5 py-3 text-right font-bold text-primary">
            <?= $r['it_sell_price'] > 0 ? number_format((int)$r['it_sell_price']) : '-' ?>
          </td>
          <td class="px-5 py-3 text-right"><?= number_format((int)$r['it_stock']) ?></td>
          <td class="px-5 py-3 text-center">
            <?php if ($r['it_use']): ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-emerald-100 text-emerald-700 font-bold">판매중</span>
            <?php else: ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-500">중지</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 whitespace-nowrap">
            <a href="/shop/item.php?it_id=<?= (int)$r['it_id'] ?>" target="_blank"
               class="text-xs text-gray-500 hover:text-primary">보기</a>
            <span class="text-gray-300">|</span>
            <a href="/admin/product_form.php?it_id=<?= (int)$r['it_id'] ?>"
               class="text-xs text-primary font-semibold">수정</a>
            <span class="text-gray-300">|</span>
            <form method="post" class="inline" onsubmit="return confirm('정말 삭제하시겠습니까?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="it_id" value="<?= (int)$r['it_id'] ?>">
              <button class="text-xs text-red-500 hover:text-red-700">삭제</button>
            </form>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
