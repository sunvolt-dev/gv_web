<?php
require_once __DIR__ . '/auth.php';
require_admin();

$admin_page = $admin_page ?? '';
$admin_title = $admin_title ?? '관리자';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= h($admin_title) ?> | 관리자</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: { colors: {
    primary: { DEFAULT: '#0A2540', light: '#1B3A5C', dark: '#061829' },
    accent:  { DEFAULT: '#FFC107', dark: '#FFA000' },
  }, fontFamily: { sans: ['Pretendard', 'sans-serif'] } } } };
</script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<style>[x-cloak]{display:none!important;}</style>
</head>
<body class="font-sans bg-gray-100" x-data="{ menu: false }">

<!-- 사이드바 (데스크탑 항상 / 모바일 토글) -->
<aside class="fixed top-0 left-0 bottom-0 w-60 bg-primary text-white z-30
              transform md:translate-x-0 transition-transform"
       :class="menu ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
  <div class="px-5 h-16 flex items-center gap-2 border-b border-white/10">
    <div class="w-8 h-8 rounded bg-accent flex items-center justify-center">
      <span class="text-primary font-black">⚡</span>
    </div>
    <span class="font-bold">관리자</span>
  </div>
  <nav class="p-3 space-y-1 text-sm">
    <?php
    $menu = [
      ['url' => '/admin/',            'icon' => '📊', 'label' => '대시보드',     'key' => 'dashboard'],
      ['url' => '/admin/products.php','icon' => '📦', 'label' => '상품 관리',     'key' => 'products'],
      ['url' => '/admin/categories.php','icon' => '🗂️', 'label' => '카테고리 관리', 'key' => 'categories'],
    ];
    foreach ($menu as $m):
      $active = $m['key'] === $admin_page;
    ?>
    <a href="<?= h($m['url']) ?>"
       class="block px-3 py-2.5 rounded-lg <?= $active ? 'bg-white/10 text-accent font-bold' : 'text-white/80 hover:bg-white/5' ?>">
      <span class="mr-2"><?= $m['icon'] ?></span><?= h($m['label']) ?>
    </a>
    <?php endforeach; ?>

    <div class="border-t border-white/10 mt-3 pt-3 space-y-1">
      <a href="/" target="_blank"
         class="block px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5">
        🌐 사이트 보기
      </a>
      <a href="/admin/logout.php"
         class="block px-3 py-2.5 rounded-lg text-white/80 hover:bg-white/5">
        🚪 로그아웃
      </a>
    </div>
  </nav>
  <div class="absolute bottom-0 left-0 right-0 p-4 text-xs text-white/40 border-t border-white/10">
    <?= h(admin_user()['username']) ?> 님
  </div>
</aside>

<!-- 모바일 오버레이 -->
<div x-show="menu" x-cloak @click="menu = false"
     class="fixed inset-0 bg-black/50 z-20 md:hidden"></div>

<!-- 본문 -->
<div class="md:ml-60">

  <!-- 상단바 -->
  <header class="sticky top-0 z-10 bg-white border-b h-16 flex items-center px-4 md:px-6 gap-3">
    <button class="md:hidden p-2 -ml-2" @click="menu = true">☰</button>
    <h1 class="text-lg md:text-xl font-extrabold text-primary"><?= h($admin_title) ?></h1>
  </header>

  <main class="p-4 md:p-6">
