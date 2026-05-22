<?php
/**
 * robots.txt 동적 생성 (.htaccess가 /robots.txt → 여기로 매핑)
 * 도메인이 바뀌어도 sitemap 주소 자동 반영.
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: text/plain; charset=utf-8');

$origin = site_origin();
?>
User-agent: *
Allow: /
Disallow: /admin/
Disallow: /auth/
Disallow: /mypage/
Disallow: /cart.php
Disallow: /cart_action.php
Disallow: /checkout.php
Disallow: /checkout_complete.php

Sitemap: <?= $origin ?>/sitemap.xml
