<?php
require_once __DIR__ . '/../../includes/functions.php';

$cfg = require __DIR__ . '/../../includes/config.php';
$per_page = (int)$cfg['paging']['per_page'];

$ca_id = isset($_GET['ca_id']) ? (int)$_GET['ca_id'] : 0;
$sort  = $_GET['sort'] ?? 'best';
$page  = max(1, (int)($_GET['page'] ?? 1));
$q     = trim((string)($_GET['q'] ?? ''));

$category = $ca_id ? get_category($ca_id) : null;
$page_title = $category ? $category['ca_name'] : ($q ? "'{$q}' 검색결과" : '전체 상품');

[$items, $total] = list_products($ca_id ?: null, $sort, $page, $per_page);

// 검색어 필터 (간단히 메모리에서)
if ($q !== '') {
    $items = array_values(array_filter($items, fn($p) =>
        mb_stripos($p['it_name'], $q) !== false ||
        mb_stripos((string)$p['it_summary'], $q) !== false
    ));
    $total = count($items);
}

$pg = pagination_links($page, $total, $per_page);
$breadcrumb = $category ? category_breadcrumb($ca_id) : [];

$tree = category_tree();
$root_categories = $tree[0] ?? [];

require __DIR__ . '/../../includes/header.php';
?>

<!-- 빵부스러기 -->
<nav class="bg-gray-50 border-b">
  <div class="max-w-7xl mx-auto px-4 py-3 text-xs text-gray-600 flex items-center gap-1.5 flex-wrap">
    <a href="/" class="hover:text-primary">HOME</a>
    <span>›</span>
    <a href="<?= h(url('/shop/list.php')) ?>" class="hover:text-primary">전체상품</a>
    <?php foreach ($breadcrumb as $b): ?>
    <span>›</span>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $b['ca_id']])) ?>"
       class="hover:text-primary <?= $b['ca_id'] == $ca_id ? 'text-primary font-bold' : '' ?>">
      <?= h($b['ca_name']) ?>
    </a>
    <?php endforeach; ?>
  </div>
</nav>

