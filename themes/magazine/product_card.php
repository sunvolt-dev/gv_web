<?php
/* [magazine 테마] 상품 카드 — 에디토리얼, 큰 이미지 + 하단 텍스트 */
$has_sale = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$final    = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$cat      = get_category((int)$p['ca_id']);
$imgs     = product_images($p);
?>
<a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>" class="group block">
  <div class="relative aspect-[4/5] overflow-hidden bg-gray-100">
    <img src="<?= h($imgs[0]) ?>" alt="<?= h($p['it_name']) ?>" loading="lazy"
         class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
    <?php if ($p['it_best']): ?>
    <span class="absolute top-3 left-3 bg-primary text-white text-[10px] font-bold tracking-widest px-2 py-1">BEST</span>
    <?php endif; ?>
  </div>
  <div class="pt-3 text-center">
    <?php if ($cat): ?>
    <div class="text-[10px] tracking-[0.2em] text-accent uppercase mb-1"><?= h($cat['ca_name']) ?></div>
    <?php endif; ?>
    <div class="font-bold text-[15px] text-primary leading-snug line-clamp-2 group-hover:text-accent transition"><?= h($p['it_name']) ?></div>
    <div class="mt-1.5 text-sm">
      <?php if ($has_sale): ?><span class="text-gray-300 line-through text-xs mr-1"><?= price((int)$p['it_price']) ?></span><?php endif; ?>
      <span class="font-extrabold text-primary"><?= price($final) ?></span>
    </div>
  </div>
</a>
