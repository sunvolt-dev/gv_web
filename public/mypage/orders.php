<?php
require_once __DIR__ . '/../../includes/user_auth.php';
require_login();

$u = user();
$orders = db_all(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC',
    [$u['id']]
);

// 각 주문의 아이템도 같이
foreach ($orders as &$o) {
    $o['items'] = db_all('SELECT * FROM order_items WHERE order_id = ?', [$o['id']]);
}
unset($o);

$page_title = '주문 내역';
require __DIR__ . '/../../includes/header.php';
?>

<section class="max-w-5xl mx-auto px-4 py-8 md:py-12">
  <div class="grid lg:grid-cols-[260px_1fr] gap-6">

    <aside class="bg-white border rounded-xl overflow-hidden">
      <div class="bg-primary text-white p-5">
        <div class="text-xs text-white/60 mb-1">안녕하세요</div>
        <div class="font-extrabold text-lg"><?= h($u['name']) ?>님</div>
      </div>
      <nav class="p-2 text-sm">
        <a href="/mypage/" class="block px-3 py-2 rounded-lg hover:bg-gray-50">📊 마이홈</a>
        <a href="/mypage/orders.php" class="block px-3 py-2 rounded-lg bg-gray-100 font-bold text-primary">📦 주문 내역</a>
        <a href="/mypage/edit.php" class="block px-3 py-2 rounded-lg hover:bg-gray-50">✏️ 회원정보 수정</a>
        <a href="/auth/logout.php" class="block px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 mt-2 border-t pt-3">🚪 로그아웃</a>
      </nav>
    </aside>

    <main>
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-6">주문 내역 (<?= count($orders) ?>)</h1>

      <?php if (empty($orders)): ?>
      <div class="py-20 text-center bg-white border rounded-xl">
        <div class="text-6xl mb-4">📦</div>
        <p class="text-gray-500 mb-6">아직 주문 내역이 없습니다.</p>
        <a href="/shop/list.php" class="inline-block px-6 py-3 rounded-lg bg-primary text-white font-bold">
          쇼핑하러 가기 →
        </a>
      </div>
      <?php else: ?>
      <div class="space-y-4">
        <?php foreach ($orders as $o): ?>
        <div class="bg-white border rounded-xl overflow-hidden">
          <div class="px-5 py-3 bg-gray-50 border-b flex flex-wrap items-center justify-between gap-3">
            <div>
              <div class="font-extrabold text-primary">주문번호 #<?= h($o['order_no']) ?></div>
              <div class="text-xs text-gray-500"><?= h(date('Y.m.d H:i', strtotime($o['created_at']))) ?></div>
            </div>
            <span class="px-3 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-800">
              <?= h($o['status']) ?>
            </span>
          </div>
          <div class="divide-y">
            <?php foreach ($o['items'] as $it): ?>
            <div class="px-5 py-3 flex items-center justify-between text-sm">
              <div class="flex-1">
                <div class="font-semibold"><?= h($it['it_name']) ?></div>
                <?php if ($it['option_text']): ?>
                <div class="text-xs text-gray-500"><?= h($it['option_text']) ?></div>
                <?php endif; ?>
                <div class="text-xs text-gray-500">수량 <?= (int)$it['qty'] ?></div>
              </div>
              <div class="font-bold text-primary"><?= price((int)$it['subtotal']) ?></div>
            </div>
            <?php endforeach; ?>
          </div>
          <div class="px-5 py-3 bg-gray-50 border-t flex items-center justify-between">
            <span class="text-xs text-gray-500"><?= h($o['recv_address']) ?></span>
            <div class="flex items-baseline gap-2">
              <span class="text-xs text-gray-500">총 결제금액</span>
              <span class="text-xl font-extrabold text-primary"><?= price((int)$o['total_amount']) ?></span>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </main>
  </div>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
