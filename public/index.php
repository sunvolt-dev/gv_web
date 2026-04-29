<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = '메인';
$page_desc  = '자동차·산업용·전동모빌리티 배터리 전문 - 정품 보장, 빠른 배송';

$best_products = get_best_products(8);
$new_products  = get_new_products(4);
$top_categories = category_tree()[0] ?? [];

require __DIR__ . '/../includes/header.php';
?>

<!-- ───────── HERO ───────── -->
<section class="relative bg-gradient-to-br from-primary via-primary to-primary-dark
                text-white overflow-hidden">
  <div class="absolute -right-20 -top-20 w-96 h-96 rounded-full bg-accent/10 blur-3xl"></div>
  <div class="absolute -left-20 bottom-0 w-72 h-72 rounded-full bg-accent/5 blur-3xl"></div>

  <div class="relative max-w-7xl mx-auto px-4 py-16 md:py-28
              grid md:grid-cols-2 items-center gap-10">
    <div>
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                  bg-accent/20 text-accent text-xs font-bold mb-4">
        <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
        BATTERY · CHARGER · ESS
      </div>
      <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-5">
        모든 차량과 장비의<br>
        <span class="text-accent">파워를 책임집니다</span>
      </h1>
      <p class="text-base md:text-lg text-white/80 mb-8 leading-relaxed">
        승용차부터 산업용 ESS, 전동 모빌리티까지<br class="hidden md:block">
        정품 인증 배터리만 — 전국 무료배송 · 당일 출고
      </p>
      <div class="flex flex-wrap gap-3">
        <a href="<?= h(url('/shop/list.php', ['ca_id' => 10])) ?>"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg
                  bg-accent text-primary font-bold hover:bg-accent-light transition">
          🚗 자동차 배터리 보기 →
        </a>
        <a href="<?= h(url('/shop/list.php', ['ca_id' => 30])) ?>"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-lg
                  border-2 border-white/30 text-white hover:bg-white/10 transition">
          🛴 전동모빌리티
        </a>
      </div>
    </div>
    <div class="hidden md:flex justify-center">
      <div class="relative">
        <div class="w-72 h-72 lg:w-96 lg:h-96 rounded-3xl bg-gradient-to-br from-accent to-accent-dark
                    flex items-center justify-center text-9xl shadow-2xl rotate-3">
          🔋
        </div>
        <div class="absolute -bottom-4 -left-4 bg-white text-primary
                    px-4 py-3 rounded-xl shadow-lg">
          <div class="text-xs text-gray-500">최저가 보장</div>
          <div class="font-extrabold text-lg">₩79,000~</div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ───────── 베네핏 스트립 ───────── -->
<section class="border-b">
  <div class="max-w-7xl mx-auto px-4 py-6 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
    <div class="flex flex-col items-center gap-1">
      <span class="text-2xl">🚚</span>
      <div class="text-sm font-bold">전국 무료배송</div>
      <div class="text-[11px] text-gray-500">5만원 이상 주문 시</div>
    </div>
    <div class="flex flex-col items-center gap-1">
      <span class="text-2xl">⏱️</span>
      <div class="text-sm font-bold">당일 발송</div>
      <div class="text-[11px] text-gray-500">평일 15시 이전</div>
    </div>
    <div class="flex flex-col items-center gap-1">
      <span class="text-2xl">🛡️</span>
      <div class="text-sm font-bold">정품 보장</div>
      <div class="text-[11px] text-gray-500">불량 시 100% 교환</div>
    </div>
    <div class="flex flex-col items-center gap-1">
      <span class="text-2xl">📞</span>
      <div class="text-sm font-bold">전문 상담</div>
      <div class="text-[11px] text-gray-500">평일 09 - 18시</div>
    </div>
  </div>
</section>

