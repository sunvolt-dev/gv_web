<?php
/**
 * 디바이스 감지 — PC / 모바일 뷰 분기
 *
 * 우선순위:
 *   1) ?view=mobile / ?view=pc  → 강제 전환 + 쿠키 30일 저장
 *   2) 저장된 쿠키 view_mode
 *   3) User-Agent 자동 감지
 */

function is_mobile(): bool
{
    static $result = null;
    if ($result !== null) return $result;

    // 1. 쿼리스트링 강제 전환
    if (isset($_GET['view'])) {
        if ($_GET['view'] === 'mobile') {
            if (!headers_sent()) setcookie('view_mode', 'mobile', time() + 2592000, '/');
            return $result = true;
        }
        if ($_GET['view'] === 'pc') {
            if (!headers_sent()) setcookie('view_mode', 'pc', time() + 2592000, '/');
            return $result = false;
        }
    }
    // 2. 저장된 쿠키
    if (isset($_COOKIE['view_mode'])) {
        return $result = ($_COOKIE['view_mode'] === 'mobile');
    }
    // 3. User-Agent 자동 감지
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? '';
    return $result = (bool)preg_match(
        '/(android.+mobile|iphone|ipod|blackberry|iemobile|opera mini|windows phone|webos)/i',
        $ua
    );
}

/** 현재 페이지를 반대 뷰(PC↔모바일)로 여는 URL */
function toggle_view_url(): string
{
    $uri = $_SERVER['REQUEST_URI'] ?? '/';
    $uri = preg_replace('/([?&])view=(mobile|pc)(&|$)/', '$1', $uri);
    $uri = rtrim($uri, '?&');
    $sep = strpos($uri, '?') !== false ? '&' : '?';
    return $uri . $sep . 'view=' . (is_mobile() ? 'pc' : 'mobile');
}
