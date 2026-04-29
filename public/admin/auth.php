<?php
/**
 * 어드민 인증 헬퍼
 */
require_once __DIR__ . '/../../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) session_start();

function admin_user(): ?array
{
    return $_SESSION['admin'] ?? null;
}

function require_admin(): void
{
    if (!admin_user()) {
        header('Location: /admin/login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

function admin_login(string $username, string $password): bool
{
    $row = db_one('SELECT * FROM admin_users WHERE username = ?', [$username]);
    if (!$row) return false;
    if (!password_verify($password, $row['password_hash'])) return false;

    $_SESSION['admin'] = [
        'id'       => (int)$row['id'],
        'username' => $row['username'],
    ];
    return true;
}

function admin_logout(): void
{
    unset($_SESSION['admin']);
}
