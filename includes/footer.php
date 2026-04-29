<?php
$site = (require __DIR__ . '/config.php')['site'];
?>
</main>

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
        <li><a href="#" class="hover:text-accent">회사소개</a></li>
        <li><a href="#" class="hover:text-accent">이용약관</a></li>
        <li><a href="#" class="hover:text-accent">개인정보처리방침</a></li>
        <li><a href="#" class="hover:text-accent">배송·교환·반품</a></li>
      </ul>
    </div>
  </div>
  <div class="border-t border-white/10 py-4 text-center text-xs text-white/40">
    © <?= date('Y') ?> <?= h($site['company']) ?>. All rights reserved. — 데모용 페이지
  </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="/assets/js/main.js"></script>
</body>
</html>
