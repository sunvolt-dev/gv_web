<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/user_auth.php';
require_once __DIR__ . '/device.php';
cart_init();

$site = (require __DIR__ . '/config.php')['site'];

$page_title = $page_title ?? $site['name'];
$page_desc  = $page_desc  ?? $site['tagline'];
$cats_l1    = category_tree()[0] ?? [];
$current_user = user();

/* PC/모바일 뷰 분기 — 같은 URL, User-Agent별 다른 셸 (Vary 헤더로 SEO 안전) */
$IS_MOBILE = is_mobile();
if (!headers_sent()) header('Vary: User-Agent');

/* ── SEO 변수 (페이지에서 header include 전에 선택적으로 지정) ── */
$og_type   = $og_type   ?? 'website';                 // website | product | article
$og_image  = $og_image  ?? 'https://placehold.co/1200x630/0A2540/FFC107?text=Sunvolt+Battery';
$canonical = $canonical ?? canonical_url();
$no_index  = $no_index  ?? false;                      // true → 검색 제외
$json_ld   = $json_ld   ?? [];                         // 페이지별 추가 구조화 데이터 배열
$full_title = $page_title === $site['name']
    ? $site['name'] . ' — ' . $site['tagline']
    : $page_title . ' | ' . $site['name'];
?>
<!DOCTYPE html>
<html lang="ko">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="<?= h($page_desc) ?>">
  <title><?= h($full_title) ?></title>

  <link rel="canonical" href="<?= h($canonical) ?>">
  <?php if ($no_index): ?>
  <meta name="robots" content="noindex, nofollow">
  <?php else: ?>
  <meta name="robots" content="index, follow, max-image-preview:large">
  <?php endif; ?>

  <!-- Open Graph (카카오톡·페이스북 공유) -->
  <meta property="og:type" content="<?= h($og_type) ?>">
  <meta property="og:site_name" content="<?= h($site['name']) ?>">
  <meta property="og:title" content="<?= h($full_title) ?>">
  <meta property="og:description" content="<?= h($page_desc) ?>">
  <meta property="og:url" content="<?= h($canonical) ?>">
  <meta property="og:image" content="<?= h(abs_url($og_image)) ?>">
  <meta property="og:locale" content="ko_KR">

  <!-- Twitter Card -->
  <meta name="twitter:card" content="summary_large_image">
  <meta name="twitter:title" content="<?= h($full_title) ?>">
  <meta name="twitter:description" content="<?= h($page_desc) ?>">
  <meta name="twitter:image" content="<?= h(abs_url($og_image)) ?>">

  <!-- 구조화 데이터 (SEO/AEO) -->
  <?= json_ld(organization_ld()) ?>
  <?= json_ld(website_ld()) ?>
  <?php foreach ($json_ld as $ld): ?>
  <?= json_ld($ld) ?>
  <?php endforeach; ?>

  <link rel="preconnect" href="https://cdn.jsdelivr.net">
  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">

  <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css">

  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            primary: { DEFAULT: '#0A2540', light: '#1B3A5C', dark: '#061829' },
            accent:  { DEFAULT: '#FFC107', light: '#FFD54F', dark: '#FFA000' },
          },
          fontFamily: {
            sans: ['Pretendard', 'system-ui', 'sans-serif'],
          },
        },
      },
    };
  </script>

  <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
  <link rel="stylesheet" href="/assets/css/style.css">
</head>

<body class="font-sans bg-white text-gray-900 antialiased"
      x-data="{ mobileMenu: false }">

<?php if ($IS_MOBILE): /* ================= 모바일 셸 ================= */ ?>

