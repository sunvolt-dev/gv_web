<?php
require_once __DIR__ . '/auth.php';
require_admin();

$id = (int)($_GET['id'] ?? $_POST['id'] ?? 0);
$is_edit = $id > 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title        = trim((string)($_POST['title'] ?? ''));
    $subtitle     = trim((string)($_POST['subtitle'] ?? ''));
    $accent_label = trim((string)($_POST['accent_label'] ?? ''));
    $cta_text     = trim((string)($_POST['cta_text'] ?? ''));
    $cta_url      = trim((string)($_POST['cta_url'] ?? ''));
    $text_align   = in_array($_POST['text_align'] ?? '', ['left','center','right']) ? $_POST['text_align'] : 'left';
    $sort_order   = (int)($_POST['sort_order'] ?? 0);
    $published    = isset($_POST['published']) ? 1 : 0;

    if ($title === '') $errors[] = '제목을 입력해주세요.';

    // 이미지 URL 또는 업로드
    $image_url = trim((string)($_POST['image_url'] ?? ''));

    if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);
        if (!isset($allowed[$mime])) {
            $errors[] = '이미지는 jpg/png/webp만 가능합니다.';
        } elseif ($_FILES['image']['size'] > 10 * 1024 * 1024) {
            $errors[] = '이미지는 10MB 이하만 업로드 가능합니다.';
        } else {
            $name = 'banner_' . date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $allowed[$mime];
            $dir = __DIR__ . '/../assets/images/banners/';
            if (!is_dir($dir)) @mkdir($dir, 0755, true);
            if (move_uploaded_file($_FILES['image']['tmp_name'], $dir . $name)) {
                $image_url = '/assets/images/banners/' . $name;
            }
        }
    } elseif ($image_url === '' && $is_edit) {
        $cur = db_one('SELECT image_url FROM banners WHERE id = ?', [$id]);
        $image_url = $cur['image_url'] ?? '';
    }

    if ($image_url === '') $errors[] = '이미지를 업로드하거나 URL을 입력해주세요.';

    if (empty($errors)) {
        if ($is_edit) {
            db_exec(
                'UPDATE banners
                    SET title=?, subtitle=?, image_url=?, cta_text=?, cta_url=?,
                        accent_label=?, text_align=?, sort_order=?, published=?
                  WHERE id=?',
                [$title, $subtitle, $image_url, $cta_text, $cta_url,
                 $accent_label, $text_align, $sort_order, $published, $id]
            );
            $_SESSION['flash'] = "배너 #$id 수정 완료";
        } else {
            db_exec(
                'INSERT INTO banners
                  (title, subtitle, image_url, cta_text, cta_url, accent_label, text_align, sort_order, published)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)',
                [$title, $subtitle, $image_url, $cta_text, $cta_url,
                 $accent_label, $text_align, $sort_order, $published]
            );
            $_SESSION['flash'] = "배너 등록 완료";
        }
        header('Location: /admin/banners.php');
        exit;
    }
}

$row = $is_edit ? db_one('SELECT * FROM banners WHERE id = ?', [$id])
                : ['title' => '', 'subtitle' => '', 'image_url' => '',
                   'cta_text' => '', 'cta_url' => '', 'accent_label' => '',
                   'text_align' => 'left', 'sort_order' => 99, 'published' => 1];

if (!$row) { http_response_code(404); exit('배너를 찾을 수 없습니다.'); }

$admin_page = 'banners';
$admin_title = $is_edit ? "배너 수정 #$id" : '새 배너 추가';
require __DIR__ . '/_layout_top.php';
?>

