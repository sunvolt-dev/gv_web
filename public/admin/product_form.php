<?php
require_once __DIR__ . '/auth.php';
require_admin();

$it_id = (int)($_GET['it_id'] ?? $_POST['it_id'] ?? 0);
$is_edit = $it_id > 0;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 입력 수집
    $data = [
        'it_name'       => trim((string)($_POST['it_name'] ?? '')),
        'ca_id'         => (int)($_POST['ca_id'] ?? 0),
        'it_price'      => max(0, (int)($_POST['it_price'] ?? 0)),
        'it_sell_price' => max(0, (int)($_POST['it_sell_price'] ?? 0)),
        'it_stock'      => max(0, (int)($_POST['it_stock'] ?? 0)),
        'it_summary'    => trim((string)($_POST['it_summary'] ?? '')),
        'it_desc'       => (string)($_POST['it_desc'] ?? ''),
        'it_use'        => isset($_POST['it_use']) ? 1 : 0,
        'it_new'        => isset($_POST['it_new']) ? 1 : 0,
        'it_best'       => isset($_POST['it_best']) ? 1 : 0,
    ];

    // 검증
    if ($data['it_name'] === '') $errors[] = '상품명을 입력해주세요.';
    if ($data['ca_id'] === 0)    $errors[] = '카테고리를 선택해주세요.';
    if ($data['it_price'] === 0) $errors[] = '가격을 입력해주세요.';

    // 이미지 업로드 (it_img1~5)
    $img_paths = [];
    $cur = $is_edit ? db_one('SELECT it_img1, it_img2, it_img3, it_img4, it_img5 FROM products WHERE it_id = ?', [$it_id]) : null;

    for ($n = 1; $n <= 5; $n++) {
        $key = "img{$n}";
        $existing = $cur["it_img{$n}"] ?? null;
        $url_input = trim((string)($_POST["it_img{$n}_url"] ?? ''));

        // 1) 파일 업로드 처리
        if (!empty($_FILES[$key]['name'])) {
            $f = $_FILES[$key];
            if ($f['error'] === UPLOAD_ERR_OK) {
                $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime = $finfo->file($f['tmp_name']);
                if (!isset($allowed[$mime])) {
                    $errors[] = "이미지{$n}: jpg/png/webp/gif만 업로드 가능합니다. (감지: $mime)";
                } elseif ($f['size'] > 5 * 1024 * 1024) {
                    $errors[] = "이미지{$n}: 5MB 이하만 업로드 가능합니다.";
                } else {
                    $ext = $allowed[$mime];
                    $name = bin2hex(random_bytes(8)) . '.' . $ext;
                    $dest_dir = __DIR__ . '/../assets/images/products/';
                    if (!is_dir($dest_dir)) @mkdir($dest_dir, 0755, true);
                    if (move_uploaded_file($f['tmp_name'], $dest_dir . $name)) {
                        $img_paths[$n] = $name;
                    } else {
                        $errors[] = "이미지{$n}: 저장 실패";
                    }
                }
            }
        }
        // 2) URL 직접 입력
        elseif ($url_input !== '') {
            $img_paths[$n] = $url_input;
        }
        // 3) 기존 값 유지
        elseif ($is_edit && $existing) {
            $img_paths[$n] = $existing;
        }
    }

    if (empty($errors)) {
        if ($is_edit) {
            db_exec(
                "UPDATE products SET
                    it_name=?, ca_id=?, it_price=?, it_sell_price=?, it_stock=?,
                    it_summary=?, it_desc=?, it_use=?, it_new=?, it_best=?,
                    it_img1=?, it_img2=?, it_img3=?, it_img4=?, it_img5=?
                 WHERE it_id=?",
                [
                    $data['it_name'], $data['ca_id'], $data['it_price'], $data['it_sell_price'], $data['it_stock'],
                    $data['it_summary'], $data['it_desc'], $data['it_use'], $data['it_new'], $data['it_best'],
                    $img_paths[1] ?? null, $img_paths[2] ?? null, $img_paths[3] ?? null,
                    $img_paths[4] ?? null, $img_paths[5] ?? null,
                    $it_id,
                ]
            );
            $_SESSION['flash'] = "상품 #$it_id 수정 완료";
        } else {
            db_exec(
                "INSERT INTO products
                    (it_name, ca_id, it_price, it_sell_price, it_stock,
                     it_summary, it_desc, it_use, it_new, it_best,
                     it_img1, it_img2, it_img3, it_img4, it_img5)
                 VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?)",
                [
                    $data['it_name'], $data['ca_id'], $data['it_price'], $data['it_sell_price'], $data['it_stock'],
                    $data['it_summary'], $data['it_desc'], $data['it_use'], $data['it_new'], $data['it_best'],
                    $img_paths[1] ?? null, $img_paths[2] ?? null, $img_paths[3] ?? null,
                    $img_paths[4] ?? null, $img_paths[5] ?? null,
                ]
            );
            $new_id = (int)db()->lastInsertId();
            $_SESSION['flash'] = "상품 #$new_id 등록 완료";
        }
        header('Location: /admin/products.php');
        exit;
    }
}

$row = $is_edit ? db_one('SELECT * FROM products WHERE it_id = ?', [$it_id]) : [
    'it_name' => '', 'ca_id' => 0, 'it_price' => 0, 'it_sell_price' => 0, 'it_stock' => 0,
    'it_summary' => '', 'it_desc' => '', 'it_use' => 1, 'it_new' => 0, 'it_best' => 0,
    'it_img1' => null, 'it_img2' => null, 'it_img3' => null, 'it_img4' => null, 'it_img5' => null,
];

