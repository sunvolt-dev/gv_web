<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$is_edit = $id > 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title     = trim((string)($_POST['title'] ?? ''));
    $summary   = trim((string)($_POST['summary'] ?? ''));
    $content   = (string)($_POST['content'] ?? '');
    $published = isset($_POST['published']) ? 1 : 0;
    $thumbnail = trim((string)($_POST['thumbnail_url'] ?? ''));

    if ($title === '') $errors[] = '제목을 입력해주세요.';

    // 썸네일 파일 업로드 처리
    if (!empty($_FILES['thumbnail']['name']) && $_FILES['thumbnail']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['thumbnail']['tmp_name']);
        if (isset($allowed[$mime])) {
            $name = 'thumb_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
            $dir = __DIR__ . '/../assets/images/uploads/';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            if (move_uploaded_file($_FILES['thumbnail']['tmp_name'], $dir . $name)) {
                $thumbnail = '/assets/images/uploads/' . $name;
            }
        } else {
            $errors[] = '썸네일은 jpg/png/webp 형식만 가능합니다.';
        }
    } elseif ($thumbnail === '' && $is_edit) {
        // 기존 썸네일 유지
        $cur = db_one('SELECT thumbnail FROM posts WHERE id = ?', [$id]);
        $thumbnail = $cur['thumbnail'] ?? null;
    }

    if (empty($errors)) {
        if ($is_edit) {
            db_exec(
                'UPDATE posts SET title=?, summary=?, content=?, thumbnail=?, published=? WHERE id=?',
                [$title, $summary, $content, $thumbnail ?: null, $published, $id]
            );
            $_SESSION['flash'] = "블로그 글 #$id 수정 완료";
        } else {
            db_exec(
                'INSERT INTO posts (title, summary, content, thumbnail, published) VALUES (?, ?, ?, ?, ?)',
                [$title, $summary, $content, $thumbnail ?: null, $published]
            );
            $new_id = (int)db()->lastInsertId();
            $_SESSION['flash'] = "블로그 글 #$new_id 등록 완료";
        }
        header('Location: /admin/posts.php');
        exit;
    }
}

$row = $is_edit ? db_one('SELECT * FROM posts WHERE id = ?', [$id])
                : ['title' => '', 'summary' => '', 'content' => '', 'thumbnail' => null, 'published' => 1];

if (!$row) { http_response_code(404); exit('글을 찾을 수 없습니다.'); }

$admin_page = 'posts';
$admin_title = $is_edit ? "블로그 글 수정 #$id" : '새 글 작성';
require __DIR__ . '/_layout_top.php';
?>

<link href="https://cdn.jsdelivr.net/npm/quill@2/dist/quill.snow.css" rel="stylesheet">
<style>
  .ql-editor { min-height: 400px; font-size: 15px; line-height: 1.7; }
  .ql-editor h2 { font-size: 1.5rem; font-weight: 700; color: #0A2540; margin-top: 1.5rem; }
  .ql-editor h3 { font-size: 1.2rem; font-weight: 700; color: #0A2540; margin-top: 1rem; }
</style>

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

  <!-- 제목 -->
  <div class="bg-white rounded-xl border p-5">
    <input type="text" name="title" required value="<?= h($row['title']) ?>"
           placeholder="제목을 입력하세요"
           class="w-full text-2xl font-extrabold text-primary border-0 focus:ring-0 px-0
                  placeholder:text-gray-300">
  </div>

  <!-- 요약 + 썸네일 -->
  <div class="bg-white rounded-xl border p-5 grid md:grid-cols-[1fr_280px] gap-5">
    <div>
      <label class="text-xs font-semibold text-gray-600">요약 (목록 페이지에 노출)</label>
      <textarea name="summary" rows="3"
                placeholder="블로그 목록에 보여질 한두 문장 요약"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm
                       focus:outline-none focus:ring-2 focus:ring-accent"><?= h($row['summary']) ?></textarea>

      <label class="text-xs font-semibold text-gray-600 mt-3 block">썸네일 URL (또는 아래 파일 업로드)</label>
      <input type="text" name="thumbnail_url" value="<?= h($row['thumbnail']) ?>" placeholder="https://..."
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm
                    focus:outline-none focus:ring-2 focus:ring-accent">

      <input type="file" name="thumbnail" accept="image/*" class="mt-2 text-xs">
    </div>
    <div>
      <div class="text-xs font-semibold text-gray-600 mb-1">미리보기</div>
      <div class="aspect-[16/10] rounded-lg bg-gray-100 overflow-hidden border-2 border-dashed border-gray-300">
        <?php if ($row['thumbnail']): ?>
          <img src="<?= h($row['thumbnail']) ?>" class="w-full h-full object-cover">
        <?php else: ?>
          <div class="w-full h-full flex items-center justify-center text-gray-300 text-3xl">📷</div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Quill 에디터 -->
  <div class="bg-white rounded-xl border overflow-hidden">
    <div class="px-5 py-3 border-b">
      <span class="font-bold text-primary text-sm">본문 작성</span>
      <span class="text-[11px] text-gray-500 ml-2">툴바의 🖼️ 버튼으로 이미지 삽입 (서버 업로드)</span>
    </div>
    <div id="editor"></div>
  </div>

  <!-- 노출 설정 -->
  <div class="bg-white rounded-xl border p-5 flex items-center justify-between flex-wrap gap-3">
    <label class="inline-flex items-center gap-2">
      <input type="checkbox" name="published" <?= $row['published'] ? 'checked' : '' ?> class="w-4 h-4">
      <span class="text-sm font-semibold">공개</span>
      <span class="text-xs text-gray-500">(체크 해제 시 비공개 임시저장)</span>
    </label>

    <div class="flex gap-2">
      <a href="/admin/posts.php" class="h-10 px-5 inline-flex items-center text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
        취소
      </a>
      <button type="submit"
              class="h-10 px-6 inline-flex items-center text-sm font-bold rounded-lg bg-primary text-white hover:bg-primary-light">
        <?= $is_edit ? '수정 저장' : '글 등록' ?>
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
        ['bold', 'italic', 'underline', 'strike'],
        [{ list: 'ordered' }, { list: 'bullet' }],
        ['blockquote', 'link', 'image'],
        [{ align: [] }],
        ['clean'],
      ],
      handlers: {
        image: imageHandler,
      },
    },
  },
  placeholder: '여기에 본문을 작성하세요. 이미지·링크·목록 등 자유롭게 사용 가능합니다.',
});

// 기존 콘텐츠 로드
const initial = <?= json_encode($row['content'] ?? '', JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
if (initial) quill.root.innerHTML = initial;

// 폼 제출 시 HTML을 hidden input에 주입
document.querySelector('form').addEventListener('submit', () => {
  document.getElementById('content_input').value = quill.root.innerHTML;
});

// 이미지 업로드 핸들러
function imageHandler() {
  const input = document.createElement('input');
  input.type = 'file';
  input.accept = 'image/*';
  input.click();
  input.onchange = async () => {
    const file = input.files[0];
    if (!file) return;
    const fd = new FormData();
    fd.append('image', file);
    try {
      const res = await fetch('/admin/upload_image.php', { method: 'POST', body: fd });
      const data = await res.json();
      if (data.url) {
        const range = quill.getSelection(true);
        quill.insertEmbed(range.index, 'image', data.url);
        quill.setSelection(range.index + 1);
      } else {
        alert('업로드 실패: ' + (data.error || '알 수 없는 오류'));
      }
    } catch (e) {
      alert('업로드 중 오류: ' + e.message);
    }
  };
}
</script>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
