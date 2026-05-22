<?php
/**
 * [classic 테마] 상품 카드 — 입력: $p (products 행)
 */
$has_sale = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$final    = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$rate     = $has_sale ? discount_rate((int)$p['it_price'], (int)$p['it_sell_price']) : 0;
$cat      = get_category((int)$p['ca_id']);
$imgs     = product_images($p);
?>
<a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>"
   class="product-card group block bg-white rounded-xl overflow-hidden border border-gray-100">
  <div class="relative aspect-square bg-gray-50 overflow-hidden">
    <img src="<?= h($imgs[0]) ?>" alt="<?= h($p['it_name']) ?>"
         class="w-full h-full object-cover group-hover:scale-105 transition duration-300" loading="lazy">
    <div class="absolute top-2 left-2 flex flex-col gap-1">
      <?php if ($p['it_best']): ?><span class="px-2 py-0.5 rounded text-[10px] font-bold badge-best">BEST</span><?php endif; ?>
      <?php if ($p['it_new']): ?><span class="px-2 py-0.5 rounded text-[10px] font-bold badge-new">NEW</span><?php endif; ?>
    </div>
    <?php if ($rate > 0): ?>
    <div class="absolute top-2 right-2">
      <span class="px-2 py-0.5 rounded text-[11px] font-bold badge-sale">-<?= $rate ?>%</span>
    </div>
    <?php endif; ?>
  </div>
  <div class="p-3 md:p-4">
    <?php if ($cat): ?>
    <div class="text-[11px] text-gray-400 mb-1"><?= h($cat['ca_name']) ?></div>
    <?php endif; ?>
    <div class="font-semibold text-sm md:text-[15px] text-gray-900 line-clamp-2 leading-snug min-h-[2.6em] group-hover:text-primary transition">
      <?= h($p['it_name']) ?>
    </div>
    <div class="mt-2 flex items-baseline gap-2 flex-wrap">
      <?php if ($has_sale): ?>
      <span class="text-xs text-gray-400 line-through"><?= price((int)$p['it_price']) ?></span>
      <?php endif; ?>
      <span class="text-base md:text-lg font-extrabold text-primary"><?= price($final) ?></span>
    </div>
  </div>
</a>