if (!$row) { http_response_code(404); exit('상품을 찾을 수 없습니다.'); }

$admin_page = 'products';
$admin_title = $is_edit ? "상품 수정 #$it_id" : '새 상품 등록';
require __DIR__ . '/_layout_top.php';
?>

<?php if ($errors): ?>
<div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
  <ul class="list-disc pl-5 text-sm text-red-700 space-y-1">
    <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
  </ul>
</div>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" class="bg-white rounded-xl border p-5 md:p-7 max-w-4xl space-y-5">
  <input type="hidden" name="it_id" value="<?= (int)$it_id ?>">

  <!-- 기본 정보 -->
  <div class="grid md:grid-cols-2 gap-4">
    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">상품명 *</label>
      <input type="text" name="it_name" required value="<?= h($row['it_name']) ?>"
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg
                    focus:outline-none focus:ring-2 focus:ring-accent">
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">카테고리 *</label>
      <select name="ca_id" required
              class="mt-1 w-full h-10 px-2 border border-gray-300 rounded-lg
                     focus:outline-none focus:ring-2 focus:ring-accent">
        <option value="">선택</option>
        <?php foreach (get_categories() as $c): ?>
        <option value="<?= (int)$c['ca_id'] ?>" <?= $row['ca_id'] == $c['ca_id'] ? 'selected' : '' ?>>
          <?= $c['ca_parent'] ? '— ' : '' ?><?= h($c['ca_name']) ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">재고</label>
      <input type="number" name="it_stock" min="0" value="<?= (int)$row['it_stock'] ?>"
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg">
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">정가 (원) *</label>
      <input type="number" name="it_price" required min="0" value="<?= (int)$row['it_price'] ?>"
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg">
    </div>

    <div>
      <label class="text-xs font-semibold text-gray-600">할인가 (원, 0=할인 없음)</label>
      <input type="number" name="it_sell_price" min="0" value="<?= (int)$row['it_sell_price'] ?>"
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg">
    </div>

    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">한줄 요약</label>
      <input type="text" name="it_summary" value="<?= h($row['it_summary']) ?>"
             class="mt-1 w-full h-10 px-3 border border-gray-300 rounded-lg">
    </div>

    <div class="md:col-span-2">
      <label class="text-xs font-semibold text-gray-600">상세 설명 (HTML 허용)</label>
      <textarea name="it_desc" rows="8"
                class="mt-1 w-full px-3 py-2 border border-gray-300 rounded-lg
                       font-mono text-sm focus:outline-none focus:ring-2 focus:ring-accent"><?= h($row['it_desc']) ?></textarea>
      <p class="text-[11px] text-gray-500 mt-1">예) &lt;h3&gt;제목&lt;/h3&gt; &lt;ul&gt;&lt;li&gt;항목&lt;/li&gt;&lt;/ul&gt;</p>
    </div>
  </div>

  <!-- 이미지 -->
  <div class="border-t pt-5">
    <h3 class="font-bold text-primary mb-3">상품 이미지 (최대 5장)</h3>
    <p class="text-xs text-gray-500 mb-4">파일 업로드 또는 URL 직접 입력 (둘 다 입력하면 업로드 우선)</p>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-3">
      <?php for ($n = 1; $n <= 5; $n++):
        $cur = $row["it_img{$n}"] ?? null;
      ?>
      <div class="space-y-2">
        <div class="aspect-square rounded-lg border-2 border-dashed border-gray-300 overflow-hidden bg-gray-50 flex items-center justify-center">
          <?php if ($cur): ?>
          <img src="<?= h(image_url($cur)) ?>" class="w-full h-full object-cover">
          <?php else: ?>
          <span class="text-gray-300 text-3xl">+</span>
          <?php endif; ?>
        </div>
        <input type="file" name="img<?= $n ?>" accept="image/*" class="block w-full text-xs">
        <input type="text" name="it_img<?= $n ?>_url" placeholder="URL (선택)"
               value="<?= h($cur && preg_match('#^https?://#', $cur) ? $cur : '') ?>"
               class="block w-full h-8 px-2 text-xs border border-gray-300 rounded">
      </div>
      <?php endfor; ?>
    </div>
  </div>

  <!-- 노출 설정 -->
  <div class="border-t pt-5">
    <h3 class="font-bold text-primary mb-3">노출 설정</h3>
    <div class="flex flex-wrap gap-4">
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="it_use" <?= $row['it_use'] ? 'checked' : '' ?> class="w-4 h-4">
        <span class="text-sm">판매중</span>
      </label>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="it_best" <?= $row['it_best'] ? 'checked' : '' ?> class="w-4 h-4">
        <span class="text-sm">BEST 표시</span>
      </label>
      <label class="inline-flex items-center gap-2">
        <input type="checkbox" name="it_new" <?= $row['it_new'] ? 'checked' : '' ?> class="w-4 h-4">
        <span class="text-sm">NEW 표시</span>
      </label>
    </div>
  </div>

  <!-- 액션 -->
  <div class="border-t pt-5 flex justify-between items-center">
    <a href="/admin/products.php" class="text-sm text-gray-500 hover:text-primary">← 목록으로</a>
    <button type="submit"
            class="px-6 py-3 rounded-lg bg-primary text-white font-bold hover:bg-primary-light">
      <?= $is_edit ? '수정 저장' : '상품 등록' ?>
    </button>
  </div>
</form>

<?php require __DIR__ . '/_layout_bottom.php'; ?>
