<?php
require_once __DIR__ . '/../includes/user_auth.php';

$page_title = '주문/결제';
$items = cart_items_detailed();
$total = cart_total();
$shipping = ($total > 0 && $total < 50000) ? 3000 : 0;
$grand = $total + $shipping;
$current_user = user();

// 빈 장바구니 → 메인으로
if (empty($items)) {
    header('Location: /cart.php');
    exit;
}

// 주문하기 처리 (회원/비회원 모두 DB 저장)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_no = date('Ymd') . '-' . str_pad((string)random_int(1, 9999), 4, '0', STR_PAD_LEFT);
    $name     = trim((string)($_POST['name']    ?? ''));
    $phone    = trim((string)($_POST['phone']   ?? ''));
    $address  = trim((string)($_POST['address'] ?? ''));
    $memo     = trim((string)($_POST['memo']    ?? ''));
    $pay      = trim((string)($_POST['pay']     ?? ''));

    db()->beginTransaction();
    try {
        db_exec(
            'INSERT INTO orders
              (order_no, user_id, recv_name, recv_phone, recv_address, memo, pay_method, total_amount, status)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
            [$order_no, $current_user['id'] ?? null, $name, $phone, $address, $memo, $pay, $grand, '결제완료']
        );
        $order_id = (int)db()->lastInsertId();

        foreach ($items as $i) {
            db_exec(
                'INSERT INTO order_items
                  (order_id, it_id, it_name, option_text, unit_price, qty, subtotal)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [
                    $order_id, (int)$i['product']['it_id'], $i['product']['it_name'],
                    $i['option_text'] ?: null, $i['unit_price'], $i['qty'], $i['subtotal'],
                ]
            );
        }
        db()->commit();
    } catch (Throwable $e) {
        db()->rollBack();
        throw $e;
    }

    $_SESSION['last_order'] = [
        'order_no' => $order_no,
        'items'    => $items,
        'total'    => $grand,
        'name'     => $name,
        'phone'    => $phone,
        'address'  => $address,
        'date'     => date('Y-m-d H:i'),
    ];
    $_SESSION['cart'] = [];
    header('Location: /checkout_complete.php');
    exit;
}

require __DIR__ . '/../includes/header.php';
?>

<section class="max-w-5xl mx-auto px-4 py-8 md:py-12">
  <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-8">주문/결제</h1>

  <form method="post" class="grid lg:grid-cols-[1fr_340px] gap-8">

    <div class="space-y-8">

      <!-- 주문 상품 -->
      <section>
        <h2 class="font-extrabold text-primary mb-3">주문 상품 (<?= count($items) ?>)</h2>
        <div class="border rounded-xl divide-y">
          <?php foreach ($items as $i): $p=$i['product']; $imgs=product_images($p); ?>
          <div class="flex gap-3 p-4">
            <img src="<?= h($imgs[0]) ?>" class="w-16 h-16 object-cover rounded bg-gray-50">
            <div class="flex-1 text-sm">
              <div class="font-semibold line-clamp-1"><?= h($p['it_name']) ?></div>
              <?php if ($i['option_text']): ?>
              <div class="text-xs text-gray-500"><?= h($i['option_text']) ?></div>
              <?php endif; ?>
              <div class="text-xs text-gray-500">수량 <?= $i['qty'] ?></div>
            </div>
            <div class="font-bold text-primary"><?= price($i['subtotal']) ?></div>
          </div>
          <?php endforeach; ?>
        </div>
      </section>

      <!-- 주문자 -->
      <section>
        <h2 class="font-extrabold text-primary mb-3">주문자 정보</h2>
        <div class="space-y-3">
          <div>
            <label class="text-xs font-semibold text-gray-600">이름 *</label>
            <input type="text" name="name" required value="<?= h($current_user['name'] ?? '') ?>"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-accent">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">연락처 *</label>
            <input type="text" name="phone" required value="<?= h($current_user['phone'] ?? '') ?>" placeholder="010-1234-5678"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-accent">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">이메일</label>
            <input type="email" name="email" value="<?= h($current_user['email'] ?? '') ?>" placeholder="example@email.com"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                          focus:outline-none focus:ring-2 focus:ring-accent">
          </div>
        </div>
      </section>

      <!-- 배송 -->
      <section>
        <h2 class="font-extrabold text-primary mb-3">배송지</h2>
        <div class="space-y-3">
          <div>
            <label class="text-xs font-semibold text-gray-600">받는 분 *</label>
            <input type="text" name="recv_name" required value="<?= h($current_user['name'] ?? '') ?>"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">주소 *</label>
            <input type="text" name="address" required value="<?= h($current_user['address'] ?? '') ?>" placeholder="배송지 주소"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg">
          </div>
          <div>
            <label class="text-xs font-semibold text-gray-600">배송 메모</label>
            <input type="text" name="memo" placeholder="문 앞에 놓아주세요"
                   class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg">
          </div>
        </div>
      </section>

      <!-- 결제수단 -->
      <section>
        <h2 class="font-extrabold text-primary mb-3">결제 수단</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
          <?php foreach (['신용카드', '실시간계좌이체', '무통장입금', '간편결제'] as $i => $m): ?>
          <label class="block">
            <input type="radio" name="pay" value="<?= h($m) ?>"
                   <?= $i===0?'checked':'' ?> class="peer sr-only">
            <div class="border-2 border-gray-300 peer-checked:border-primary
                        peer-checked:bg-primary peer-checked:text-white
                        py-3 text-center rounded-lg text-sm font-semibold cursor-pointer
                        transition">
              <?= h($m) ?>
            </div>
          </label>
          <?php endforeach; ?>
        </div>
        <p class="mt-3 text-xs text-gray-500">⚠️ 데모 페이지로 실제 결제는 발생하지 않습니다.</p>
      </section>
    </div>

    <!-- 결제 요약 -->
    <aside class="lg:sticky lg:top-28 self-start">
      <div class="bg-white border rounded-xl p-5 space-y-3">
        <h3 class="font-extrabold text-primary">결제 요약</h3>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">상품금액</span>
          <span><?= price($total) ?></span>
        </div>
        <div class="flex justify-between text-sm">
          <span class="text-gray-600">배송비</span>
          <span><?= $shipping === 0 ? '무료' : price($shipping) ?></span>
        </div>
        <div class="border-t pt-3 flex justify-between items-baseline">
          <span class="font-bold">총 결제금액</span>
          <span class="text-2xl font-extrabold text-primary"><?= price($grand) ?></span>
        </div>
        <button type="submit"
                class="block w-full text-center py-3 rounded-lg bg-primary text-white
                       font-bold hover:bg-primary-light transition mt-3">
          <?= price($grand) ?> 결제하기
        </button>
        <a href="/cart.php" class="block w-full text-center py-2 text-xs text-gray-500 hover:text-primary">
          ← 장바구니로 돌아가기
        </a>
      </div>
    </aside>
  </form>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
