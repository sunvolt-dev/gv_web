<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        db_exec('DELETE FROM posts WHERE id = ?', [$id]);
        $_SESSION['flash'] = "블로그 글 #$id 삭제 완료";
    }
    header('Location: /admin/posts.php');
    exit;
}

$rows = db_all('SELECT * FROM posts ORDER BY id DESC');

$admin_page = 'posts';
$admin_title = '블로그 관리';
require __DIR__ . '/_layout_top.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div><?php unset($_SESSION['flash']); endif; ?>

<div class="bg-white rounded-xl border overflow-hidden">
  <div class="px-5 py-4 border-b flex items-center justify-between">
    <h2 class="font-extrabold text-primary">블로그 글 (<?= count($rows) ?>)</h2>
    <a href="/admin/post_form.php"
       class="h-9 px-4 inline-flex items-center text-sm font-bold rounded-lg bg-accent text-primary hover:bg-accent-dark">
      + 새 글 작성
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs text-gray-500">
        <tr>
          <th class="text-left px-5 py-3 w-16">ID</th>
          <th class="text-left px-5 py-3 w-20">썸네일</th>
          <th class="text-left px-5 py-3">제목</th>
          <th class="text-right px-5 py-3 w-24">조회수</th>
          <th class="text-center px-5 py-3 w-20">상태</th>
          <th class="text-left px-5 py-3 w-32">등록일</th>
          <th class="px-5 py-3 w-32"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="text-center py-12 text-gray-400">등록된 글이 없습니다.</td></tr>
        <?php else: foreach ($rows as $r): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3 text-gray-500"><?= (int)$r['id'] ?></td>
          <td class="px-5 py-3">
            <?php if ($r['thumbnail']): ?>
            <img src="<?= h($r['thumbnail']) ?>" class="w-12 h-12 rounded object-cover bg-gray-100">
            <?php else: ?>
            <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center text-gray-300">📝</div>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3">
            <a href="/admin/post_form.php?id=<?= (int)$r['id'] ?>" class="font-semibold hover:text-primary line-clamp-1">
              <?= h($r['title']) ?>
            </a>
            <?php if ($r['summary']): ?>
            <div class="text-xs text-gray-500 mt-0.5 line-clamp-1"><?= h($r['summary']) ?></div>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 text-right font-bold"><?= number_format((int)$r['views']) ?></td>
          <td class="px-5 py-3 text-center">
            <?php if ($r['published']): ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-emerald-100 text-emerald-700 font-bold">공개</span>
            <?php else: ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-500">비공개</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 text-xs text-gray-500"><?= h(date('Y.m.d', strtotime($r['created_at']))) ?></td>
          <td class="px-5 py-3 whitespace-nowrap">
            <a href="/blog/view.php?id=<?= (int)$r['id'] ?>" target="_blank"
               class="text-xs text-gray-500 hover:text-primary">보기</a>
            <span class="text-gray-300">|</span>
            <a href="/admin/post_form.php?id=<?= (int)$r['id'] ?>"
               class="text-xs text-primary font-semibold">수정</a>
            <span class="text-gray-300">|</span>
            <form method="post" class="inline" onsubmit="return confirm('삭제하시겠습니까?');">
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
              <button class="text-xs text-red-500 hover:text-red-700">삭제</button>
            </form>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
