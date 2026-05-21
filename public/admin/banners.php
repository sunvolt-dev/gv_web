<?php
require_once __DIR__ . '/auth.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            db_exec('DELETE FROM banners WHERE id = ?', [$id]);
            $_SESSION['flash'] = "배너 #$id 삭제 완료";
        }
    } elseif ($action === 'reorder') {
        $orders = $_POST['order'] ?? [];
        foreach ($orders as $id => $sort) {
            db_exec('UPDATE banners SET sort_order = ? WHERE id = ?', [(int)$sort, (int)$id]);
        }
        $_SESSION['flash'] = "정렬 순서 저장 완료";
    } elseif ($action === 'toggle') {
        $id = (int)($_POST['id'] ?? 0);
        db_exec('UPDATE banners SET published = 1 - published WHERE id = ?', [$id]);
    }
    header('Location: /admin/banners.php');
    exit;
}

$rows = db_all('SELECT * FROM banners ORDER BY sort_order, id');

$admin_page = 'banners';
$admin_title = '배너 관리';
require __DIR__ . '/_layout_top.php';
?>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div><?php unset($_SESSION['flash']); endif; ?>

<div class="bg-white rounded-xl border overflow-hidden">
  <div class="px-5 py-4 border-b flex items-center justify-between">
    <div>
      <h2 class="font-extrabold text-primary">메인 히어로 배너 (<?= count($rows) ?>)</h2>
      <p class="text-xs text-gray-500 mt-0.5">메인 페이지 상단 슬라이더에 표시됩니다. sort 값이 작을수록 먼저 노출.</p>
    </div>
    <a href="/admin/banner_form.php"
       class="h-9 px-4 inline-flex items-center text-sm font-bold rounded-lg bg-accent text-primary hover:bg-accent-dark">
      + 새 배너 추가
    </a>
  </div>

  <form method="post">
    <input type="hidden" name="action" value="reorder">
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 text-xs text-gray-500">
          <tr>
            <th class="text-center px-4 py-3 w-16">순서</th>
            <th class="text-left px-4 py-3 w-32">이미지</th>
            <th class="text-left px-4 py-3">제목 / 부제</th>
            <th class="text-left px-4 py-3 w-32">CTA</th>
            <th class="text-center px-4 py-3 w-20">상태</th>
            <th class="px-4 py-3 w-32"></th>
          </tr>
        </thead>
        <tbody class="divide-y">
          <?php if (empty($rows)): ?>
          <tr><td colspan="6" class="text-center py-12 text-gray-400">등록된 배너가 없습니다.</td></tr>
          <?php else: foreach ($rows as $r): ?>
          <tr class="hover:bg-gray-50">
            <td class="px-4 py-3 text-center">
              <input type="number" name="order[<?= (int)$r['id'] ?>]" value="<?= (int)$r['sort_order'] ?>"
                     class="w-14 h-8 px-2 text-center border border-gray-300 rounded text-sm">
            </td>
            <td class="px-4 py-3">
              <img src="<?= h($r['image_url']) ?>" class="w-28 h-16 object-cover rounded bg-gray-100">
            </td>
            <td class="px-4 py-3">
              <a href="/admin/banner_form.php?id=<?= (int)$r['id'] ?>" class="font-semibold hover:text-primary line-clamp-1">
                <?= h($r['title']) ?>
              </a>
              <?php if ($r['subtitle']): ?>
              <div class="text-xs text-gray-500 mt-0.5 line-clamp-1"><?= h($r['subtitle']) ?></div>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-xs">
              <?php if ($r['cta_text']): ?>
                <div class="font-semibold"><?= h($r['cta_text']) ?></div>
                <div class="text-gray-400 line-clamp-1"><?= h($r['cta_url']) ?></div>
              <?php else: ?>
                <span class="text-gray-300">-</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 text-center">
              <?php if ($r['published']): ?>
                <span class="px-2 py-0.5 text-[11px] rounded bg-emerald-100 text-emerald-700 font-bold">공개</span>
              <?php else: ?>
                <span class="px-2 py-0.5 text-[11px] rounded bg-gray-100 text-gray-500">숨김</span>
              <?php endif; ?>
            </td>
            <td class="px-4 py-3 whitespace-nowrap">
              <a href="/admin/banner_form.php?id=<?= (int)$r['id'] ?>" class="text-xs text-primary font-semibold">수정</a>
              <span class="text-gray-300">|</span>
              <button type="button" class="text-xs text-gray-600"
                      onclick="document.getElementById('toggle_<?= (int)$r['id'] ?>').submit();">
                <?= $r['published'] ? '숨김' : '공개' ?>
              </button>
              <span class="text-gray-300">|</span>
              <button type="button" class="text-xs text-red-500 hover:text-red-700"
                      onclick="if(confirm('삭제하시겠습니까?')){document.getElementById('del_<?= (int)$r['id'] ?>').submit();}">삭제</button>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
    <?php if (!empty($rows)): ?>
    <div class="px-5 py-4 border-t flex justify-end">
      <button class="h-9 px-4 text-sm font-bold rounded-lg bg-primary text-white hover:bg-primary-light">
        순서 저장
      </button>
    </div>
    <?php endif; ?>
  </form>
</div>

<!-- 별도 폼 (포스트백 분리) -->
<?php foreach ($rows as $r): ?>
<form method="post" id="del_<?= (int)$r['id'] ?>" class="hidden">
  <input type="hidden" name="action" value="delete">
  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
</form>
<form method="post" id="toggle_<?= (int)$r['id'] ?>" class="hidden">
  <input type="hidden" name="action" value="toggle">
  <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
</form>
<?php endforeach; ?>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
