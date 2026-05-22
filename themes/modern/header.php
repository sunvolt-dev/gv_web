<?php /* [modern 테마] PC 헤더 — 얇은 단일 행, 미니멀 */ ?>
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-gray-100">
  <div class="max-w-6xl mx-auto px-6 h-[72px] flex items-center justify-between">

    <button class="md:hidden p-2 -ml-2" @click="mobileMenu = true" aria-label="메뉴">
      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>

    <a href="/" class="text-lg md:text-xl font-extrabold tracking-tight text-primary shrink-0">
      <?= h($site['name']) ?>
    </a>

    <nav class="hidden md:flex items-center gap-8 text-[13px] font-semibold text-gray-500 tracking-wide">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="hover:text-primary transition"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="hover:text-primary transition">CASES</a>
      <a href="/blog/list.php" class="hover:text-primary transition">JOURNAL</a>
    </nav>

    <div class="flex items-center gap-1 shrink-0">
      <a href="/shop/list.php" class="p-2 text-gray-700 hover:text-accent" aria-label="검색">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.5-4.5"/></svg>
      </a>
      <a href="/cart.php" class="relative p-2 text-gray-700 hover:text-accent" aria-label="장바구니">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4zM3 6h18M16 10a4 4 0 0 1-8 0"/></svg>
        <?php if (cart_count() > 0): ?>
        <span class="absolute top-0.5 right-0.5 min-w-[16px] h-4 px-1 rounded-full bg-accent text-white text-[9px] font-bold flex items-center justify-center"><?= cart_count() ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= is_logged_in() ? '/mypage/' : '/auth/login.php' ?>" class="p-2 text-gray-700 hover:text-accent" aria-label="마이페이지">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
      </a>
    </div>
  </div>
</header>

<!-- 좁은 PC 창용 드로어 -->
<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 md:hidden" x-transition.opacity>
  <div class="absolute inset-0 bg-black/40" @click="mobileMenu = false"></div>
  <aside class="absolute right-0 top-0 bottom-0 w-72 bg-white p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="flex justify-between items-center mb-6">
      <span class="font-extrabold text-primary"><?= h($site['name']) ?></span>
      <button @click="mobileMenu = false" class="text-xl">✕</button>
    </div>
    <nav class="space-y-1 text-sm font-semibold">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="block py-2.5 border-b text-gray-700"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block py-2.5 border-b text-gray-700">납품사례</a>
      <a href="/blog/list.php" class="block py-2.5 border-b text-gray-700">블로그</a>
      <a href="/faq.php" class="block py-2.5 border-b text-gray-700">FAQ</a>
      <a href="/admin/" class="block py-2.5 text-gray-400 text-xs">관리자</a>
    </nav>
  </aside>
</div>
