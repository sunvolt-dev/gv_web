<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = '메인';
$page_desc  = '자동차·산업용·전동모빌리티 배터리 전문 - 정품 보장, 빠른 배송';

$best_products = get_best_products(8);
$new_products  = get_new_products(4);
$top_categories = category_tree()[0] ?? [];
$recent_posts  = db_all('SELECT id, title, summary, thumbnail, created_at FROM posts WHERE published = 1 ORDER BY created_at DESC LIMIT 2');
$banners = [];
try {
    $banners = db_all('SELECT * FROM banners WHERE published = 1 ORDER BY sort_order, id');
} catch (Throwable $e) { /* banners 테이블 없으면 폴백 */ }

require __DIR__ . '/../includes/header.php';
?>

<!-- ───────── HERO 슬라이더 ───────── -->
<?php if (!empty($banners)): ?>
<section class="relative bg-primary-dark text-white overflow-hidden">
  <div class="swiper hero-swiper">
    <div class="swiper-wrapper">
      <?php foreach ($banners as $b):
        $align = $b['text_align'] ?: 'left';
        $align_cls = match ($align) {
          'center' => 'items-center text-center',
          'right'  => 'items-end text-right',
          default  => 'items-start text-left',
        };
        $grad_cls = $align === 'right'
          ? 'bg-gradient-to-l from-black/70 via-black/40 to-transparent'
          : ($align === 'center'
              ? 'bg-gradient-to-t from-black/70 via-black/40 to-black/30'
              : 'bg-gradient-to-r from-black/70 via-black/40 to-transparent');
      ?>
      <div class="swiper-slide relative">
        <div class="aspect-[16/9] md:aspect-[16/6] min-h-[360px] md:min-h-[480px] relative">
          <img src="<?= h($b['image_url']) ?>" alt="<?= h($b['title']) ?>"
               class="absolute inset-0 w-full h-full object-cover">
          <div class="absolute inset-0 <?= $grad_cls ?>"></div>

          <div class="absolute inset-0 flex flex-col justify-center <?= $align_cls ?>">
            <div class="max-w-7xl w-full mx-auto px-4 md:px-8 flex flex-col <?= $align_cls ?>">
              <?php if ($b['accent_label']): ?>
              <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full
                          bg-accent/25 text-accent text-xs font-bold mb-4 backdrop-blur-sm">
                <span class="w-1.5 h-1.5 rounded-full bg-accent animate-pulse"></span>
                <?= h($b['accent_label']) ?>
              </div>
              <?php endif; ?>

              <h1 class="text-3xl md:text-5xl lg:text-6xl font-extrabold leading-tight tracking-tight mb-4 md:mb-5 max-w-5xl xl:max-w-6xl drop-shadow">
                <?= h($b['title']) ?>
              </h1>

              <?php if ($b['subtitle']): ?>
              <p class="text-base md:text-xl text-white/90 mb-7 md:mb-8 leading-relaxed max-w-3xl drop-shadow whitespace-pre-line">
                <?= h($b['subtitle']) ?>
              </p>
              <?php endif; ?>

              <?php if ($b['cta_text']): ?>
              <a href="<?= h($b['cta_url'] ?: '#') ?>"
                 class="inline-flex items-center gap-2 px-7 py-3.5 rounded-lg
                        bg-accent text-primary font-bold hover:bg-accent-light transition shadow-lg w-fit">
                <?= h($b['cta_text']) ?> →
              </a>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <?php if (count($banners) > 1): ?>
    <div class="hero-swiper-pagination !bottom-4"></div>
    <button class="hero-swiper-prev hidden md:flex absolute left-4 top-1/2 -translate-y-1/2 z-10
                   w-12 h-12 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur items-center justify-center text-white text-xl">‹</button>
    <button class="hero-swiper-next hidden md:flex absolute right-4 top-1/2 -translate-y-1/2 z-10
                   w-12 h-12 rounded-full bg-white/20 hover:bg-white/40 backdrop-blur items-center justify-center text-white text-xl">›</button>
    <?php endif; ?>
  </div>

  <style>
    .hero-swiper-pagination { position: absolute; left: 0; right: 0; text-align: center; z-index: 10; }
    .hero-swiper-pagination .swiper-pagination-bullet { width: 28px; height: 4px; border-radius: 2px; background: #ffffff80; opacity: 1; transition: all 0.3s; }
    .hero-swiper-pagination .swiper-pagination-bullet-active { background: #FFC107; width: 48px; }
  </style>

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      new Swiper('.hero-swiper', {
        loop: <?= count($banners) > 1 ? 'true' : 'false' ?>,
        autoplay: { delay: 5500, disableOnInteraction: false },
        pagination: { el: '.hero-swiper-pagination', clickable: true },
        navigation: { nextEl: '.hero-swiper-next', prevEl: '.hero-swiper-prev' },
        speed: 700,
      });
    });
  </script>
