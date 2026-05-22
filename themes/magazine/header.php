<?php /* [magazine 테마] PC 헤더 — 중앙 로고 + 하단 네비 */ ?>
<header class="bg-white border-b-2 border-primary">
  <!-- 상단 띠 -->
  <div class="bg-primary text-white/80 text-[11px]">
    <div class="max-w-5xl mx-auto px-6 h-8 flex items-center justify-between">
      <span>SUNVOLT JOURNAL · 배터리 전문 매거진</span>
      <div class="flex gap-3">
        <?php if ($current_user): ?>
        <a href="/mypage/" class="hover:text-accent"><?= h($current_user['name']) ?>님</a>
        <a href="/auth/logout.php" class="hover:text-accent">로그아웃</a>
        <?php else: ?>
        <a href="/auth/login.php" class="hover:text-accent">로그인</a>
        <?php endif; ?>
        <a href="/cart.php" class="hover:text-accent">장바구니(<?= cart_count() ?>)</a>
      </div>
    </div>
  </div>
  <!-- 중앙 로고 -->
  <div class="max-w-5xl mx-auto px-6 py-7 flex items-center justify-center relative">
    <button class="md:hidden absolute left-6 p-1" @click="mobileMenu = true" aria-label="메뉴">
      <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>
    <a href="/" class="text-center">
      <div class="text-2xl md:text-3xl font-extrabold tracking-[0.15em] text-primary uppercase"><?= h($site['name']) ?></div>
      <div class="text-[11px] tracking-[0.3em] text-accent mt-1 uppercase">Battery Magazine</div>
    </a>
  </div>
  <!-- 하단 네비 -->
  <nav class="hidden md:block border-t border-gray-200">
    <ul class="max-w-5xl mx-auto px-6 flex items-center justify-center gap-8 h-12 text-[13px] font-bold tracking-wide text-gray-600">
      <li><a href="/" class="hover:text-accent">HOME</a></li>
      <?php foreach ($cats_l1 as $c): ?>
      <li><a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="hover:text-accent"><?= h($c['ca_name']) ?></a></li>
      <?php endforeach; ?>
      <li><a href="/cases/list.php" class="hover:text-accent">납품사례</a></li>
      <li><a href="/blog/list.php" class="text-accent">JOURNAL</a></li>
      <li><a href="/faq.php" class="hover:text-accent">FAQ</a></li>
    </ul>
  </nav>
</header>

<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 md:hidden" x-transition.opacity>
  <div class="absolute inset-0 bg-black/40" @click="mobileMenu = false"></div>
  <aside class="absolute left-0 top-0 bottom-0 w-72 bg-white p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="flex justify-between items-center mb-6 border-b border-primary pb-3">
      <span class="font-extrabold tracking-widest text-primary uppercase"><?= h($site['name']) ?></span>
      <button @click="mobileMenu = false" class="text-xl">✕</button>
    </div>
    <nav class="space-y-1 text-sm font-bold">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="block py-2.5 border-b text-gray-700"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block py-2.5 border-b text-gray-700">납품사례</a>
      <a href="/blog/list.php" class="block py-2.5 border-b text-accent">JOURNAL</a>
      <a href="/faq.php" class="block py-2.5 border-b text-gray-700">FAQ</a>
      <a href="/admin/" class="block py-2.5 text-gray-400 text-xs">관리자</a>
    </nav>
  </aside>
</div>
