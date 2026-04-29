<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = '장바구니';
$items = cart_items_detailed();
$total = cart_total();
$shipping = ($total > 0 && $total < 50000) ? 3000 : 0;
$grand = $total + $shipping;
$added = isset($_GET['added']);

require __DIR__ . '/../includes/header.php';
?>

<section class="max-w-5xl mx-auto px-4 py-8 md:py-12">

  <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-6">
    장바구니 <span class="text-gray-400 text-lg font-medium">(<?= count($items) ?>)</span>
  </h1>

  <?php if ($added): ?>
  <div class="mb-6 p-4 rounded-xl bg-accent/10 border border-accent text-primary text-sm font-semibold">
    ✅ 상품이 장바구니에 담겼습니다.
  </div>
  <?php endif; ?>

  <?php if (empty($items)): ?>
    <div class="py-20 text-center">
      <div class="text-6xl mb-4">🛒</div>
      <h2 class="text-lg font-bold text-gray-700 mb-2">장바구니가 비어있습니다</h2>
      <p class="text-sm text-gray-500 mb-6">관심있는 상품을 장바구니에 담아보세요.</p>
      <a href="/shop/list.php"
         class="inline-block px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-light">
        쇼핑 계속하기 →
      </a>
    </div>
  <?php else: ?>

  <div class="grid lg:grid-cols-[1fr_340px] gap-8">

    <!-- 아이템 리스트 -->
    <div class="space-y-3">
      <?php foreach ($items as $i):
        $p = $i['product']; $imgs = product_images($p);
      ?>
      <div class="flex gap-4 p-4 bg-white border rounded-xl">
        <a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>"
           class="shrink-0">
          <img src="<?= h($imgs[0]) ?>" alt=""
               class="w-20 h-20 md:w-24 md:h-24 object-cover rounded-lg bg-gray-50">
        </a>
        <div class="flex-1 min-w-0">
          <a href="<?= h(url('/shop/item.php', ['it_id' => $p['it_id']])) ?>"
             class="font-semibold text-sm md:text-base text-gray-900 line-clamp-2 hover:text-primary">
            <?= h($p['it_name']) ?>
          </a>
          <?php if ($i['option_text']): ?>
          <div class="text-xs text-gray-500 mt-1"><?= h($i['option_text']) ?></div>
          <?php endif; ?>

          <div class="mt-3 flex items-center justify-between gap-3 flex-wrap">
            <form method="post" action="/cart_action.php"
                  class="flex items-center border rounded-lg">
              <input type="hidden" name="action" value="update">
              <input type="hidden" name="key" value="<?= h($i['key']) ?>">
              <button type="submit" name="qty" value="<?= max(1, $i['qty'] - 1) ?>"
                      class="w-8 h-8 flex items-center justify-center hover:bg-gray-50">−</button>
              <span class="w-8 text-center text-sm font-bold"><?= $i['qty'] ?></span>
              <button type="submit" name="qty" value="<?= $i['qty'] + 1 ?>"
                      class="w-8 h-8 flex items-center justify-center hover:bg-gray-50">+</button>
            </form>
            <div class="text-base font-extrabold text-primary"><?= price($i['subtotal']) ?></div>
            <form method="post" action="/cart_action.php">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="key" value="<?= h($i['key']) ?>">
              <button type="submit" class="text-xs text-gray-400 hover:text-red-600">✕ 삭제</button>
            </form>
          </div>
        </div>
      </div>
      <?php endforeach; ?>

      <div class="text-right pt-2">
        <form method="post" action="/cart_action.php" class="inline">
          <input type="hidden" name="action" value="clear">
          <button class="text-xs text-gray-500 hover:text-red-600 underline">전체 비우기</button>
        </form>
      </div>
    </div>

    <!-- 결제 박스 -->
    <aside class="lg:sticky lg:top-28 self-start">
      <div class="bg-white border rounded-xl p-5 space-y-3">
        <h3 class="font-extrabold text-primary mb-2">주문 요약</h3>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">상품금액</span>
          <span class="font-semibold"><?= price($total) ?></span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">배송비</span>
          <span class="font-semibold">
            <?= $shipping === 0 ? '무료' : price($shipping) ?>
          </span>
        </div>
        <?php if ($shipping > 0): ?>
        <div class="text-[11px] text-accent-dark">
          5만원 이상 주문 시 무료배송 (<?= price(50000 - $total) ?> 추가 시)
        </div>
        <?php endif; ?>
        <div class="border-t pt-3 flex justify-between items-baseline">
          <span class="font-bold">총 결제금액</span>
          <span class="text-2xl font-extrabold text-primary"><?= price($grand) ?></span>
        </div>

        <a href="/checkout.php"
           class="block w-full text-center py-3 rounded-lg bg-primary text-white
                  font-bold hover:bg-primary-light transition mt-2">
          결제하기
        </a>
        <a href="/shop/list.php"
           class="block w-full text-center py-3 rounded-lg border border-gray-300
                  text-gray-700 hover:bg-gray-50 transition">
          쇼핑 계속하기
        </a>
      </div>
    </aside>
  </div>

  <?php endif; ?>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