<!-- 모바일 헤더 (고정) -->
<header class="sticky top-0 z-40 bg-primary text-white shadow-md">
  <div class="h-14 px-2 flex items-center justify-between gap-1">
    <button @click="mobileMenu = true" class="p-2.5" aria-label="메뉴">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
        <path d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>
    <a href="/" class="flex items-center gap-1.5">
      <span class="w-7 h-7 rounded-md bg-accent flex items-center justify-center text-primary font-black text-sm">⚡</span>
      <span class="font-extrabold text-[15px]"><?= h($site['name']) ?></span>
    </a>
    <a href="/cart.php" class="relative p-2.5" aria-label="장바구니">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.9">
        <circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/>
        <path d="M3 3h2.5l2.6 13.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/>
      </svg>
      <?php if (cart_count() > 0): ?>
      <span class="absolute top-0.5 right-0.5 min-w-[18px] h-[18px] px-1 rounded-full
                   bg-accent text-primary text-[10px] font-bold flex items-center justify-center">
        <?= cart_count() ?>
      </span>
      <?php endif; ?>
    </a>
  </div>
  <!-- 검색 -->
  <div class="px-3 pb-2.5">
    <form action="/shop/list.php" method="get" class="relative">
      <input type="text" name="q" placeholder="배터리 검색" value="<?= h($_GET['q'] ?? '') ?>"
             class="w-full h-9 pl-3 pr-9 rounded-full text-sm text-gray-900 focus:outline-none">
      <button type="submit" class="absolute right-0 top-0 h-9 w-9 flex items-center justify-center text-primary" aria-label="검색">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
          <circle cx="11" cy="11" r="7"/><path d="M21 21l-4.5-4.5"/>
        </svg>
      </button>
    </form>
  </div>
  <!-- 가로 스크롤 카테고리 칩 -->
  <nav class="bg-white overflow-x-auto scrollbar-hide">
    <div class="flex gap-1.5 px-3 py-2 whitespace-nowrap">
      <a href="/" class="px-3 py-1.5 rounded-full bg-primary text-white text-xs font-bold">홈</a>
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
         class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">납품사례</a>
      <a href="/blog/list.php" class="px-3 py-1.5 rounded-full bg-gray-100 text-gray-700 text-xs font-semibold">블로그</a>
    </div>
  </nav>
</header>

<!-- 모바일 전체메뉴 (전체화면 슬라이드) -->
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50" x-transition.opacity>
  <div class="absolute inset-0 bg-black/50" @click="mobileMenu = false"></div>
  <aside class="absolute left-0 top-0 bottom-0 w-[82%] max-w-xs bg-white flex flex-col"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="bg-primary text-white p-4">
      <div class="flex items-center justify-between mb-3">
        <span class="font-bold">전체메뉴</span>
        <button @click="mobileMenu = false" class="p-1 text-xl" aria-label="닫기">✕</button>
      </div>
      <?php if ($current_user): ?>
      <a href="/mypage/" class="block bg-white/10 rounded-lg p-3">
        <div class="font-bold"><?= h($current_user['name']) ?>님</div>
        <div class="text-xs text-white/60">마이페이지 →</div>
      </a>
      <?php else: ?>
      <div class="flex gap-2">
        <a href="/auth/login.php" class="flex-1 text-center bg-accent text-primary rounded-lg py-2 text-sm font-bold">로그인</a>
        <a href="/auth/register.php" class="flex-1 text-center bg-white/10 rounded-lg py-2 text-sm font-bold">회원가입</a>
      </div>
      <?php endif; ?>
    </div>
    <nav class="flex-1 overflow-y-auto py-1">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
         class="block px-4 py-3 font-bold text-primary border-b"><?= h($c['ca_name']) ?></a>
      <?php foreach (category_tree()[$c['ca_id']] ?? [] as $sub): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $sub['ca_id']])) ?>"
         class="block pl-7 pr-4 py-2 text-sm text-gray-600 border-b border-gray-50">└ <?= h($sub['ca_name']) ?></a>
      <?php endforeach; ?>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block px-4 py-3 font-bold text-gray-800 border-b">납품사례</a>
      <a href="/blog/list.php" class="block px-4 py-3 font-bold text-gray-800 border-b">블로그</a>
      <a href="/faq.php" class="block px-4 py-3 font-bold text-gray-800 border-b">FAQ</a>
    </nav>
    <div class="border-t p-4 text-xs text-gray-500 space-y-1.5">
      <a href="tel:<?= h($site['phone']) ?>" class="block">고객센터 <?= h($site['phone']) ?></a>
      <a href="/admin/" class="block">관리자</a>
      <a href="<?= h(toggle_view_url()) ?>" class="block text-primary font-semibold">🖥 PC 버전으로 보기</a>
    </div>
  </aside>
