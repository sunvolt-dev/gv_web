<?php
$site = (require __DIR__ . '/config.php')['site'];
$IS_MOBILE = function_exists('is_mobile') ? is_mobile() : false;
?>
</main>

<?php if ($IS_MOBILE): /* ================= 모바일 푸터 + 하단 탭바 ================= */ ?>

<footer class="mt-10 bg-primary-dark text-white/60 text-xs">
  <div class="px-4 py-6 space-y-2">
    <div class="font-bold text-white text-sm"><?= h($site['name']) ?></div>
    <p class="text-white/50 leading-relaxed">
      <?= h($site['company']) ?> · 사업자 <?= h($site['business_no']) ?><br>
      <?= h($site['address']) ?>
    </p>
    <p class="text-accent font-bold text-base pt-1">고객센터 <?= h($site['phone']) ?></p>
    <div class="flex flex-wrap gap-x-3 gap-y-1 pt-2 text-white/50">
      <a href="/faq.php">FAQ</a><span>·</span>
      <a href="/cases/list.php">납품사례</a><span>·</span>
      <a href="/blog/list.php">블로그</a><span>·</span>
      <a href="/admin/">관리자</a>
    </div>
    <a href="<?= h(toggle_view_url()) ?>" class="inline-block pt-2 text-white/70 font-semibold">🖥 PC 버전으로 보기</a>
    <p class="text-white/30 pt-2">© <?= date('Y') ?> <?= h($site['company']) ?>. — 데모</p>
  </div>
</footer>

<!-- 하단 고정 탭바 -->
<nav class="fixed bottom-0 inset-x-0 z-40 bg-white border-t border-gray-200 grid grid-cols-4
            shadow-[0_-2px_10px_rgba(0,0,0,0.06)]"
     x-data="{ path: location.pathname }">
  <a href="/" class="flex flex-col items-center justify-center gap-0.5 py-2"
     :class="path === '/' ? 'text-primary' : 'text-gray-400'">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 11l9-8 9 8M5 10v10h14V10"/>
    </svg>
    <span class="text-[10px] font-semibold">홈</span>
  </a>
  <button type="button" @click="mobileMenu = true"
          class="flex flex-col items-center justify-center gap-0.5 py-2 text-gray-400">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <path d="M3 6h18M3 12h18M3 18h18"/>
    </svg>
    <span class="text-[10px] font-semibold">카테고리</span>
  </button>
  <a href="/cart.php" class="relative flex flex-col items-center justify-center gap-0.5 py-2"
     :class="path.indexOf('/cart') === 0 ? 'text-primary' : 'text-gray-400'">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="9" cy="21" r="1.5"/><circle cx="18" cy="21" r="1.5"/>
      <path d="M3 3h2.5l2.6 13.4a2 2 0 0 0 2 1.6h7.7a2 2 0 0 0 2-1.5L21 8H6"/>
    </svg>
    <?php if (cart_count() > 0): ?>
    <span class="absolute top-1 right-1/2 translate-x-3 min-w-[16px] h-4 px-1 rounded-full
                 bg-accent text-primary text-[9px] font-bold flex items-center justify-center">
      <?= cart_count() ?>
    </span>
    <?php endif; ?>
    <span class="text-[10px] font-semibold">장바구니</span>
  </a>
  <a href="<?= is_logged_in() ? '/mypage/' : '/auth/login.php' ?>"
     class="flex flex-col items-center justify-center gap-0.5 py-2"
     :class="path.indexOf('/mypage') === 0 ? 'text-primary' : 'text-gray-400'">
    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
      <circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/>
    </svg>
    <span class="text-[10px] font-semibold">마이페이지</span>
  </a>
</nav>

<?php else: /* ================= PC 푸터 ================= */ ?>

<footer class="mt-20 bg-primary-dark text-white/70">
  <div class="max-w-7xl mx-auto px-4 py-12 grid md:grid-cols-4 gap-8 text-sm">

    <div class="md:col-span-2">
      <div class="flex items-center gap-2 mb-4">
        <div class="w-9 h-9 rounded-lg bg-accent flex items-center justify-center">
          <span class="text-primary font-black text-lg">⚡</span>
        </div>
        <span class="font-bold text-white text-lg"><?= h($site['name']) ?></span>
      </div>
      <p class="leading-relaxed mb-4 text-white/80">
        <?= h($site['tagline']) ?>
      </p>
      <p class="text-xs text-white/50">
        <?= h($site['company']) ?> · 사업자등록번호 <?= h($site['business_no']) ?>
        <br><?= h($site['address']) ?>
      </p>
    </div>

    <div>
      <h4 class="text-white font-bold mb-3">고객센터</h4>
      <p class="text-2xl font-extrabold text-accent mb-2"><?= h($site['phone']) ?></p>
      <p class="text-xs text-white/60">
        평일 09:00 - 18:00<br>
        주말·공휴일 휴무
      </p>
    </div>

    <div>
      <h4 class="text-white font-bold mb-3">이용안내</h4>
      <ul class="space-y-2 text-sm">
        <li><a href="/faq.php" class="hover:text-accent">자주 묻는 질문</a></li>
        <li><a href="/cases/list.php" class="hover:text-accent">납품사례</a></li>
        <li><a href="/blog/list.php" class="hover:text-accent">블로그</a></li>
        <li><a href="#" class="hover:text-accent">이용약관</a></li>
        <li><a href="#" class="hover:text-accent">개인정보처리방침</a></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-white/10 py-4 text-center text-xs text-white/40">
    © <?= date('Y') ?> <?= h($site['company']) ?>. All rights reserved. — 데모용 페이지
    · <a href="<?= h(toggle_view_url()) ?>" class="hover:text-accent">📱 모바일 버전</a>
  </div>
</footer>

<?php endif; /* ================= 푸터 분기 끝 ================= */ ?>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
