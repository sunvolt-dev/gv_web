<?php
$admin_page = 'dashboard';
$admin_title = '대시보드';
require __DIR__ . '/_layout_top.php';

$cnt_products   = (int)db_one('SELECT COUNT(*) c FROM products')['c'];
$cnt_active     = (int)db_one('SELECT COUNT(*) c FROM products WHERE it_use = 1')['c'];
$cnt_categories = (int)db_one('SELECT COUNT(*) c FROM categories')['c'];
$cnt_options    = (int)db_one('SELECT COUNT(*) c FROM product_options')['c'];

$recent = db_all('SELECT * FROM products ORDER BY created_at DESC LIMIT 5');
?>

<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <?php
  $cards = [
    ['전체 상품', $cnt_products, '📦', 'bg-primary text-white'],
    ['판매중',     $cnt_active,    '✅', 'bg-emerald-600 text-white'],
    ['카테고리',   $cnt_categories,'🗂️', 'bg-orange-500 text-white'],
    ['옵션',       $cnt_options,   '⚙️', 'bg-purple-600 text-white'],
  ];
  foreach ($cards as $c):
  ?>
  <div class="bg-white rounded-xl p-5 border">
    <div class="flex items-center justify-between mb-2">
      <span class="text-xs font-semibold text-gray-500"><?= h($c[0]) ?></span>
      <span class="text-2xl"><?= $c[2] ?></span>
    </div>
    <div class="text-3xl font-extrabold text-primary"><?= number_format($c[1]) ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="bg-white rounded-xl border overflow-hidden">
  <div class="px-5 py-4 border-b flex items-center justify-between">
    <h2 class="font-extrabold text-primary">최근 등록 상품</h2>
    <a href="/admin/products.php" class="text-xs text-primary hover:text-accent-dark">전체보기 →</a>
  </div>
  <table class="w-full text-sm">
    <thead class="bg-gray-50 text-xs text-gray-500">
      <tr>
        <th class="text-left px-5 py-3">ID</th>
        <th class="text-left px-5 py-3">상품명</th>
        <th class="text-right px-5 py-3">가격</th>
        <th class="text-right px-5 py-3">재고</th>
        <th class="text-left px-5 py-3">등록일</th>
      </tr>
    </thead>
    <tbody class="divide-y">
      <?php foreach ($recent as $r): ?>
      <tr class="hover:bg-gray-50">
        <td class="px-5 py-3 text-gray-500"><?= (int)$r['it_id'] ?></td>
        <td class="px-5 py-3">
          <a href="/admin/product_form.php?it_id=<?= (int)$r['it_id'] ?>"
             class="font-semibold hover:text-primary"><?= h($r['it_name']) ?></a>
        </td>
        <td class="px-5 py-3 text-right">
          <?= price($r['it_sell_price'] > 0 ? (int)$r['it_sell_price'] : (int)$r['it_price']) ?>
        </td>
        <td class="px-5 py-3 text-right"><?= number_format((int)$r['it_stock']) ?></td>
        <td class="px-5 py-3 text-xs text-gray-500"><?= h($r['created_at']) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
