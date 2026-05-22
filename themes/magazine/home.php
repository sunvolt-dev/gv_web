<?php /* [magazine 테마] 홈 — 에디토리얼 매거진 레이아웃 */ ?>

<!-- HERO — 매거진 커버 (첫 배너 大 + 나머지 사이드) -->
<section class="max-w-5xl mx-auto px-6 pt-10 pb-14">
  <?php if (!empty($banners)): $main = $banners[0]; $subs = array_slice($banners, 1, 2); ?>
  <div class="grid md:grid-cols-3 gap-5">
    <a href="<?= h($main['cta_url'] ?: '#') ?>" class="md:col-span-2 group relative block overflow-hidden">
      <img src="<?= h($main['image_url']) ?>" alt="<?= h($main['title']) ?>"
           class="w-full aspect-[3/2] object-cover group-hover:scale-105 transition duration-700">
      <div class="absolute inset-0 bg-gradient-to-t from-black/75 to-transparent flex items-end p-6 md:p-8">
        <div class="text-white">
          <?php if ($main['accent_label']): ?>
          <div class="text-[11px] tracking-[0.25em] uppercase text-accent mb-2 font-bold"><?= h($main['accent_label']) ?></div>
          <?php endif; ?>
          <h1 class="text-2xl md:text-4xl font-extrabold leading-tight"><?= h($main['title']) ?></h1>
        </div>
      </div>
    </a>
    <div class="flex flex-col gap-5">
      <?php foreach ($subs as $s): ?>
      <a href="<?= h($s['cta_url'] ?: '#') ?>" class="group relative block overflow-hidden flex-1">
        <img src="<?= h($s['image_url']) ?>" alt="<?= h($s['title']) ?>"
             class="w-full h-full min-h-[140px] object-cover group-hover:scale-105 transition duration-700">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex items-end p-4">
          <h2 class="text-white font-bold text-sm leading-snug line-clamp-2"><?= h($s['title']) ?></h2>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</section>

<!-- 섹션 헤더 헬퍼 스타일 -->
<?php $sec_head = function($kicker, $title) { ?>
  <div class="text-center mb-8">
    <div class="text-[11px] tracking-[0.3em] uppercase text-accent font-bold mb-2"><?= $kicker ?></div>
    <h2 class="text-2xl md:text-3xl font-extrabold text-primary inline-block relative px-6">
      <span class="absolute left-0 top-1/2 w-4 h-px bg-primary"></span>
      <?= $title ?>
      <span class="absolute right-0 top-1/2 w-4 h-px bg-primary"></span>
    </h2>
  </div>
<?php }; ?>

<!-- 카테고리 -->
<section class="max-w-5xl mx-auto px-6 py-12 border-t border-gray-200">
  <?php $sec_head('Explore', '카테고리'); ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php foreach ($top_categories as $c): ?>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
       class="border border-gray-200 p-6 text-center hover:border-accent hover:bg-orange-50/40 transition group">
      <div class="font-bold text-primary group-hover:text-accent"><?= h($c['ca_name']) ?></div>
      <div class="text-[11px] text-gray-400 mt-1 tracking-widest">VIEW →</div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- BEST 상품 -->
<section class="max-w-5xl mx-auto px-6 py-12 border-t border-gray-200">
  <?php $sec_head('Editors Pick', '베스트 셀렉션'); ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-9">
    <?php foreach ($best_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>

<!-- JOURNAL (블로그) — 매거진 핵심 -->
<?php if (!empty($recent_posts)): ?>
<section class="bg-primary text-white py-14">
  <div class="max-w-5xl mx-auto px-6">
    <div class="text-center mb-10">
      <div class="text-[11px] tracking-[0.3em] uppercase text-accent font-bold mb-2">Journal</div>
      <h2 class="text-2xl md:text-3xl font-extrabold">배터리 이야기</h2>
    </div>
    <div class="grid md:grid-cols-2 gap-8">
      <?php foreach ($recent_posts as $post):
        $img = $post['thumbnail'] ?: 'https://placehold.co/800x500/14532D/C2410C?text=Journal'; ?>
      <a href="<?= h(url('/blog/view.php', ['id' => $post['id']])) ?>" class="group">
        <div class="aspect-[16/9] overflow-hidden mb-4">
          <img src="<?= h($img) ?>" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
        </div>
        <div class="text-[11px] tracking-widest text-accent uppercase mb-1"><?= h(date('Y.m.d', strtotime($post['created_at']))) ?></div>
        <h3 class="font-bold text-xl leading-snug group-hover:text-accent transition line-clamp-2"><?= h($post['title']) ?></h3>
        <?php if ($post['summary']): ?>
        <p class="text-sm text-white/60 mt-2 line-clamp-2"><?= h($post['summary']) ?></p>
        <?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- NEW -->
<?php if (!empty($new_products)): ?>
<section class="max-w-5xl mx-auto px-6 py-12">
  <?php $sec_head('Just In', '신상품'); ?>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-x-5 gap-y-9">
    <?php foreach ($new_products as $p): include theme_file('product_card.php'); endforeach; ?>
  </div>
</section>
<?php endif; ?>
