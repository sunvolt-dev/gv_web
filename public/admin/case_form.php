<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$is_edit = $id > 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') !== 'delete_image') {
    $title        = trim((string)($_POST['title'] ?? ''));
    $client_name  = trim((string)($_POST['client_name'] ?? ''));
    $scale        = trim((string)($_POST['scale'] ?? ''));
    $delivered_at = trim((string)($_POST['delivered_at'] ?? ''));
    $summary      = trim((string)($_POST['summary'] ?? ''));
    $content      = (string)($_POST['content'] ?? '');
    $published    = isset($_POST['published']) ? 1 : 0;

    if ($title === '')       $errors[] = '제목을 입력해주세요.';
    if ($client_name === '') $errors[] = '고객사명을 입력해주세요.';
    if ($delivered_at === '') $delivered_at = null;

    if (empty($errors)) {
        if ($is_edit) {
            db_exec(
                'UPDATE case_studies
                    SET title=?, client_name=?, scale=?, delivered_at=?, summary=?, content=?, published=?
                  WHERE id=?',
                [$title, $client_name, $scale, $delivered_at, $summary, $content, $published, $id]
            );
            $case_id = $id;
        } else {
            db_exec(
                'INSERT INTO case_studies
                    (title, client_name, scale, delivered_at, summary, content, published)
                 VALUES (?, ?, ?, ?, ?, ?, ?)',
                [$title, $client_name, $scale, $delivered_at, $summary, $content, $published]
            );
            $case_id = (int)db()->lastInsertId();
        }

        // 새 이미지 업로드 처리
        if (!empty($_FILES['images']['name'][0])) {
            $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            $dir = __DIR__ . '/../assets/images/cases/';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);

            $next_order = (int)(db_one('SELECT COALESCE(MAX(sort_order), 0) AS m FROM case_images WHERE case_id = ?', [$case_id])['m']);

            foreach ($_FILES['images']['tmp_name'] as $i => $tmp) {
                if ($_FILES['images']['error'][$i] !== UPLOAD_ERR_OK) continue;
                $mime = $finfo->file($tmp);
                if (!isset($allowed[$mime])) continue;
                $name = 'case_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
                if (move_uploaded_file($tmp, $dir . $name)) {
                    $next_order++;
                    db_exec(
                        'INSERT INTO case_images (case_id, image_url, caption, sort_order) VALUES (?, ?, ?, ?)',
                        [$case_id, '/assets/images/cases/' . $name, '', $next_order]
                    );
                }
            }
        }

        // URL로 이미지 추가
        $url_inputs = $_POST['image_urls'] ?? [];
        foreach ($url_inputs as $url) {
            $url = trim((string)$url);
            if ($url === '' || !preg_match('#^https?://#i', $url)) continue;
            $next_order = ($next_order ?? 0) + 1;
            db_exec(
                'INSERT INTO case_images (case_id, image_url, caption, sort_order) VALUES (?, ?, ?, ?)',
                [$case_id, $url, '', $next_order]
            );
        }

        // 기존 이미지 캡션 업데이트
        $captions = $_POST['captions'] ?? [];
        foreach ($captions as $img_id => $cap) {
            db_exec('UPDATE case_images SET caption = ? WHERE id = ? AND case_id = ?',
                    [trim((string)$cap), (int)$img_id, $case_id]);
        }

        $_SESSION['flash'] = $is_edit ? "납품사례 #$id 수정 완료" : "납품사례 #$case_id 등록 완료";
        header("Location: /admin/case_form.php?id=$case_id");
        exit;
    }
}

// 이미지 삭제
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_image') {
    $img_id = (int)($_POST['image_id'] ?? 0);
    if ($img_id && $is_edit) {
        db_exec('DELETE FROM case_images WHERE id = ? AND case_id = ?', [$img_id, $id]);
    }
    header("Location: /admin/case_form.php?id=$id");
    exit;
}

$row = $is_edit ? db_one('SELECT * FROM case_studies WHERE id = ?', [$id])
                : ['title' => '', 'client_name' => '', 'scale' => '', 'delivered_at' => '',
                   'summary' => '', 'content' => '', 'published' => 1];
$images = $is_edit ? db_all('SELECT * FROM case_images WHERE case_id = ? ORDER BY sort_order, id', [$id]) : [];

if (!$row) { http_response_code(404); exit('사례를 찾을 수 없습니다.'); }

$admin_page = 'cases';
$admin_title = $is_edit ? "납품사례 수정 #$id" : '새 납품사례 등록';
require __DIR__ . '/_layout_top.php';
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>.ql-editor { min-height: 280px; font-size: 15px; line-height: 1.7; }</style>

<?php if (!empty($_SESSION['flash'])): ?>
<div class="mb-4 p-3 rounded-lg bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm">
  ✅ <?= h($_SESSION['flash']) ?>
</div><?php unset($_SESSION['flash']); endif; ?>

