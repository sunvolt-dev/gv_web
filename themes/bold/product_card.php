<?php
/* [bold 테마] 상품 카드 — 굵고 강렬, 큰 가격 */
$has_sale = $p['it_sell_price'] > 0 && $p['it_sell_price'] < $p['it_price'];
$final    = $has_sale ? (int)$p['it_sell_price'] : (int)$p['it_price'];
$rate     = $has_sale ? discount_rate((int)$p['it_price'], (int)$p['it_sell_price']) : 0;
$imgs     = product_images($p);
?>
<a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>"
   class="group block bg-white rounded-2xl overflow-hidden border-2 border-gray-900
          hover:-translate-y-1 hover:shadow-[6px_6px_0_0_#7C3AED] transition">
  <div class="relative aspect-square bg-gray-100 overflow-hidden">
    <img src="<?= h($imgs[0]) ?>" alt="<?= h($p['it_name']) ?>" loading="lazy"
         class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
    <div class="absolute top-2 left-2 flex flex-col gap-1">
      <?php if ($p['it_best']): ?><span class="px-2 py-1 rounded bg-primary text-white text-[10px] font-black">BEST</span><?php endif; ?>
      <?php if ($p['it_new']): ?><span class="px-2 py-1 rounded bg-accent text-primary text-[10px] font-black">NEW</span><?php endif; ?>
    </div>
    <?php if ($rate > 0): ?>
    <span class="absolute top-2 right-2 px-2 py-1 rounded bg-red-600 text-white text-xs font-black">-<?= $rate ?>%</span>
    <?php endif; ?>
  </div>
  <div class="p-4">
    <div class="font-bold text-sm text-gray-900 line-clamp-2 leading-snug min-h-[2.6em]"><?= h($p['it_name']) ?></div>
    <div class="mt-2 flex items-baseline gap-1.5">
      <span class="text-xl font-black text-primary"><?= price($final) ?></span>
      <?php if ($has_sale): ?><span class="text-xs text-gray-400 line-through"><?= price((int)$p['it_price']) ?></span><?php endif; ?>
    </div>
  </div>
</a>
