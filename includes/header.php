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
          colors: <?= json_encode(theme_colors(), JSON_UNESCAPED_SLASHES) ?>,
          fontFamily: {
            sans: [<?= json_encode(theme_font()) ?>, 'system-ui', 'sans-serif'],
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

<?php else: /* ============ PC 셸 (테마별) ============ */ ?>

<?php include theme_file('header.php'); ?>

<?php endif; /* ================= 셸 분기 끝 ================= */ ?>

<main class="<?= $IS_MOBILE ? 'pb-16' : '' ?>">
