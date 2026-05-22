<?php
/* [compact 테마] 상품 카드 — 작고 촘촘, 정보 위주 */
$has_sale = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$final    = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$rate     = $has_sale ? discount_rate((int)$p['it_price'], (int)$p['it_sell_price']) : 0;
$imgs     = product_images($p);
?>
<a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>"
   class="group block bg-white border border-gray-200 hover:border-accent hover:shadow transition">
  <div class="relative aspect-square bg-gray-50 overflow-hidden">
    <img src="<?= h($imgs[0]) ?>" alt="<?= h($p['it_name']) ?>" loading="lazy"
         class="w-full h-full object-cover group-hover:scale-105 transition duration-200">
    <?php if ($rate > 0): ?>
    <span class="absolute top-1 right-1 px-1.5 py-0.5 bg-accent text-white text-[10px] font-bold">-<?= $rate ?>%</span>
    <?php endif; ?>
    <?php if ($p['it_best']): ?>
    <span class="absolute top-1 left-1 px-1.5 py-0.5 bg-primary text-white text-[10px] font-bold">BEST</span>
    <?php endif; ?>
  </div>
  <div class="p-2">
    <div class="text-xs text-gray-800 line-clamp-2 leading-tight min-h-[2.4em] group-hover:text-accent"><?= h($p['it_name']) ?></div>
    <div class="mt-1 flex items-baseline gap-1">
      <span class="text-sm font-extrabold text-primary"><?= number_format($final) ?></span><span class="text-[10px] text-gray-500">원</span>
      <?php if ($has_sale): ?>
      <span class="text-[10px] text-gray-400 line-through ml-auto"><?= number_format((int)$p['it_price']) ?></span>
      <?php endif; ?>
    </div>
    <div class="mt-1 text-[10px] text-gray-400">재고 <?= number_format((int)$p['it_stock']) ?></div>
  </div>
</a>