</section>
<?php else: ?>
<!-- 폴백 (배너 없을 때) -->
<section class="relative bg-gradient-to-br from-primary via-primary to-primary-dark text-white overflow-hidden">
  <div class="relative max-w-7xl mx-auto px-4 py-16 md:py-28 text-center">
    <h1 class="text-3xl md:text-5xl font-extrabold mb-4">모든 차량과 장비의 <span class="text-accent">파워를 책임집니다</span></h1>
    <p class="text-white/80 mb-8">관리자 페이지에서 배너를 등록해주세요.</p>
    <a href="/shop/list.php" class="inline-block px-7 py-3 rounded-lg bg-accent text-primary font-bold">전체 상품 보기 →</a>
  </div>
</section>
<?php endif; ?>

<!-- ───────── 베네핏 스트립 ───────── -->
<section class="border-b">
  <div class="max-w-7xl mx-auto px-4 py-12 md:py-14 grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
    <div class="flex flex-col items-center gap-2">
      <span class="text-5xl md:text-6xl">🚚</span>
      <div class="text-base md:text-lg font-bold">전국 무료배송</div>
      <div class="text-xs md:text-sm text-gray-500">5만원 이상 주문 시</div>
    </div>
    <div class="flex flex-col items-center gap-2">
      <span class="text-5xl md:text-6xl">⏱️</span>
      <div class="text-base md:text-lg font-bold">당일 발송</div>
      <div class="text-xs md:text-sm text-gray-500">평일 15시 이전</div>
    </div>
    <div class="flex flex-col items-center gap-2">
      <span class="text-5xl md:text-6xl">🛡️</span>
      <div class="text-base md:text-lg font-bold">정품 보장</div>
      <div class="text-xs md:text-sm text-gray-500">불량 시 100% 교환</div>
    </div>
    <div class="flex flex-col items-center gap-2">
      <span class="text-5xl md:text-6xl">📞</span>
      <div class="text-base md:text-lg font-bold">전문 상담</div>
      <div class="text-xs md:text-sm text-gray-500">평일 09 - 18시</div>
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

<!-- ───────── 최신 블로그 ───────── -->
<?php if (!empty($recent_posts)): ?>
<section class="bg-gray-50 py-12 md:py-16">
  <div class="max-w-7xl mx-auto px-4">
    <div class="flex items-end justify-between mb-8">
      <div>
        <span class="inline-block px-2 py-1 rounded text-[11px] font-bold bg-primary text-accent mb-2">BLOG</span>
        <h2 class="text-2xl md:text-3xl font-extrabold text-primary">최신 소식</h2>
        <p class="text-sm text-gray-500 mt-1">배터리 관리·안전·기술 정보</p>
      </div>
      <a href="/blog/list.php"
         class="hidden md:inline-flex items-center gap-1 text-sm text-primary font-semibold hover:text-accent-dark">
        블로그 전체보기 →
      </a>
    </div>
    <div class="grid md:grid-cols-2 gap-5">
      <?php foreach ($recent_posts as $post):
        $img = $post['thumbnail'] ?: 'https://placehold.co/800x500/0A2540/FFC107?text=Blog';
      ?>
      <a href="<?= h(url('/blog/view.php', ['id' => $post['id']])) ?>"
         class="product-card group block bg-white rounded-2xl overflow-hidden border">
        <div class="grid md:grid-cols-[180px_1fr]">
          <div class="aspect-[16/10] md:aspect-auto bg-gray-100 overflow-hidden">
            <img src="<?= h($img) ?>" alt=""
                 class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
          </div>
          <div class="p-4 md:p-5">
            <div class="text-[11px] text-gray-400 mb-1">
              <?= h(date('Y.m.d', strtotime($post['created_at']))) ?>
            </div>
            <h3 class="font-extrabold text-base text-primary line-clamp-2 leading-snug group-hover:text-accent-dark transition">
              <?= h($post['title']) ?>
            </h3>
            <?php if ($post['summary']): ?>
            <p class="text-xs text-gray-500 mt-2 line-clamp-2"><?= h($post['summary']) ?></p>
            <?php endif; ?>
          </div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
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
