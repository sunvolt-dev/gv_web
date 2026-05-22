<?php /* [compact 테마] 홈 — 정보밀집, 촘촘한 그리드 */ ?>

<!-- HERO + 사이드 카테고리 (밀집형) -->
<section class="max-w-[1280px] mx-auto px-4 py-4">
  <div class="grid md:grid-cols-[200px_minmax(0,1fr)] gap-3">
    <!-- 사이드 카테고리 -->
    <aside class="hidden md:block border border-gray-200 bg-white self-start">
      <div class="bg-primary text-white text-xs font-bold px-3 py-2">전체 카테고리</div>
      <ul class="text-sm">
        <?php foreach ($top_categories as $c): ?>
        <li>
          <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
             class="block px-3 py-2 border-b font-semibold text-gray-700 hover:bg-accent hover:text-white"><?= h($c['ca_name']) ?></a>
        </li>
        <?php endforeach; ?>
        <li><a href="/cases/list.php" class="block px-3 py-2 border-b text-gray-600 hover:bg-gray-50">납품사례</a></li>
        <li><a href="/blog/list.php" class="block px-3 py-2 text-gray-600 hover:bg-gray-50">블로그</a></li>
      </ul>
    </aside>
    <!-- 메인 배너 (정적 — 첫 배너) -->
    <?php $hb = $banners[0] ?? null; if ($hb): ?>
    <a href="<?= h($hb['cta_url'] ?: '#') ?>" class="relative block min-w-0 border border-gray-200 overflow-hidden group">
      <img src="<?= h($hb['image_url']) ?>" alt="<?= h($hb['title']) ?>"
           class="w-full aspect-[21/9] object-cover group-hover:scale-105 transition duration-500">
      <div class="absolute inset-0 bg-gradient-to-r from-black/65 to-transparent flex items-center">
        <div class="px-6 md:px-10 text-white max-w-md">
          <?php if ($hb['accent_label']): ?>
          <div class="text-[11px] font-bold text-accent mb-1.5"><?= h($hb['accent_label']) ?></div>
          <?php endif; ?>
          <h1 class="text-xl md:text-3xl font-extrabold leading-tight"><?= h($hb['title']) ?></h1>
          <?php if ($hb['cta_text']): ?>
          <span class="inline-block mt-3 px-4 py-1.5 bg-accent text-white text-xs font-bold rounded"><?= h($hb['cta_text']) ?> →</span>
          <?php endif; ?>
        </div>
      </div>
    </a>
    <?php else: ?>
    <div class="min-w-0 border border-gray-200 bg-gray-50 flex items-center justify-center aspect-[21/9] text-gray-400 text-sm">
      관리자에서 배너를 등록하세요
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- 빠른 안내 바 -->
<section class="max-w-[1280px] mx-auto px-4 pb-4">
  <div class="grid grid-cols-4 gap-2 text-center text-xs">
    <?php foreach ([['🚚','무료배송'],['⏱️','당일발송'],['🛡️','정품보장'],['📞','전문상담']] as $b): ?>
    <div class="border border-gray-200 bg-white py-2 font-semibold text-gray-600"><?= $b[0] ?> <?= $b[1] ?></div>
    <?php endforeach; ?>
  </div>
</section>

<!-- BEST — 촘촘한 그리드 (최대 6열) -->
<section class="max-w-[1280px] mx-auto px-4 py-3">
  <div class="flex items-center justify-between bg-primary text-white px-3 py-2">
    <h2 class="font-bold text-sm">🔥 베스트 상품</h2>
    <a href="/shop/list.php" class="text-xs text-white/70 hover:text-accent">더보기 +</a>
  </div>
  <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-2">
    <?php foreach ($best_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>

<!-- NEW -->
<?php if (!empty($new_products)): ?>
<section class="max-w-[1280px] mx-auto px-4 py-3">
  <div class="flex items-center justify-between bg-accent text-white px-3 py-2">
    <h2 class="font-bold text-sm">⚡ 신상품</h2>
  </div>
  <div class="grid grid-cols-3 md:grid-cols-6 gap-2 mt-2">
    <?php foreach ($new_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- 블로그 — 컴팩트 리스트 -->
<?php if (!empty($recent_posts)): ?>
<section class="max-w-[1280px] mx-auto px-4 py-3 mb-6">
  <div class="bg-primary text-white px-3 py-2"><h2 class="font-bold text-sm">📋 배터리 정보</h2></div>
  <div class="grid md:grid-cols-2 gap-2 mt-2">
    <?php foreach ($recent_posts as $post):
      $img = $post['thumbnail'] ?: 'https://placehold.co/400x300/1F2937/0D9488?text=Info'; ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $post['id']])) ?>"
       class="flex gap-3 border border-gray-200 bg-white p-2 hover:border-accent transition">
      <img src="<?= h($img) ?>" class="w-20 h-16 object-cover shrink-0 bg-gray-100">
      <div class="min-w-0">
        <div class="text-[10px] text-gray-400"><?= h(date('Y.m.d', strtotime($post['created_at']))) ?></div>
        <h3 class="text-xs font-bold text-gray-800 line-clamp-2 leading-tight"><?= h($post['title']) ?></h3>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
