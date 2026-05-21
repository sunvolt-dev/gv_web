<?php
require_once __DIR__ . '/../../includes/user_auth.php';
require_login();

$u = user();
$msg = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim((string)($_POST['name'] ?? ''));
    $phone   = trim((string)($_POST['phone'] ?? ''));
    $address = trim((string)($_POST['address'] ?? ''));
    $new_pw  = (string)($_POST['new_password'] ?? '');
    $new_pw2 = (string)($_POST['new_password2'] ?? '');

    if ($name === '') $errors[] = '이름은 필수입니다.';

    if ($new_pw !== '' || $new_pw2 !== '') {
        if (strlen($new_pw) < 6) $errors[] = '새 비밀번호는 6자 이상이어야 합니다.';
        if ($new_pw !== $new_pw2) $errors[] = '새 비밀번호 확인이 일치하지 않습니다.';
    }

    if (empty($errors)) {
        if ($new_pw !== '') {
            db_exec('UPDATE users SET name=?, phone=?, address=?, password_hash=? WHERE id=?',
                    [$name, $phone, $address, password_hash($new_pw, PASSWORD_DEFAULT), $u['id']]);
        } else {
            db_exec('UPDATE users SET name=?, phone=?, address=? WHERE id=?',
                    [$name, $phone, $address, $u['id']]);
        }
        $msg = '회원 정보가 수정되었습니다.';
        $u = user(); // refresh cache (note: cache is static, may need manual reload)
        $u['name'] = $name; $u['phone'] = $phone; $u['address'] = $address;
    }
}

$page_title = '회원정보 수정';
require __DIR__ . '/../../includes/header.php';
?>

<section class="max-w-3xl mx-auto px-4 py-8 md:py-12">
  <div class="grid lg:grid-cols-[260px_1fr] gap-6">

    <aside class="bg-white border rounded-xl overflow-hidden">
      <div class="bg-primary text-white p-5">
        <div class="text-xs text-white/60 mb-1">안녕하세요</div>
        <div class="font-extrabold text-lg"><?= h($u['name']) ?>님</div>
      </div>
      <nav class="p-2 text-sm">
        <a href="/mypage/" class="block px-3 py-2 rounded-lg hover:bg-gray-50">📊 마이홈</a>
        <a href="/mypage/orders.php" class="block px-3 py-2 rounded-lg hover:bg-gray-50">📦 주문 내역</a>
        <a href="/mypage/edit.php" class="block px-3 py-2 rounded-lg bg-gray-100 font-bold text-primary">✏️ 회원정보 수정</a>
        <a href="/auth/logout.php" class="block px-3 py-2 rounded-lg text-red-600 hover:bg-red-50 mt-2 border-t pt-3">🚪 로그아웃</a>
      </nav>
    </aside>

    <main>
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-6">회원정보 수정</h1>

      <?php if ($msg): ?>
      <div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
        ✅ <?= h($msg) ?>
      </div>
      <?php endif; ?>

      <?php if ($errors): ?>
      <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
        <?php foreach ($errors as $e): ?><div>⚠ <?= h($e) ?></div><?php endforeach; ?>
      </div>
      <?php endif; ?>

      <form method="post" class="bg-white border rounded-xl p-5 md:p-7 space-y-4">
        <div>
          <label class="text-xs font-semibold text-gray-600">이메일 (변경 불가)</label>
          <input type="email" disabled value="<?= h($u['email']) ?>"
                 class="mt-1 w-full h-11 px-3 border border-gray-200 bg-gray-50 rounded-lg text-gray-500">
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">이름 *</label>
          <input type="text" name="name" required value="<?= h($u['name']) ?>"
                 class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">연락처</label>
          <input type="text" name="phone" value="<?= h($u['phone']) ?>"
                 class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">주소</label>
          <input type="text" name="address" value="<?= h($u['address']) ?>"
                 class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
        </div>

        <div class="border-t pt-4">
          <h3 class="font-bold text-primary mb-3 text-sm">비밀번호 변경 (선택)</h3>
          <div class="space-y-3">
            <div>
              <label class="text-xs font-semibold text-gray-600">새 비밀번호</label>
              <input type="password" name="new_password" minlength="6" placeholder="변경 시에만 입력"
                     class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            <div>
              <label class="text-xs font-semibold text-gray-600">새 비밀번호 확인</label>
              <input type="password" name="new_password2" minlength="6"
                     class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
          </div>
        </div>

        <button type="submit" class="w-full h-11 mt-2 rounded-lg bg-primary text-white font-bold hover:bg-primary-light transition">
          저장하기
        </button>
      </form>
    </main>
  </div>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
