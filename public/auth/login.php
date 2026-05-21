<?php
require_once __DIR__ . '/../../includes/user_auth.php';

if (is_logged_in()) { header('Location: /mypage/'); exit; }

$error = '';
$email = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim((string)($_POST['email'] ?? ''));
    $password = (string)($_POST['password'] ?? '');
    if (user_login($email, $password)) {
        $r = $_GET['redirect'] ?? '/mypage/';
        if (!preg_match('#^/[^/]#', $r)) $r = '/mypage/';
        header("Location: $r");
        exit;
    }
    $error = '이메일 또는 비밀번호가 일치하지 않습니다.';
}

$page_title = '로그인';
require __DIR__ . '/../../includes/header.php';
?>

<section class="max-w-md mx-auto px-4 py-12 md:py-16">
  <h1 class="text-2xl md:text-3xl font-extrabold text-primary mb-2 text-center">로그인</h1>
  <p class="text-sm text-gray-500 mb-8 text-center">썬볼트 배터리몰에 오신 것을 환영합니다</p>

  <?php if ($error): ?>
  <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
    ⚠ <?= h($error) ?>
  </div>
  <?php endif; ?>

  <form method="post" class="space-y-3 bg-white border rounded-2xl p-6">
    <div>
      <label class="text-xs font-semibold text-gray-600">이메일</label>
      <input type="email" name="email" required value="<?= h($email) ?>" autofocus
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">비밀번호</label>
      <input type="password" name="password" required
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>

    <button type="submit" class="w-full h-11 mt-4 rounded-lg bg-primary text-white font-bold hover:bg-primary-light transition">
      로그인
    </button>

    <p class="text-center text-sm text-gray-500 pt-2">
      아직 회원이 아니신가요?
      <a href="/auth/register.php" class="text-primary font-bold hover:text-accent-dark">회원가입</a>
    </p>
  </form>
</section>

<?php require __DIR__ . '/../../includes/footer.php'; ?>