<?php if ($errors): ?>
<div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
  <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="grid lg:grid-cols-[1fr_360px] gap-5 max-w-6xl">
  <input type="hidden" name="id" value="<?= (int)$id ?>">

  <!-- 좌측: 입력 -->
  <div class="space-y-5">
    <div class="bg-white rounded-xl border p-5 space-y-4">
      <h3 class="font-bold text-primary">배너 정보</h3>

      <div>
        <label class="text-xs font-semibold text-gray-600">상단 작은 라벨 (선택)</label>
        <input type="text" name="accent_label" value="<?= h($row['accent_label']) ?>"
               placeholder="예) NEW · 신제품 / B2B 전용 / EVENT"
               class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-accent">
      </div>

      <div>
        <label class="text-xs font-semibold text-gray-600">제목 *</label>
        <input type="text" name="title" required value="<?= h($row['title']) ?>"
               placeholder="모든 차량과 장비의 파워를 책임집니다"
               class="mt-1 w-full h-11 px-3 border border-gray-300 rounded-lg
                      focus:outline-none focus:ring-2 focus:ring-accent">
      </div>

      <div>
        <label class="text-xs font-semibold text-gray-600">부제 (선택) — Enter 키로 줄바꿈 가능</label>
        <textarea name="subtitle" rows="4"
                  placeholder="간단한 부연설명&#10;Enter 키 눌러 줄을 바꿀 수 있어요"
                  class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg text-sm leading-relaxed
                         focus:outline-none focus:ring-2 focus:ring-accent"><?= h($row['subtitle']) ?></textarea>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-semibold text-gray-600">CTA 버튼 텍스트</label>
          <input type="text" name="cta_text" value="<?= h($row['cta_text']) ?>"
                 placeholder="자동차 배터리 보기"
                 class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm
                        focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">CTA 링크 URL</label>
          <input type="text" name="cta_url" value="<?= h($row['cta_url']) ?>"
                 placeholder="/shop/list.php?ca_id=10"
                 class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm
                        focus:outline-none focus:ring-2 focus:ring-accent">
        </div>
      </div>

      <div class="grid grid-cols-2 gap-3">
        <div>
          <label class="text-xs font-semibold text-gray-600">텍스트 정렬</label>
          <select name="text_align"
                  class="mt-1 w-full h-10 px-2 border border-gray-300 rounded-lg text-sm">
            <option value="left"   <?= $row['text_align']==='left'   ? 'selected':'' ?>>왼쪽</option>
            <option value="center" <?= $row['text_align']==='center' ? 'selected':'' ?>>가운데</option>
            <option value="right"  <?= $row['text_align']==='right'  ? 'selected':'' ?>>오른쪽</option>
          </select>
        </div>
        <div>
          <label class="text-xs font-semibold text-gray-600">정렬 순서 (작을수록 먼저)</label>
          <input type="number" name="sort_order" value="<?= (int)$row['sort_order'] ?>"
                 class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm">
        </div>
      </div>
    </div>

    <div class="bg-white rounded-xl border p-5 space-y-3">
      <h3 class="font-bold text-primary">배경 이미지</h3>
      <p class="text-xs text-gray-500">권장 사이즈: 1920×720 (16:6 비율) · jpg/png/webp · 최대 10MB</p>

      <div>
        <label class="text-xs font-semibold text-gray-600">파일 업로드</label>
        <input type="file" name="image" accept="image/*" class="mt-1 block w-full text-sm">
      </div>

      <div>
        <label class="text-xs font-semibold text-gray-600">또는 이미지 URL 직접 입력</label>
        <input type="text" name="image_url" value="<?= h($row['image_url']) ?>"
               placeholder="https://placehold.co/1920x720/0A2540/FFC107?text=Banner"
               class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg text-sm
                      focus:outline-none focus:ring-2 focus:ring-accent">
      </div>
    </div>

    <div class="bg-white rounded-xl border p-5 flex items-center justify-between flex-wrap gap-3">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="published" <?= $row['published'] ? 'checked' : '' ?> class="w-4 h-4">
        <span class="text-sm font-semibold">메인에 노출</span>
      </label>
      <div class="flex gap-2">
        <a href="/admin/banners.php" class="h-10 px-5 inline-flex items-center text-sm border border-gray-300 rounded-lg hover:bg-gray-50">
          취소
        </a>
        <button type="submit" class="h-10 px-6 inline-flex items-center text-sm font-bold rounded-lg bg-primary text-white hover:bg-primary-light">
          <?= $is_edit ? '수정 저장' : '배너 추가' ?>
        </button>
      </div>
    </div>
  </div>

  <!-- 우측: 미리보기 -->
  <div class="lg:sticky lg:top-24 self-start">
    <div class="bg-white rounded-xl border p-4">
      <h3 class="font-bold text-primary text-sm mb-3">미리보기</h3>
      <div class="relative aspect-[16/9] rounded-lg overflow-hidden bg-gray-200">
        <?php if ($row['image_url']): ?>
        <img src="<?= h($row['image_url']) ?>" class="absolute inset-0 w-full h-full object-cover">
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/30 to-transparent"></div>
        <div class="absolute inset-0 flex flex-col justify-center px-6 text-white">
          <?php if ($row['accent_label']): ?>
          <div class="text-[10px] font-bold text-accent mb-1"><?= h($row['accent_label']) ?></div>
          <?php endif; ?>
          <h4 class="font-extrabold text-base leading-tight mb-1 truncate"><?= h($row['title'] ?: '제목 미리보기') ?></h4>
          <?php if ($row['subtitle']): ?>
          <p class="text-[11px] text-white/80 mb-2 whitespace-pre-line line-clamp-3"><?= h($row['subtitle']) ?></p>
          <?php endif; ?>
          <?php if ($row['cta_text']): ?>
          <span class="inline-block px-3 py-1.5 text-[11px] bg-accent text-primary font-bold rounded w-fit"><?= h($row['cta_text']) ?></span>
          <?php endif; ?>
        </div>
      </div>
      <p class="text-[11px] text-gray-400 mt-2 text-center">실제 메인에서는 와이드 16:6 비율로 표시됩니다</p>
    </div>
  </div>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