</div>

<?php else: /* ================= PC 셸 ================= */ ?>

<!-- 상단 유틸리티 + 메인 헤더 (함께 고정) -->
<div class="sticky top-0 z-40">

<!-- 상단 유틸리티 바 -->
<div class="hidden md:block bg-primary-dark text-white/80 text-xs">
  <div class="max-w-7xl mx-auto px-4 h-9 flex items-center justify-between">
    <div>전국 무료배송 · 평일 오후 3시 이전 주문 시 당일 발송</div>
    <div class="flex gap-4">
      <a href="tel:<?= h($site['phone']) ?>" class="hover:text-accent">고객센터 <?= h($site['phone']) ?></a>
      <?php if ($current_user): ?>
        <a href="/mypage/" class="hover:text-accent"><?= h($current_user['name']) ?>님</a>
        <a href="/auth/logout.php" class="hover:text-accent">로그아웃</a>
      <?php else: ?>
        <a href="/auth/login.php" class="hover:text-accent">로그인</a>
        <a href="/auth/register.php" class="hover:text-accent">회원가입</a>
      <?php endif; ?>
      <a href="/admin/" class="hover:text-accent">관리자</a>
    </div>
  </div>
</div>

<!-- 메인 헤더 -->
<header class="bg-white/95 backdrop-blur border-b border-gray-200">
  <div class="max-w-7xl mx-auto px-4 h-16 md:h-20 flex items-center justify-between gap-6">

    <!-- 모바일 햄버거 -->
    <button class="md:hidden p-2 -ml-2 text-primary"
            @click="mobileMenu = true" aria-label="메뉴">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
        <path d="M3 6h18M3 12h18M3 18h18"/>
      </svg>
    </button>

    <!-- 로고 -->
    <a href="/" class="flex items-center gap-2 shrink-0">
      <div class="w-9 h-9 md:w-10 md:h-10 rounded-lg bg-primary flex items-center justify-center">
        <span class="text-accent font-black text-lg md:text-xl">⚡</span>
      </div>
      <div class="leading-tight">
        <div class="font-extrabold text-base md:text-lg text-primary tracking-tight">
          <?= h($site['name']) ?>
        </div>
        <div class="hidden md:block text-[11px] text-gray-500"><?= h($site['tagline']) ?></div>
      </div>
    </a>

    <!-- 검색 (데스크탑) -->
    <form action="/shop/list.php" method="get"
          class="hidden md:flex flex-1 max-w-xl mx-4 relative">
      <input type="text" name="q" placeholder="찾으시는 배터리를 검색해보세요"
             value="<?= h($_GET['q'] ?? '') ?>"
             class="w-full h-11 pl-4 pr-12 rounded-full border-2 border-primary
                    focus:outline-none focus:ring-2 focus:ring-accent
                    text-sm">
      <button type="submit"
              class="absolute right-1 top-1 bottom-1 px-4 rounded-full
                     bg-primary text-white hover:bg-primary-light transition">
        검색
      </button>
    </form>

    <!-- 우측 아이콘 -->
    <div class="flex items-center gap-2 md:gap-4 shrink-0">
      <a href="/cart.php"
         class="relative p-2 text-primary hover:text-accent-dark transition"
         aria-label="장바구니">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
          <circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/>
          <path d="M3 3h2.5l2.6 13.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/>
        </svg>
        <?php if (cart_count() > 0): ?>
        <span class="absolute -top-0.5 -right-0.5 min-w-[20px] h-5 px-1
                     rounded-full bg-accent text-primary text-[11px] font-bold
                     flex items-center justify-center">
          <?= cart_count() ?>
        </span>
        <?php endif; ?>
      </a>
    </div>
  </div>

  <!-- 카테고리 GNB (데스크탑) -->
  <nav class="hidden md:block bg-white border-t border-gray-100">
    <div class="max-w-7xl mx-auto px-4">
      <ul class="flex items-center gap-1 h-12 text-sm font-semibold">
        <li>
          <a href="/" class="px-3 py-2 hover:text-accent-dark text-primary">HOME</a>
        </li>
        <?php foreach ($cats_l1 as $c): ?>
        <li>
          <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
             class="px-3 py-2 hover:text-accent-dark text-gray-700 hover:text-primary">
            <?= h($c['ca_name']) ?>
          </a>
        </li>
        <?php endforeach; ?>
        <li class="ml-2 pl-3 border-l">
          <a href="/cases/list.php" class="px-3 py-2 hover:text-accent-dark text-gray-700 hover:text-primary">
            납품사례
          </a>
        </li>
        <li>
          <a href="/blog/list.php" class="px-3 py-2 hover:text-accent-dark text-gray-700 hover:text-primary">
            블로그
          </a>
        </li>
        <li>
          <a href="/faq.php" class="px-3 py-2 hover:text-accent-dark text-gray-700 hover:text-primary">
            FAQ
          </a>
        </li>
      </ul>
    </div>
  </nav>
