<?php
require_once __DIR__ . '/auth.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $u = trim((string)($_POST['username'] ?? ''));
    $p = (string)($_POST['password'] ?? '');
    if (admin_login($u, $p)) {
        $redirect = $_GET['redirect'] ?? '/admin/';
        if (!preg_match('#^/admin/#', $redirect)) $redirect = '/admin/';
        header("Location: $redirect");
        exit;
    }
    $error = '아이디 또는 비밀번호가 일치하지 않습니다.';
}
?>
<!DOCTYPE html>
<html lang="ko">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>관리자 로그인</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/orioncactus/pretendard@v1.3.9/dist/web/static/pretendard.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script>
  tailwind.config = { theme: { extend: { colors: {
    primary: { DEFAULT: '#0A2540', dark: '#061829' },
    accent:  { DEFAULT: '#FFC107' },
  }, fontFamily: { sans: ['Pretendard', 'sans-serif'] } } } };
</script>
</head>
<body class="font-sans bg-gradient-to-br from-primary to-primary-dark min-h-screen flex items-center justify-center p-4">

<div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-8 md:p-10">
  <div class="text-center mb-8">
    <div class="w-14 h-14 mx-auto rounded-xl bg-primary flex items-center justify-center mb-3">
      <span class="text-accent text-2xl font-black">⚡</span>
    </div>
    <h1 class="text-2xl font-extrabold text-primary">관리자 로그인</h1>
    <p class="text-sm text-gray-500 mt-1">썬볼트 배터리몰 관리 시스템</p>
  </div>

  <?php if ($error): ?>
  <div class="mb-4 p-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
    <?= htmlspecialchars($error) ?>
  </div>
  <?php endif; ?>

  <form method="post" class="space-y-4">
    <div>
      <label class="text-xs font-semibold text-gray-600">아이디</label>
      <input type="text" name="username" required value="admin" autofocus
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">비밀번호</label>
      <input type="password" name="password" required
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <button type="submit"
            class="w-full h-11 rounded-lg bg-primary text-white font-bold hover:bg-primary-dark transition">
      로그인
    </button>
  </form>

  <div class="mt-6 p-3 bg-gray-50 rounded-lg text-xs text-gray-600">
    <p class="font-bold mb-1">📌 데모 계정</p>
    <p>아이디: <code class="bg-white px-1.5 py-0.5 rounded">admin</code></p>
    <p>비밀번호: <code class="bg-white px-1.5 py-0.5 rounded">admin1234</code></p>
  </div>

  <div class="mt-6 text-center">
    <a href="/" class="text-xs text-gray-500 hover:text-primary">← 사이트로 돌아가기</a>
  </div>
</div>

</body>
</html>