<?php if ($errors): ?>
<div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
  <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="space-y-5">
  <input type="hidden" name="id" value="<?= (int)$id ?>">
  <input type="hidden" name="content" id="content_input">

  <!-- 핵심 정보 -->
  <div class="bg-white rounded-xl border p-5 grid md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">제목 *</label>
      <input type="text" name="title" required value="<?= h($row['title']) ?>"
             placeholder="예) 인천 ㄱ산업단지 UPS 배터리 일괄 교체"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">고객사명 *</label>
      <input type="text" name="client_name" required value="<?= h($row['client_name']) ?>"
             placeholder="예) ㄱ산업단지 (인천)"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div>
      <label class="text-xs font-semibold text-gray-600">납품일자</label>
      <input type="date" name="delivered_at" value="<?= h($row['delivered_at']) ?>"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">납품 규모 (수량/금액)</label>
      <input type="text" name="scale" value="<?= h($row['scale']) ?>"
             placeholder="예) 12V 100Ah 240셀 / 약 3,200만원"
             class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-accent">
    </div>
    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">한줄 요약 (목록에 노출)</label>
      <textarea name="summary" rows="2"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-accent"><?= h($row['summary']) ?></textarea>
    </div>
  </div>

  <!-- 본문 (Quill) -->
  <div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b">
      <span class="font-bold text-primary text-sm">상세 설명</span>
    </div>
    <div id="editor"></div>
  </div>

  <!-- 사진 갤러리 -->
  <div class="bg-white rounded-xl border p-5">
    <h3 class="font-bold text-primary mb-1">납품사례 사진</h3>
    <p class="text-xs text-gray-500 mb-4">여러 장 업로드 가능. 등록 후 순서대로 갤러리에 노출됩니다.</p>

    <?php if (!empty($images)): ?>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-5">
      <?php foreach ($images as $img): ?>
      <div class="border rounded-lg overflow-hidden">
        <div class="aspect-square bg-gray-100">
          <img src="<?= h($img['image_url']) ?>" class="w-full h-full object-cover">
        </div>
        <div class="p-2 space-y-1">
          <input type="text" name="captions[<?= (int)$img['id'] ?>]" value="<?= h($img['caption']) ?>"
                 placeholder="캡션 (선택)"
                 class="w-full h-7 px-2 text-xs border border-gray-300 rounded">
          <button type="button" onclick="if(confirm('이 이미지를 삭제하시겠습니까?')){document.getElementById('del_<?= (int)$img['id'] ?>').submit();}"
                  class="w-full text-xs text-red-500 hover:text-red-700">✕ 삭제</button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- 삭제용 별도 폼 (포스트백 분리) -->
    <?php foreach ($images as $img): ?>
    <form method="post" id="del_<?= (int)$img['id'] ?>" class="hidden">
      <input type="hidden" name="id" value="<?= (int)$id ?>">
      <input type="hidden" name="action" value="delete_image">
      <input type="hidden" name="image_id" value="<?= (int)$img['id'] ?>">
    </form>
    <?php endforeach; ?>
    <?php endif; ?>

    <div class="border-t pt-4 space-y-3">
      <label class="text-xs font-semibold text-gray-600 block">파일 업로드 (여러 장 선택 가능)</label>
      <input type="file" name="images[]" accept="image/*" multiple
             class="block w-full text-sm">

      <label class="text-xs font-semibold text-gray-600 block mt-3">또는 URL 직접 추가</label>
      <input type="text" name="image_urls[]" placeholder="https://..."
             class="block w-full h-9 px-3 text-sm border border-gray-300 rounded-lg">
      <input type="text" name="image_urls[]" placeholder="https://..."
             class="block w-full h-9 px-3 text-sm border border-gray-300 rounded-lg">
      <input type="text" name="image_urls[]" placeholder="https://..."
             class="block w-full h-9 px-3 text-sm border border-gray-300 rounded-lg">
    </div>
  </div>

  <!-- 노출 + 액션 -->
  <div class="bg-white rounded-xl border p-5 flex items-center justify-between flex-wrap gap-3">
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="published" <?= $row['published'] ? 'checked' : '' ?> class="w-4 h-4">
      <span class="text-sm font-semibold">공개</span>
    </label>
    <div class="flex gap-2">
      <a href="/admin/cases.php" class="h-10 px-5 inline-flex items-center text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
        취소
      </a>
      <button type="submit" class="h-10 px-6 inline-flex items-center text-sm font-bold rounded-lg bg-primary text-white hover:bg-primary-light">
        <?= $is_edit ? '수정 저장' : '사례 등록' ?>
      </button>
    </div>
  </div>
</form>

<script src="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.js"></script>
<script>
const quill = new Quill('#editor', {
  theme: 'snow',
  modules: {
    toolbar: {
      container: [
        [{ header: [2, 3, false] }],
        ['bold', 'italic', 'underline'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'link'],
        ['clean'],
      ],
    },
  },
  placeholder: '프로젝트 개요, 적용 솔루션, 결과 등을 작성하세요.',
});

const initial = <?= json_encode($row['content'] ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
if (initial) quill.root.innerHTML = initial;

document.querySelectorAll('form').forEach(f => {
  if (f.id && f.id.startsWith('del_')) return;
  f.addEventListener('submit', () => {
    const ci = document.getElementById('content_input');
    if (ci) ci.value = quill.root.innerHTML;
  });
});
</script>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