</header>

</div><!-- /sticky 유틸리티+헤더 래퍼 -->

<!-- 모바일 메뉴 슬라이드 패널 -->
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 md:hidden"
     x-transition.opacity>
  <div class="absolute inset-0 bg-black/50" @click="mobileMenu = false"></div>
  <aside class="absolute left-0 top-0 bottom-0 w-72 bg-white shadow-xl
                flex flex-col"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="bg-primary text-white px-4 h-16 flex items-center justify-between">
      <span class="font-bold"><?= h($site['name']) ?></span>
      <button @click="mobileMenu = false" class="p-2" aria-label="닫기">✕</button>
    </div>
    <form action="/shop/list.php" method="get" class="p-4 border-b">
      <input type="text" name="q" placeholder="검색"
             class="w-full h-10 px-3 rounded-lg border border-gray-300
                    focus:outline-none focus:ring-2 focus:ring-accent text-sm">
    </form>
    <nav class="flex-1 overflow-y-auto py-2">
      <a href="/" class="block px-4 py-3 font-semibold text-primary hover:bg-gray-50">HOME</a>
      <?php foreach ($cats_l1 as $c): ?>
        <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
           class="block px-4 py-3 font-semibold text-gray-800 hover:bg-gray-50 border-t">
          <?= h($c['ca_name']) ?>
        </a>
        <?php $children = category_tree()[$c['ca_id']] ?? []; ?>
        <?php foreach ($children as $sub): ?>
          <a href="<?= h(url('/shop/list.php', ['ca_id' => $sub['ca_id']])) ?>"
             class="block pl-8 pr-4 py-2 text-sm text-gray-600 hover:bg-gray-50">
            └ <?= h($sub['ca_name']) ?>
          </a>
        <?php endforeach; ?>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block px-4 py-3 font-semibold text-gray-800 hover:bg-gray-50 border-t bg-gray-50">납품사례</a>
      <a href="/blog/list.php" class="block px-4 py-3 font-semibold text-gray-800 hover:bg-gray-50 border-t">블로그</a>
      <a href="/faq.php" class="block px-4 py-3 font-semibold text-gray-800 hover:bg-gray-50 border-t">FAQ</a>
    </nav>
    <div class="border-t p-4 text-xs text-gray-500 space-y-2">
      <div>고객센터 <?= h($site['phone']) ?></div>
      <a href="/cart.php" class="block text-primary font-semibold">🛒 장바구니 (<?= cart_count() ?>)</a>
      <?php if ($current_user): ?>
        <a href="/mypage/" class="block text-primary font-semibold">👤 <?= h($current_user['name']) ?>님 (마이페이지)</a>
        <a href="/auth/logout.php" class="block text-red-600">로그아웃</a>
      <?php else: ?>
        <a href="/auth/login.php" class="block text-primary font-semibold">로그인</a>
        <a href="/auth/register.php" class="block text-gray-600">회원가입</a>
      <?php endif; ?>
      <a href="/admin/" class="block text-gray-500 pt-2 border-t">관리자 로그인</a>
    </div>
  </aside>
</div>

<?php endif; /* ================= 셸 분기 끝 ================= */ ?>

<main class="<?= $IS_MOBILE ? 'pb-16' : '' ?>">
