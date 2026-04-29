<?php
require_once __DIR__ . '/../includes/functions.php';
cart_init();

$order = $_SESSION['last_order'] ?? null;
if (!$order) {
    header('Location: /');
    exit;
}

$page_title = '주문 완료';
require __DIR__ . '/../includes/header.php';
?>

<section class="max-w-2xl mx-auto px-4 py-12 md:py-20 text-center">

  <div class="w-20 h-20 mx-auto rounded-full bg-accent/20 flex items-center justify-center mb-6">
    <span class="text-5xl">🎉</span>
  </div>

  <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-2">
    주문이 완료되었습니다
  </h1>
  <p class="text-gray-500 mb-8">주문해 주셔서 감사합니다. (데모 — 실제 주문은 처리되지 않습니다)</p>

  <div class="bg-gray-50 border rounded-2xl p-6 md:p-8 text-left space-y-4 mb-8">
    <div class="flex justify-between">
      <span class="text-gray-500 text-sm">주문번호</span>
      <span class="font-extrabold text-primary text-lg">#<?= h($order['order_no']) ?></span>
    </div>
    <div class="flex justify-between">
      <span class="text-gray-500 text-sm">주문일시</span>
      <span class="font-semibold"><?= h($order['date']) ?></span>
    </div>
    <div class="flex justify-between">
      <span class="text-gray-500 text-sm">주문자</span>
      <span class="font-semibold"><?= h($order['name']) ?> · <?= h($order['phone']) ?></span>
    </div>
    <div class="flex justify-between">
      <span class="text-gray-500 text-sm">배송지</span>
      <span class="font-semibold text-right"><?= h($order['address']) ?></span>
    </div>
    <div class="border-t pt-4">
      <div class="text-sm text-gray-500 mb-2">주문 상품 (<?= count($order['items']) ?>)</div>
      <ul class="space-y-2">
        <?php foreach ($order['items'] as $i): ?>
        <li class="flex justify-between text-sm">
          <span class="line-clamp-1"><?= h($i['product']['it_name']) ?> × <?= $i['qty'] ?></span>
          <span class="font-bold whitespace-nowrap ml-3"><?= price($i['subtotal']) ?></span>
        </li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="border-t pt-4 flex justify-between items-baseline">
      <span class="font-bold">총 결제금액</span>
      <span class="text-2xl font-extrabold text-primary"><?= price($order['total']) ?></span>
    </div>
  </div>

  <div class="flex flex-col sm:flex-row justify-center gap-3">
    <a href="/" class="px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-light">
      메인으로
    </a>
    <a href="/shop/list.php" class="px-6 py-3 rounded-lg border border-gray-300 hover:bg-gray-50">
      쇼핑 계속하기
    </a>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
