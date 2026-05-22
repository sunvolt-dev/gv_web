<?php /* [compact 테마] 홈 — 정보밀집, 촘촘한 그리드 */ ?>

<!-- HERO + 사이드 카테고리 (밀집형) -->
<section class="max-w-[1280px] mx-auto px-4 py-4">
  <div class="grid md:grid-cols-[200px_1fr] gap-3">
    <!-- 사이드 카테고리 -->
    <aside class="hidden md:block border border-gray-200 bg-white">
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
    <!-- 배너 슬라이더 -->
    <?php if (!empty($banners)): ?>
    <div class="swiper hero-swiper border border-gray-200">
      <div class="swiper-wrapper">
        <?php foreach ($banners as $b): ?>
        <div class="swiper-slide relative">
          <img src="<?= h($b['image_url']) ?>" alt="<?= h($b['title']) ?>" class="w-full aspect-[21/9] object-cover">
          <div class="absolute inset-0 bg-gradient-to-r from-black/60 to-transparent flex items-center">
            <div class="px-6 md:px-10 text-white max-w-md">
              <h1 class="text-xl md:text-3xl font-extrabold leading-tight"><?= h($b['title']) ?></h1>
              <?php if ($b['cta_text']): ?>
              <a href="<?= h($b['cta_url'] ?: '#') ?>" class="inline-block mt-3 px-4 py-1.5 bg-accent text-white text-xs font-bold rounded"><?= h($b['cta_text']) ?> →</a>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <div class="swiper-pagination"></div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
        new Swiper('.hero-swiper', { loop: true, autoplay: { delay: 4500 }, pagination: { el: '.hero-swiper .swiper-pagination', clickable: true } });
      });
    </script>
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
