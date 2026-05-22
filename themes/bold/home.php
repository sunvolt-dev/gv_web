<?php /* [bold 테마] 홈 — 강렬, 대형 타이포, 풀블리드 */ ?>

<!-- HERO — 풀블리드 강렬 -->
<?php $hb = $banners[0] ?? null; ?>
<section class="relative bg-primary text-white overflow-hidden">
  <div class="absolute -right-16 -top-16 w-96 h-96 rounded-full bg-accent/20 blur-2xl"></div>
  <div class="max-w-7xl mx-auto px-5 py-14 md:py-24 grid md:grid-cols-2 items-center gap-8 relative">
    <div>
      <?php if ($hb && $hb['accent_label']): ?>
      <div class="inline-block bg-accent text-primary text-xs font-black px-3 py-1.5 rounded-full uppercase mb-5">
        <?= h($hb['accent_label']) ?>
      </div>
      <?php endif; ?>
      <h1 class="text-4xl md:text-6xl lg:text-7xl font-black leading-[0.95] tracking-tighter mb-5">
        <?= h($hb['title'] ?? '파워풀한 배터리') ?>
      </h1>
      <?php if ($hb && $hb['subtitle']): ?>
      <p class="text-lg text-white/80 mb-7 whitespace-pre-line font-medium"><?= h($hb['subtitle']) ?></p>
      <?php endif; ?>
      <a href="<?= h($hb['cta_url'] ?? '/shop/list.php') ?>"
         class="inline-block bg-accent text-primary text-lg font-black px-9 py-4 rounded-xl
                hover:translate-y-[-2px] hover:shadow-[5px_5px_0_0_#FACC15] transition uppercase">
        <?= h($hb['cta_text'] ?? '쇼핑하기') ?> →
      </a>
    </div>
    <?php if ($hb): ?>
    <div class="relative">
      <img src="<?= h($hb['image_url']) ?>" alt="<?= h($hb['title']) ?>"
           class="w-full aspect-square object-cover rounded-3xl border-4 border-accent rotate-2">
    </div>
    <?php endif; ?>
  </div>
</section>

<!-- 카테고리 — 큰 컬러 블록 -->
<section class="max-w-7xl mx-auto px-5 py-12 md:py-16">
  <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-primary mb-8">CATEGORY</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
    <?php
    $bold_bg = ['bg-violet-600','bg-fuchsia-600','bg-indigo-600','bg-orange-500'];
    foreach ($top_categories as $i => $c): ?>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
       class="<?= $bold_bg[$i % 4] ?> text-white rounded-2xl p-6 md:p-8 aspect-[4/3]
              flex flex-col justify-between hover:-translate-y-1 transition">
      <div class="text-4xl font-black opacity-30">0<?= $i + 1 ?></div>
      <div>
        <div class="text-xl md:text-2xl font-black leading-tight"><?= h($c['ca_name']) ?></div>
        <div class="text-sm font-bold opacity-70 mt-1">SHOP NOW →</div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- BEST -->
<section class="bg-gray-900 py-12 md:py-16">
  <div class="max-w-7xl mx-auto px-5">
    <div class="flex items-end justify-between mb-8">
      <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-white">🔥 BEST</h2>
      <a href="/shop/list.php" class="text-accent font-black hover:underline">전체보기 →</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
      <?php foreach ($best_products as $p): include theme_file('product_card.php'); endforeach; ?>
    </div>
  </div>
</section>

<!-- NEW -->
<?php if (!empty($new_products)): ?>
<section class="max-w-7xl mx-auto px-5 py-12 md:py-16">
  <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-primary mb-8">⚡ NEW DROP</h2>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ($new_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- 블로그 -->
<?php if (!empty($recent_posts)): ?>
<section class="max-w-7xl mx-auto px-5 pb-14">
  <h2 class="text-3xl md:text-5xl font-black tracking-tighter text-primary mb-8">STORIES</h2>
  <div class="grid md:grid-cols-2 gap-5">
    <?php foreach ($recent_posts as $post):
      $img = $post['thumbnail'] ?: 'https://placehold.co/800x500/7C3AED/FACC15?text=Story'; ?>
    <a href="<?= h(url('/blog/view.php', ['id' => $post['id']])) ?>"
       class="group block rounded-2xl overflow-hidden border-2 border-gray-900 hover:-translate-y-1 hover:shadow-[6px_6px_0_0_#7C3AED] transition">
      <div class="aspect-[16/9] overflow-hidden">
        <img src="<?= h($img) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
      </div>
      <div class="p-5">
        <div class="text-xs font-black text-accent-dark mb-1"><?= h(date('Y.m.d', strtotime($post['created_at']))) ?></div>
        <h3 class="font-black text-lg text-primary leading-snug line-clamp-2"><?= h($post['title']) ?></h3>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>