<!-- ───────── 카테고리 ───────── -->
<section class="max-w-7xl mx-auto px-4 py-12 md:py-16">
  <div class="text-center mb-10">
    <h2 class="text-2xl md:text-3xl font-extrabold text-primary">카테고리</h2>
    <p class="text-sm text-gray-500 mt-2">필요한 배터리를 카테고리에서 찾아보세요</p>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <?php
    $cat_visuals = [
      10 => ['icon' => '🚗', 'gradient' => 'from-blue-600 to-primary'],
      20 => ['icon' => '⚡', 'gradient' => 'from-emerald-600 to-teal-700'],
      30 => ['icon' => '🛴', 'gradient' => 'from-indigo-600 to-purple-700'],
      40 => ['icon' => '🔌', 'gradient' => 'from-orange-500 to-red-600'],
    ];
    foreach ($top_categories as $c):
      $v = $cat_visuals[$c['ca_id']] ?? ['icon' => '🔋', 'gradient' => 'from-gray-600 to-gray-800'];
    ?>
    <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
       class="group relative aspect-square rounded-2xl overflow-hidden
              bg-gradient-to-br <?= $v['gradient'] ?>
              text-white p-5 flex flex-col justify-between
              hover:scale-[1.02] transition shadow-md">
      <div class="text-4xl md:text-5xl"><?= $v['icon'] ?></div>
      <div>
        <div class="font-extrabold text-lg md:text-xl leading-tight"><?= h($c['ca_name']) ?></div>
        <div class="text-xs text-white/70 mt-1 group-hover:text-accent transition">바로가기 →</div>
      </div>
    </a>
    <?php endforeach; ?>
  </div>
</section>

<!-- ───────── BEST 추천상품 ───────── -->
<section class="bg-gray-50 py-12 md:py-16">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between mb-8">
      <div>
        <span class="inline-block px-2 py-1 rounded text-[11px] font-bold badge-best mb-2">BEST</span>
        <h2 class="text-2xl md:text-3xl font-extrabold text-primary">베스트 추천 상품</h2>
        <p class="text-sm text-gray-500 mt-1">고객님이 가장 많이 찾는 인기 상품</p>
      </div>
      <a href="<?= h(url('/shop/list.php')) ?>"
         class="hidden md:inline-flex items-center gap-1 text-sm text-primary font-semibold hover:text-accent-dark">
        전체보기 →
      </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 md:gap-6">
      <?php foreach ($best_products as $p): ?>
        <?php include __DIR__ . '/_product_card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ───────── NEW 신상품 ───────── -->
<?php if (!empty($new_products)): ?>
<section class="max-w-7xl mx-auto px-4 py-12 md:py-16">
  <div class="flex items-end justify-between mb-8">
    <div>
      <span class="inline-block px-2 py-1 rounded text-[11px] font-bold badge-new mb-2">NEW</span>
      <h2 class="text-2xl md:text-3xl font-extrabold text-primary">신상품</h2>
      <p class="text-sm text-gray-500 mt-1">새롭게 입고된 최신 모델</p>
    </div>
  </div>
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
    <?php foreach ($new_products as $p): ?>
      <?php include __DIR__ . '/_product_card.php'; ?>
    <?php endforeach; ?>
  </div>
</section>
<?php endif; ?>

<!-- ───────── 프로모 배너 ───────── -->
<section class="max-w-7xl mx-auto px-4 mb-16">
  <div class="rounded-2xl bg-gradient-to-r from-primary to-primary-dark
              text-white p-8 md:p-12 grid md:grid-cols-3 items-center gap-6
              relative overflow-hidden">
    <div class="absolute right-0 top-0 text-[200px] opacity-10 leading-none">⚡</div>
    <div class="md:col-span-2 relative">
      <div class="text-accent font-bold text-sm mb-2">B2B 대량 구매 문의</div>
      <h3 class="text-xl md:text-2xl font-extrabold mb-2">법인·기관 단체 주문 환영합니다</h3>
      <p class="text-white/80 text-sm leading-relaxed">
        택시 사업자, 산업체, 솔라 시공업체 등 100개 이상 대량 주문 시
        별도 견적 + 추가 할인 + 직배송 지원
      </p>
    </div>
    <div class="relative">
      <a href="#" class="inline-flex items-center justify-center w-full md:w-auto
                px-8 py-3 rounded-lg bg-accent text-primary font-bold hover:bg-accent-light transition">
        견적 문의하기
      </a>
    </div>
  </div>
</section>

<?php require __DIR__ . '/../includes/footer.php'; ?>
