<?php
/* [modern 테마] 상품 카드 — 보더리스 미니멀 */
$has_sale = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$final    = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$rate     = $has_sale ? discount_rate((int)$p['it_price'], (int)$p['it_sell_price']) : 0;
$imgs     = product_images($p);
?>
<a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>" class="group block">
  <div class="relative aspect-square bg-gray-100 overflow-hidden rounded-lg">
    <img src="<?= h($imgs[0]) ?>" alt="<?= h($p['it_name']) ?>" loading="lazy"
         class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
    <?php if ($rate > 0): ?>
    <span class="absolute top-3 left-3 text-[11px] font-bold text-accent">-<?= $rate ?>%</span>
    <?php endif; ?>
  </div>
  <div class="pt-3">
    <div class="text-sm text-gray-800 line-clamp-1 group-hover:text-accent transition"><?= h($p['it_name']) ?></div>
    <div class="mt-1.5 flex items-baseline gap-2">
      <span class="font-extrabold text-primary"><?= price($final) ?></span>
      <?php if ($has_sale): ?>
      <span class="text-xs text-gray-300 line-through"><?= price((int)$p['it_price']) ?></span>
      <?php endif; ?>
    </div>
  </div>
</a>
