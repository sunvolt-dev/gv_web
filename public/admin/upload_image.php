<?php
/**
 * Quill 에디터용 이미지 업로드 엔드포인트
 * POST: image (file)
 * 반환: JSON {url: "/assets/images/uploads/xxx.jpg"} 또는 {error: "..."}
 */
require_once __DIR__ . '/auth.php';
header('Content-Type: application/json; charset=utf-8');

if (!admin_user()) {
    http_response_code(403);
    echo json_encode(['error' => '인증되지 않은 사용자']);
    exit;
}

if (empty($_FILES['image']['name'])) {
    http_response_code(400);
    echo json_encode(['error' => '파일이 없습니다']);
    exit;
}

$f = $_FILES['image'];
if ($f['error'] !== UPLOAD_ERR_OK) {
    http_response_code(400);
    echo json_encode(['error' => '업로드 오류: ' . $f['error']]);
    exit;
}

$allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
$finfo   = new finfo(FILEINFO_MIME_TYPE);
$mime    = $finfo->file($f['tmp_name']);

if (!isset($allowed[$mime])) {
    http_response_code(400);
    echo json_encode(['error' => 'jpg/png/webp/gif만 업로드 가능 (감지: ' . $mime . ')']);
    exit;
}

if ($f['size'] > 10 * 1024 * 1024) {
    http_response_code(400);
    echo json_encode(['error' => '10MB 이하만 업로드 가능']);
    exit;
}

// 저장 경로 결정 (uploads = blog 본문 / cases = 납품사례 갤러리)
$dest = $_GET['dest'] ?? 'uploads';
$dest = $dest === 'cases' ? 'cases' : 'uploads';

$ext     = $allowed[$mime];
$name    = date('Ymd') . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
$dir     = __DIR__ . '/../assets/images/' . $dest . '/';
if (!is_dir($dir)) @mkdir($dir, 0755, true);

if (!move_uploaded_file($f['tmp_name'], $dir . $name)) {
    http_response_code(500);
    echo json_encode(['error' => '저장 실패']);
    exit;
}

echo json_encode([
    'url'  => '/assets/images/' . $dest . '/' . $name,
    'name' => $name,
    'size' => $f['size'],
]);
