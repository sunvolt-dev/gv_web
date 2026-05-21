<?php
/**
 * 회원 인증 헬퍼 (사이트 일반 회원용)
 * — 어드민 인증과 별도 (admin/auth.php)
 */
require_once __DIR__ . '/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function user(): ?array
{
    if (empty($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache !== null && $cache['id'] == $_SESSION['user_id']) return $cache;
    $cache = db_one('SELECT id, email, name, phone, address, created_at FROM users WHERE id = ?',
                    [(int)$_SESSION['user_id']]);
    return $cache;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function require_login(string $redirect_to = null): void
{
    if (!is_logged_in()) {
        $r = $redirect_to ?? $_SERVER['REQUEST_URI'];
        header('Location: /auth/login.php?redirect=' . urlencode($r));
        exit;
    }
}

function user_register(string $email, string $password, string $name, string $phone = '', string $address = ''): array
{
    $email = strtolower(trim($email));
    $errors = [];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL))   $errors[] = '유효한 이메일을 입력해주세요.';
    if (strlen($password) < 6)                         $errors[] = '비밀번호는 6자 이상 입력해주세요.';
    if (trim($name) === '')                            $errors[] = '이름을 입력해주세요.';

    if (empty($errors) && db_one('SELECT id FROM users WHERE email = ?', [$email])) {
        $errors[] = '이미 가입된 이메일입니다.';
    }
    if ($errors) return ['ok' => false, 'errors' => $errors];

    db_exec(
        'INSERT INTO users (email, password_hash, name, phone, address) VALUES (?, ?, ?, ?, ?)',
        [$email, password_hash($password, PASSWORD_DEFAULT), trim($name), trim($phone), trim($address)]
    );
    $_SESSION['user_id'] = (int)db()->lastInsertId();
    return ['ok' => true];
}

function user_login(string $email, string $password): bool
{
    $email = strtolower(trim($email));
    $row = db_one('SELECT id, password_hash FROM users WHERE email = ?', [$email]);
    if (!$row || !password_verify($password, $row['password_hash'])) return false;
    $_SESSION['user_id'] = (int)$row['id'];
    return true;
}

function user_logout(): void
{
    unset($_SESSION['user_id']);
}
