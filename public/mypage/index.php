<?php
require_once __DIR__ . '/../../includes/user_auth.php';
require_login();

$u = user();
$welcome = isset($_GET['welcome']);

// 최근 주문 5건
$recent_orders = db_all(
    'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC LIMIT 5',
    [$u['id']]
);

$page_title = '마이페이지';
require __DIR__ . '/../../includes/header.php';
?>

<section class="max-w-5xl mx-auto px-4 py-8 md:py-12">

  <?php if ($welcome): ?>
  <div class="mb-6 p-4 rounded-xl bg-accent/10 border border-accent text-primary text-sm font-semibold">
    🎉 <?= h($u['name']) ?>님, 회원가입을 환영합니다!
  </div>
  <?php endif; ?>

  <div class="grid lg:grid-cols-[260px_1fr] gap-6">

    <!-- 사이드바 -->
    <aside class="bg-white border rounded-xl overflow-hidden">
      <div class="bg-primary text-white p-5">
        <div class="text-xs text-white/60 mb-1">안녕하세요</div>
        <div class="font-extrabold text-lg"><?= h($u['name']) ?>님</div>
        <div class="text-xs text-white/70 mt-1"><?= h($u['email']) ?></div>
      </div>
      <nav class="p-2 text-sm">
        <a href="/mypage/" class="block px-3 py-2 rounded-lg bg-gray-100 font-bold text-primary">
          📊 마이홈
        </a>
        <a href="/mypage/orders.php" class="block px-3 py-2 rounded-lg hover:bg-gray-50">
          📦 주문 내역
        </a>
        <a href="/mypage/edit.php" class="block px-3 py-2 rounded-lg hover:bg-gray-50">
          ✏️ 회원정보 수정
        </a>
        <a href="/auth/logout.php" class="block px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 mt-2 border-t pt-3">
          🚪 로그아웃
        </a>
      </nav>
    </aside>

    <!-- 본문 -->
    <main class="space-y-6">
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary">마이홈</h1>

      <!-- 요약 카드 -->
      <div class="grid grid-cols-3 gap-3">
        <div class="bg-white border rounded-xl p-4 text-center">
          <div class="text-xs text-gray-500 mb-1">총 주문</div>
          <div class="text-2xl font-extrabold text-primary"><?= count($recent_orders) ?></div>
        </div>
        <div class="bg-white border rounded-xl p-4 text-center">
          <div class="text-xs text-gray-500 mb-1">장바구니</div>
          <div class="text-2xl font-extrabold text-primary"><?= cart_count() ?></div>
        </div>
        <div class="bg-white border rounded-xl p-4 text-center">
          <div class="text-xs text-gray-500 mb-1">가입일</div>
          <div class="text-sm font-bold text-primary mt-1"><?= h(date('Y.m.d', strtotime($u['created_at']))) ?></div>
        </div>
      </div>

      <!-- 최근 주문 -->
      <div class="bg-white border rounded-xl overflow-hidden">
        <div class="px-5 py-4 border-b flex items-center justify-between">
          <h2 class="font-extrabold text-primary">최근 주문</h2>
          <a href="/mypage/orders.php" class="text-xs text-primary hover:text-accent-dark">전체보기 →</a>
        </div>
        <?php if (empty($recent_orders)): ?>
        <div class="py-12 text-center text-gray-400 text-sm">
          <div class="text-4xl mb-2">📦</div>
          아직 주문 내역이 없습니다.
        </div>
        <?php else: ?>
        <div class="divide-y text-sm">
          <?php foreach ($recent_orders as $o): ?>
          <div class="px-5 py-4 flex items-center justify-between gap-4">
            <div>
              <div class="font-bold text-primary">#<?= h($o['order_no']) ?></div>
              <div class="text-xs text-gray-500"><?= h(date('Y.m.d H:i', strtotime($o['created_at']))) ?></div>
            </div>
            <div class="text-right">
              <div class="font-extrabold"><?= price((int)$o['total_amount']) ?></div>
              <div class="text-xs text-gray-500"><?= h($o['status']) ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>

      <!-- 정보 -->
      <div class="bg-white border rounded-xl p-5">
        <h2 class="font-extrabold text-primary mb-3">기본 정보</h2>
        <dl class="text-sm divide-y">
          <div class="flex py-2"><dt class="w-24 text-gray-500">이메일</dt><dd class="font-medium"><?= h($u['email']) ?></dd></div>
          <div class="flex py-2"><dt class="w-24 text-gray-500">이름</dt><dd class="font-medium"><?= h($u['name']) ?></dd></div>
          <div class="flex py-2"><dt class="w-24 text-gray-500">연락처</dt><dd class="font-medium"><?= h($u['phone'] ?: '-') ?></dd></div>
          <div class="flex py-2"><dt class="w-24 text-gray-500">주소</dt><dd class="font-medium"><?= h($u['address'] ?: '-') ?></dd></div>
        </dl>
        <a href="/mypage/edit.php" class="inline-block mt-3 text-xs text-primary hover:text-accent-dark">정보 수정 →</a>
      </div>
    </main>
  </div>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
