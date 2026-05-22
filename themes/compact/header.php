<?php /* [compact 테마] PC 헤더 — 정보밀집, 다단 네비 */ ?>
<div class="sticky top-0 z-40">
  <!-- 유틸 띠 -->
  <div class="hidden md:block bg-primary-dark text-white/70 text-[11px]">
    <div class="max-w-[1280px] mx-auto px-4 h-7 flex items-center justify-between">
      <span>📞 <?= h($site['phone']) ?> · 평일 09-18시 · 전국 무료배송(5만원↑)</span>
      <div class="flex gap-3">
        <?php if ($current_user): ?>
        <a href="/mypage/" class="hover:text-accent"><?= h($current_user['name']) ?>님</a>
        <a href="/auth/logout.php" class="hover:text-accent">로그아웃</a>
        <?php else: ?>
        <a href="/auth/login.php" class="hover:text-accent">로그인</a>
        <a href="/auth/register.php" class="hover:text-accent">회원가입</a>
        <?php endif; ?>
        <a href="/mypage/orders.php" class="hover:text-accent">주문조회</a>
        <a href="/faq.php" class="hover:text-accent">고객지원</a>
        <a href="/admin/" class="hover:text-accent">관리자</a>
      </div>
    </div>
  </div>
  <!-- 메인 바 -->
  <div class="bg-white border-b border-gray-200">
    <div class="max-w-[1280px] mx-auto px-4 h-14 flex items-center gap-4">
      <button class="md:hidden p-1.5 -ml-1" @click="mobileMenu = true" aria-label="메뉴">
        <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
      </button>
      <a href="/" class="flex items-center gap-1.5 shrink-0">
        <span class="w-7 h-7 rounded bg-primary flex items-center justify-center text-accent font-black text-sm">⚡</span>
        <span class="font-extrabold text-primary"><?= h($site['name']) ?></span>
      </a>
      <form action="/shop/list.php" method="get" class="hidden md:flex flex-1 max-w-2xl relative">
        <input type="text" name="q" placeholder="상품명·모델명 검색" value="<?= h($_GET['q'] ?? '') ?>"
               class="w-full h-9 pl-3 pr-10 border-2 border-primary rounded text-sm focus:outline-none focus:ring-1 focus:ring-accent">
        <button type="submit" class="absolute right-0 top-0 h-9 w-9 flex items-center justify-center bg-primary text-white rounded-r" aria-label="검색">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="7"/><path d="M21 21l-4.5-4.5"/></svg>
        </button>
      </form>
      <div class="flex items-center gap-3 shrink-0 text-xs font-semibold text-gray-600 ml-auto">
        <a href="/cart.php" class="relative hover:text-accent">
          🛒 장바구니<?php if (cart_count() > 0): ?> <span class="text-accent font-bold"><?= cart_count() ?></span><?php endif; ?>
        </a>
        <a href="<?= is_logged_in() ? '/mypage/' : '/auth/login.php' ?>" class="hover:text-accent">👤 마이</a>
      </div>
    </div>
  </div>
  <!-- 카테고리 바 -->
  <nav class="hidden md:block bg-primary text-white">
    <ul class="max-w-[1280px] mx-auto px-4 flex items-center gap-0 h-9 text-[13px] font-semibold">
      <li><a href="/" class="px-3 leading-9 hover:bg-white/10">HOME</a></li>
      <?php foreach ($cats_l1 as $c): ?>
      <li><a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="px-3 leading-9 hover:bg-white/10"><?= h($c['ca_name']) ?></a></li>
      <?php endforeach; ?>
      <li><a href="/cases/list.php" class="px-3 leading-9 hover:bg-white/10">납품사례</a></li>
      <li><a href="/blog/list.php" class="px-3 leading-9 hover:bg-white/10">블로그</a></li>
      <li><a href="/faq.php" class="px-3 leading-9 hover:bg-white/10">FAQ</a></li>
    </ul>
  </nav>
</div>

<div x-show="mobileMenu" x-cloak class="fixed inset-0 z-50 md:hidden" x-transition.opacity>
  <div class="absolute inset-0 bg-black/50" @click="mobileMenu = false"></div>
  <aside class="absolute left-0 top-0 bottom-0 w-72 bg-white"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0">
    <div class="bg-primary text-white px-4 h-12 flex items-center justify-between">
      <span class="font-bold text-sm"><?= h($site['name']) ?></span>
      <button @click="mobileMenu = false" class="text-lg">✕</button>
    </div>
    <nav class="text-sm">
      <?php foreach ($cats_l1 as $c): ?>
      <a href="<?= h(url('/shop/list.php', ['ca_id' => $c['ca_id']])) ?>" class="block px-4 py-2.5 border-b font-semibold text-gray-700"><?= h($c['ca_name']) ?></a>
      <?php endforeach; ?>
      <a href="/cases/list.php" class="block px-4 py-2.5 border-b font-semibold text-gray-700">납품사례</a>
      <a href="/blog/list.php" class="block px-4 py-2.5 border-b font-semibold text-gray-700">블로그</a>
      <a href="/faq.php" class="block px-4 py-2.5 border-b font-semibold text-gray-700">FAQ</a>
      <a href="/admin/" class="block px-4 py-2.5 text-gray-400 text-xs">관리자</a>
    </nav>
  </aside>
</div>
