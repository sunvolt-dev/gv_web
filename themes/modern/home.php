<?php /* [modern 테마] 홈 — 미니멀, 여백, 대형 타이포 */ ?>

<!-- HERO — 첫 배너만, 크게 -->
<?php $hb = $banners[0] ?? null; ?>
<section class="max-w-6xl mx-auto px-6 pt-10 md:pt-16 pb-12 md:pb-20">
  <?php if ($hb): ?>
  <div class="relative rounded-2xl overflow-hidden bg-gray-100">
    <img src="<?= h($hb['image_url']) ?>" alt="<?= h($hb['title']) ?>"
         class="w-full aspect-[16/10] md:aspect-[21/9] object-cover">
    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-black/10 flex items-end">
      <div class="p-6 md:p-12 text-white max-w-2xl">
        <?php if ($hb['accent_label']): ?>
        <div class="text-xs font-bold tracking-[0.2em] text-white/70 mb-3 uppercase"><?= h($hb['accent_label']) ?></div>
        <?php endif; ?>
        <h1 class="text-3xl md:text-5xl font-extrabold leading-[1.1] tracking-tight mb-4"><?= h($hb['title']) ?></h1>
        <?php if ($hb['cta_text']): ?>
        <a href="<?= h($hb['cta_url'] ?: '#') ?>" class="inline-block mt-2 px-6 py-3 bg-white text-primary text-sm font-bold rounded-full hover:bg-accent hover:text-white transition">
          <?= h($hb['cta_text']) ?>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php else: ?>
  <h1 class="text-4xl md:text-6xl font-extrabold tracking-tight text-primary">Power, simplified.</h1>
  <?php endif; ?>
</section>

<!-- 카테고리 — 미니멀 텍스트 리스트 -->
<section class="max-w-6xl mx-auto px-6 pb-12 md:pb-20">
  <div class="flex items-baseline justify-between mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-primary">Categories</h2>
    <a href="/shop/list.php" class="text-xs font-semibold text-gray-400 hover:text-accent">VIEW ALL →</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-px bg-gray-200 border border-gray-200 rounded-xl overflow-hidden">
    <?php foreach ($top_categories as $c): ?>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
       class="bg-white p-6 md:p-8 hover:bg-gray-50 transition group">
      <div class="text-xs text-gray-400 mb-8 group-hover:text-accent">0<?= (int)($c['ca_order'] ?? 1) ?></div>
      <div class="font-bold text-primary group-hover:text-accent transition"><?= h($c['ca_name']) ?></div>
      <div class="text-xs text-gray-400 mt-1">바로가기 →</div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- BEST 상품 -->
<section class="max-w-6xl mx-auto px-6 pb-12 md:pb-20">
  <div class="flex items-baseline justify-between mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-primary">Best Sellers</h2>
    <a href="/shop/list.php" class="text-xs font-semibold text-gray-400 hover:text-accent">VIEW ALL →</a>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-10">
    <?php foreach ($best_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>

<!-- NEW 상품 -->
<?php if (!empty($new_products)): ?>
<section class="max-w-6xl mx-auto px-6 pb-12 md:pb-20">
  <div class="flex items-baseline justify-between mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-primary">New Arrivals</h2>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-10">
    <?php foreach ($new_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- 저널(블로그) -->
<?php if (!empty($recent_posts)): ?>
<section class="max-w-6xl mx-auto px-6 pb-16 md:pb-24">
  <div class="flex items-baseline justify-between mb-8 border-b border-gray-200 pb-4">
    <h2 class="text-xl md:text-2xl font-extrabold tracking-tight text-primary">Journal</h2>
    <a href="/blog/list.php" class="text-xs font-semibold text-gray-400 hover:text-accent">VIEW ALL →</a>
  </div>
  <div class="grid md:grid-cols-2 gap-8">
    <?php foreach ($recent_posts as $post):
      $img = $post['thumbnail'] ?: 'https://placehold.co/800x500/111827/2563EB?text=Journal'; ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $post['id']])) ?>" class="group">
      <div class="aspect-[16/10] rounded-xl overflow-hidden bg-gray-100 mb-4">
        <img src="<?= h($img) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
      </div>
      <div class="text-xs text-gray-400 mb-1"><?= h(date('Y.m.d', strtotime($post['created_at']))) ?></div>
      <h3 class="font-bold text-lg text-primary leading-snug group-hover:text-accent transition line-clamp-2"><?= h($post['title']) ?></h3>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
