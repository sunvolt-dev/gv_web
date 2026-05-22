<?php /* [bold 테마] PC 헤더 — 굵고 강렬, 대형 로고 */ ?>
<header class="sticky top-0 z-40 bg-primary text-white">
  <div class="max-w-7xl mx-auto px-5 h-20 flex items-center justify-between gap-4">

    <button class="md:hidden p-2 -ml-2" @click="mobileMenu = true" aria-label="메뉴">
      <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
    </button>

    <a href="/" class="flex items-center gap-2 shrink-0">
      <span class="w-11 h-11 rounded-xl bg-accent flex items-center justify-center text-primary font-black text-2xl">⚡</span>
      <span class="font-black text-xl md:text-2xl tracking-tight uppercase"><?= h($site['name']) ?></span>
    </a>

    <nav class="hidden md:flex items-center gap-1 font-black text-sm uppercase">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>"
         class="px-3 py-2 rounded-lg hover:bg-accent hover:text-primary transition"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/blog/list.php" class="px-3 py-2 rounded-lg hover:bg-accent hover:text-primary transition">블로그</a>
    </nav>

    <div class="flex items-center gap-2 shrink-0">
      <a href="/cart.php" class="relative w-11 h-11 rounded-xl bg-white/15 hover:bg-accent hover:text-primary flex items-center justify-center transition" aria-label="장바구니">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/><path d="M3 3h2.5l2.6 13.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/></svg>
        <?php if (cart_count() > 0): ?>
        <span class="absolute -top-1 -right-1 min-w-[20px] h-5 px-1 rounded-full bg-accent text-primary text-[11px] font-black flex items-center justify-center"><?= cart_count() ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= is_logged_in() ? '/mypage/' : '/auth/login.php' ?>"
         class="hidden md:flex h-11 px-4 rounded-xl bg-accent text-primary font-black text-sm items-center uppercase">
        <?= is_logged_in() ? 'MY' : 'LOGIN' ?>
      </a>
    </div>
  </div>
</header>

<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 md:hidden" x-transition.opacity>
  <div class="absolute inset-0 bg-black/50" @click="mobileMenu = false"></div>
  <aside class="absolute left-0 top-0 bottom-0 w-72 bg-primary text-white p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="flex justify-between items-center mb-6">
      <span class="font-black text-lg uppercase"><?= h($site['name']) ?></span>
      <button @click="mobileMenu = false" class="text-2xl">✕</button>
    </div>
    <nav class="space-y-2 font-black uppercase text-sm">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="block py-3 px-3 rounded-lg bg-white/10"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block py-3 px-3 rounded-lg bg-white/10">납품사례</a>
      <a href="/blog/list.php" class="block py-3 px-3 rounded-lg bg-white/10">블로그</a>
      <a href="/faq.php" class="block py-3 px-3 rounded-lg bg-white/10">FAQ</a>
      <a href="/admin/" class="block py-2 text-white/40 text-xs">관리자</a>
    </nav>
  </aside>
</div>
