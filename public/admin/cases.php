<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id) {
        db_exec('DELETE FROM case_studies WHERE id = ?', [$id]);
        $_SESSION['flash'] = "납품사례 #$id 삭제 완료";
    }
    header('Location: /admin/cases.php');
    exit;
}

$rows = db_all(
    "SELECT cs.*,
            (SELECT image_url FROM case_images WHERE case_id = cs.id ORDER BY sort_order, id LIMIT 1) AS first_image,
            (SELECT COUNT(*) FROM case_images WHERE case_id = cs.id) AS img_count
       FROM case_studies cs
   ORDER BY cs.id DESC"
);

$admin_page = 'cases';
$admin_title = '납품사례 관리';
require __DIR__ . '/_layout_top.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div><?php unset($_SESSION['flash']); endif; ?>

<div class="bg-white rounded-xl border overflow-hidden">
  <div class="px-5 py-4 border-b flex items-center justify-between">
    <h2 class="font-extrabold text-primary">납품사례 (<?= count($rows) ?>)</h2>
    <a href="/admin/case_form.php"
       class="h-9 px-4 inline-flex items-center text-sm font-bold rounded-lg bg-accent text-primary hover:bg-accent-dark">
      + 새 사례 등록
    </a>
  </div>

  <div class="overflow-x-auto">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 text-xs text-gray-500">
        <tr>
          <th class="text-left px-5 py-3 w-16">ID</th>
          <th class="text-left px-5 py-3 w-20">사진</th>
          <th class="text-left px-5 py-3">제목 / 고객사</th>
          <th class="text-left px-5 py-3 w-40">납품 규모</th>
          <th class="text-left px-5 py-3 w-28">납품일</th>
          <th class="text-center px-5 py-3 w-20">상태</th>
          <th class="px-5 py-3 w-32"></th>
        </tr>
      </thead>
      <tbody class="divide-y">
        <?php if (empty($rows)): ?>
        <tr><td colspan="7" class="text-center py-12 text-gray-400">등록된 사례가 없습니다.</td></tr>
        <?php else: foreach ($rows as $r): ?>
        <tr class="hover:bg-gray-50">
          <td class="px-5 py-3 text-gray-500"><?= (int)$r['id'] ?></td>
          <td class="px-5 py-3 relative">
            <?php if ($r['first_image']): ?>
            <img src="<?= h($r['first_image']) ?>" class="w-12 h-12 rounded object-cover bg-gray-100">
            <span class="absolute -top-1 -right-1 px-1.5 py-0.5 text-[9px] bg-primary text-white rounded-full"><?= (int)$r['img_count'] ?></span>
            <?php else: ?>
            <div class="w-12 h-12 rounded bg-gray-100 flex items-center justify-center text-gray-300">📦</div>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3">
            <a href="/admin/case_form.php?id=<?= (int)$r['id'] ?>" class="font-semibold hover:text-primary line-clamp-1">
              <?= h($r['title']) ?>
            </a>
            <div class="text-xs text-gray-500 mt-0.5">🏢 <?= h($r['client_name']) ?></div>
          </td>
          <td class="px-5 py-3 text-xs"><?= h($r['scale']) ?></td>
          <td class="px-5 py-3 text-xs text-gray-500"><?= h($r['delivered_at'] ?: '-') ?></td>
          <td class="px-5 py-3 text-center">
            <?php if ($r['published']): ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-emerald-100 text-emerald-700 font-bold">공개</span>
            <?php else: ?>
              <span class="px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-500">비공개</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3 whitespace-nowrap">
            <a href="/cases/view.php?id=<?= (int)$r['id'] ?>" target="_blank"
               class="text-xs text-gray-500 hover:text-primary">보기</a>
            <span class="text-gray-300">|</span>
            <a href="/admin/case_form.php?id=<?= (int)$r['id'] ?>"
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