<div class="max-w-7xl mx-auto px-4 py-6 md:py-10 grid lg:grid-cols-[240px_1fr] gap-8">

  <!-- ───────── 사이드바 ───────── -->
  <aside class="hidden lg:block">
    <h3 class="font-extrabold text-primary text-lg mb-4">카테고리</h3>
    <ul class="space-y-1">
      <li>
        <a href="<?= h(url('/shop/list.php')) ?>"
           class="block px-3 py-2 rounded-lg text-sm font-medium
                  <?= !$ca_id ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>">
          전체 상품
        </a>
      </li>
      <?php foreach ($root_categories as $c): ?>
        <?php $is_active_root = (int)$c['ca_id'] === $ca_id; ?>
        <li class="pt-2">
          <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
             class="block px-3 py-2 rounded-lg text-sm font-bold
                    <?= $is_active_root ? 'bg-primary text-white' : 'text-primary hover:bg-gray-100' ?>">
            <?= h($c['ca_name']) ?>
          </a>
          <?php $children = $tree[$c['ca_id']] ?? []; ?>
          <?php if ($children): ?>
          <ul class="ml-2 mt-1 space-y-0.5 border-l-2 border-gray-100 pl-3">
            <?php foreach ($children as $sub): ?>
              <?php $is_active = (int)$sub['ca_id'] === $ca_id; ?>
              <li>
                <a href="<?= h(url('/shop/list.php', ['ca_id' => $sub['ca_id']])) ?>"
                   class="block py-1.5 text-sm
                          <?= $is_active ? 'text-accent-dark font-bold' : 'text-gray-600 hover:text-primary' ?>">
                  <?= h($sub['ca_name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>
  </aside>

  <!-- ───────── 본문 ───────── -->
  <main>
    <div class="mb-6">
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary">
        <?= h($page_title) ?>
      </h1>
      <p class="text-sm text-gray-500 mt-1">
        총 <span class="font-bold text-primary"><?= number_format($total) ?></span>개 상품
      </p>
    </div>

    <!-- 정렬 / 필터 바 -->
    <div class="flex items-center justify-between gap-3 mb-5 pb-4 border-b">
      <!-- 모바일 카테고리 버튼 (Alpine sheet) -->
      <div class="lg:hidden" x-data="{ open: false }">
        <button @click="open = true"
                class="inline-flex items-center gap-2 px-3 py-2 rounded-lg
                       bg-primary text-white text-sm font-semibold">
          ☰ 카테고리
        </button>
        <div x-show="open" x-cloak class="fixed inset-0 z-50">
          <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
          <div class="absolute left-0 top-0 bottom-0 w-72 bg-white p-5 overflow-y-auto"
               x-transition:enter="transition" x-transition:enter-start="-translate-x-full"
               x-transition:enter-end="translate-x-0">
            <div class="flex items-center justify-between mb-4">
              <h3 class="font-bold text-primary">카테고리</h3>
              <button @click="open = false">✕</button>
            </div>
            <ul class="space-y-1">
              <li><a href="<?= h(url('/shop/list.php')) ?>" class="block py-2 font-semibold">전체 상품</a></li>
              <?php foreach ($root_categories as $c): ?>
                <li class="pt-2 border-t">
                  <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
                     class="block py-2 font-bold text-primary">
                    <?= h($c['ca_name']) ?>
                  </a>
                  <?php foreach ($tree[$c['ca_id']] ?? [] as $sub): ?>
                  <a href="<?= h(url('/shop/list.php', ['ca_id' => $sub['ca_id']])) ?>"
                     class="block pl-3 py-1.5 text-sm text-gray-600">└ <?= h($sub['ca_name']) ?></a>
                  <?php endforeach; ?>
                </li>
              <?php endforeach; ?>
            </ul>
          </div>
        </div>
      </div>

      <!-- 정렬 -->
      <form method="get" class="flex items-center gap-2 ml-auto">
        <?php foreach (['ca_id', 'q'] as $k): if (!empty($_GET[$k])): ?>
          <input type="hidden" name="<?= $k ?>" value="<?= h((string)$_GET[$k]) ?>">
        <?php endif; endforeach; ?>
        <label class="text-xs text-gray-500">정렬</label>
        <select name="sort" onchange="this.form.submit()"
                class="text-sm border border-gray-300 rounded-md px-2 py-1.5
                       focus:outline-none focus:ring-2 focus:ring-accent">
          <option value="best"       <?= $sort==='best'?'selected':'' ?>>인기순</option>
          <option value="new"        <?= $sort==='new'?'selected':'' ?>>최신순</option>
          <option value="price_asc"  <?= $sort==='price_asc'?'selected':'' ?>>낮은가격순</option>
          <option value="price_desc" <?= $sort==='price_desc'?'selected':'' ?>>높은가격순</option>
        </select>
      </form>
    </div>

    <!-- 상품 그리드 -->
    <?php if (empty($items)): ?>
      <div class="py-20 text-center text-gray-500">
        <div class="text-6xl mb-4">🔋</div>
        <p>해당 조건의 상품이 없습니다.</p>
      </div>
    <?php else: ?>
      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
        <?php foreach ($items as $p): ?>
          <?php include __DIR__ . '/../_product_card.php'; ?>
        <?php endforeach; ?>
      </div>

      <!-- 페이지네이션 -->
      <?php if ($pg['pages'] > 1): ?>
      <nav class="mt-10 flex items-center justify-center gap-1 text-sm">
        <?php if ($page > 1): ?>
        <a href="<?= h(current_url_with(['page' => $page - 1])) ?>"
           class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-50">‹</a>
        <?php endif; ?>
        <?php for ($i = $pg['start']; $i <= $pg['end']; $i++): ?>
          <a href="<?= h(current_url_with(['page' => $i])) ?>"
             class="w-9 h-9 flex items-center justify-center rounded-lg border
                    <?= $i == $page ? 'bg-primary text-white border-primary font-bold' : 'hover:bg-gray-50' ?>">
            <?= $i ?>
          </a>
        <?php endfor; ?>
        <?php if ($page < $pg['pages']): ?>
        <a href="<?= h(current_url_with(['page' => $page + 1])) ?>"
           class="w-9 h-9 flex items-center justify-center rounded-lg border hover:bg-gray-50">›</a>
        <?php endif; ?>
      </nav>
      <?php endif; ?>
    <?php endif; ?>
  </main>
</div>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
