<?php
/**
 * 이메일 중복 확인 AJAX 엔드포인트
 * GET/POST: email
 * 반환: JSON {available: bool, message: string}
 */
require_once __DIR__ . '/../../includes/user_auth.php';

header('Content-Type: application/json; charset=utf-8');

$email = strtolower(trim((string)($_REQUEST['email'] ?? '')));

if ($email === '') {
    echo json_encode(['available' => false, 'message' => '이메일을 입력해주세요.', 'level' => 'warn']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['available' => false, 'message' => '유효한 이메일 형식이 아닙니다.', 'level' => 'error']);
    exit;
}

$exists = db_one('SELECT id FROM users WHERE email = ?', [$email]);
if ($exists) {
    echo json_encode(['available' => false, 'message' => '이미 가입된 이메일입니다.', 'level' => 'error']);
} else {
    echo json_encode(['available' => true, 'message' => '사용 가능한 이메일입니다.', 'level' => 'ok']);
}
