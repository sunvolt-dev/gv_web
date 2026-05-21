<?php
require_once __DIR__ . '/../../includes/user_auth.php';

if (is_logged_in()) { header('Location: /'); exit; }

$errors = [];
$old = ['email' => '', 'name' => '', 'phone' => '', 'address' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old['email']   = trim((string)($_POST['email'] ?? ''));
    $old['name']    = trim((string)($_POST['name'] ?? ''));
    $old['phone']   = trim((string)($_POST['phone'] ?? ''));
    $old['address'] = trim((string)($_POST['address'] ?? ''));
    $password       = (string)($_POST['password'] ?? '');
    $password2      = (string)($_POST['password2'] ?? '');

    if ($password !== $password2) {
        $errors[] = '비밀번호 확인이 일치하지 않습니다.';
    }

    if (empty($errors)) {
        $r = user_register($old['email'], $password, $old['name'], $old['phone'], $old['address']);
        if ($r['ok']) {
            header('Location: /mypage/?welcome=1');
            exit;
        }
        $errors = $r['errors'];
    }
}

$page_title = '회원가입';
require __DIR__ . '/../../includes/header.php';
?>

<section class="max-w-md mx-auto px-4 py-12 md:py-16">
  <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-2 text-center">회원가입</h1>
  <p class="text-sm text-gray-500 mb-8 text-center">썬볼트 배터리몰의 다양한 혜택을 받아보세요</p>

  <?php if ($errors): ?>
  <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
    <?php foreach ($errors as $e): ?><div>⚠ <?= h($e) ?></div><?php endforeach; ?>
  </div>
  <?php endif; ?>

  <form method="post" class="space-y-3 bg-white border rounded-2xl p-6" id="register_form">
    <div>
      <label class="text-xs font-semibold text-gray-600">이메일 *</label>
      <div class="mt-1 flex gap-2">
        <input type="email" name="email" id="email" required value="<?= h($old['email']) ?>"
               class="flex-1 h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
        <button type="button" id="check_email_btn"
                class="shrink-0 h-11 px-4 rounded-lg bg-primary text-white text-sm font-bold hover:bg-primary-light transition disabled:opacity-50">
          중복 확인
        </button>
      </div>
      <div id="email_status" class="mt-1 text-xs min-h-[18px]"></div>
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">비밀번호 * (6자 이상)</label>
      <input type="password" name="password" id="password" required minlength="6"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">비밀번호 확인 *</label>
      <input type="password" name="password2" id="password2" required minlength="6"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
      <div id="pw_status" class="mt-1 text-xs min-h-[18px]"></div>
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">이름 *</label>
      <input type="text" name="name" required value="<?= h($old['name']) ?>"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">연락처</label>
      <input type="text" name="phone" value="<?= h($old['phone']) ?>" placeholder="010-1234-5678"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">주소</label>
      <input type="text" name="address" value="<?= h($old['address']) ?>" placeholder="배송지 기본 주소"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>

    <button type="submit" class="w-full h-11 mt-4 rounded-lg bg-primary text-white font-bold hover:bg-primary-light transition">
      회원가입
    </button>

    <p class="text-center text-sm text-gray-500 pt-2">
      이미 회원이신가요?
      <a href="/auth/login.php" class="text-primary font-bold hover:text-accent-dark">로그인</a>
    </p>
  </form>
</section>

<script>
(() => {
  const emailInput  = document.getElementById('email');
  const checkBtn    = document.getElementById('check_email_btn');
  const emailStatus = document.getElementById('email_status');
  const pw1         = document.getElementById('password');
  const pw2         = document.getElementById('password2');
  const pwStatus    = document.getElementById('pw_status');
  const form        = document.getElementById('register_form');

  // 이메일 중복 확인 상태
  let emailVerified = false;
  let verifiedFor   = '';

  function setEmailStatus(message, level) {
    const colors = { ok: 'text-emerald-600', error: 'text-red-600', warn: 'text-amber-600' };
    emailStatus.className = 'mt-1 text-xs min-h-[18px] font-semibold ' + (colors[level] || 'text-gray-500');
    emailStatus.textContent = message;
  }

  // 중복 확인 버튼
  checkBtn.addEventListener('click', async () => {
    const email = emailInput.value.trim();
    if (!email) { setEmailStatus('이메일을 먼저 입력해주세요.', 'warn'); return; }

    checkBtn.disabled = true;
    setEmailStatus('확인 중…', 'warn');

    try {
      const res  = await fetch('/auth/check_email.php?email=' + encodeURIComponent(email));
      const data = await res.json();
      setEmailStatus((data.available ? '✓ ' : '✗ ') + data.message, data.level);
      if (data.available) {
        emailVerified = true;
        verifiedFor   = email.toLowerCase();
      } else {
        emailVerified = false;
      }
    } catch (e) {
      setEmailStatus('서버 오류: ' + e.message, 'error');
    } finally {
      checkBtn.disabled = false;
    }
  });

  // 이메일 변경되면 검증 상태 리셋
  emailInput.addEventListener('input', () => {
    if (emailVerified && emailInput.value.trim().toLowerCase() !== verifiedFor) {
      emailVerified = false;
      setEmailStatus('이메일이 변경되었습니다. 다시 확인해주세요.', 'warn');
    }
  });

  // 비밀번호 일치 실시간 비교
  function checkPwMatch() {
    const a = pw1.value;
    const b = pw2.value;
    if (b.length === 0) {
      pwStatus.textContent = '';
      pwStatus.className   = 'mt-1 text-xs min-h-[18px]';
      return;
    }
    if (a === b) {
      pwStatus.textContent = '✓ 비밀번호가 일치합니다';
      pwStatus.className   = 'mt-1 text-xs min-h-[18px] font-semibold text-emerald-600';
    } else {
      pwStatus.textContent = '✗ 비밀번호가 일치하지 않습니다';
      pwStatus.className   = 'mt-1 text-xs min-h-[18px] font-semibold text-red-600';
    }
  }
  pw1.addEventListener('input', checkPwMatch);
  pw2.addEventListener('input', checkPwMatch);

  // 폼 제출 전 중복 확인 강제 (선택적 - UX 보강용)
  form.addEventListener('submit', (e) => {
    if (!emailVerified || emailInput.value.trim().toLowerCase() !== verifiedFor) {
      e.preventDefault();
      setEmailStatus('이메일 중복 확인을 먼저 진행해주세요.', 'warn');
      checkBtn.focus();
      return;
    }
    if (pw1.value !== pw2.value) {
      e.preventDefault();
      pwStatus.textContent = '✗ 비밀번호가 일치하지 않습니다';
      pwStatus.className   = 'mt-1 text-xs min-h-[18px] font-semibold text-red-600';
      pw2.focus();
    }
  });
})();
</script>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
