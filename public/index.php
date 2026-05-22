<?php
require_once __DIR__ . '/../includes/functions.php';

$page_title = '메인';
$page_desc  = '자동차·산업용·전동모빌리티 배터리 전문 - 정품 보장, 빠른 배송';

$best_products  = get_best_products(8);
$new_products   = get_new_products(4);
$top_categories = category_tree()[0] ?? [];
$recent_posts   = db_all('SELECT id, title, summary, thumbnail, created_at FROM posts WHERE published = 1 ORDER BY created_at DESC LIMIT 2');
$banners = [];
try {
    $banners = db_all('SELECT * FROM banners WHERE published = 1 ORDER BY sort_order, id');
} catch (Throwable $e) { /* banners 테이블 없으면 폴백 */ }

require __DIR__ . '/../includes/header.php';

/* 활성 테마의 홈페이지 레이아웃 — index.php 스코프에서 include (위 변수들 사용) */
include theme_file('home.php');

require __DIR__ . '/../includes/footer.php';
