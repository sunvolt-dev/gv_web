<?php
/**
 * 사이트 전역 헬퍼 함수
 */

require_once __DIR__ . '/db.php';

/* ────────────────────────────────────────────────────────
   출력 안전 / 포맷
─────────────────────────────────────────────────────────*/

function h(?string $s): string
{
    return htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function price(int $n): string
{
    return number_format($n) . '원';
}

function discount_rate(int $price, int $sell): int
{
    if ($price <= 0 || $sell <= 0 || $sell >= $price) return 0;
    return (int)round((1 - $sell / $price) * 100);
}

/* ────────────────────────────────────────────────────────
   이미지 URL
─────────────────────────────────────────────────────────*/

function image_url(?string $path): string
{
    static $cfg = null;
    if ($cfg === null) $cfg = require __DIR__ . '/config.php';

    if (!$path) return $cfg['paths']['placeholder'];
    if (preg_match('#^https?://#i', $path)) return $path;
    return $cfg['paths']['image_base'] . ltrim($path, '/');
}

function product_images(array $product): array
{
    $imgs = [];
    for ($i = 1; $i <= 5; $i++) {
        $key = "it_img{$i}";
        if (!empty($product[$key])) $imgs[] = image_url($product[$key]);
    }
    if (empty($imgs)) $imgs[] = image_url(null);
    return $imgs;
}

/* ────────────────────────────────────────────────────────
   카테고리
─────────────────────────────────────────────────────────*/

function get_categories(): array
{
    static $cache = null;
    if ($cache !== null) return $cache;
    return $cache = db_all('SELECT * FROM categories ORDER BY ca_order, ca_id');
}

function category_tree(): array
{
    $rows = get_categories();
    $by_parent = [];
    foreach ($rows as $r) {
        $key = $r['ca_parent'] === null ? 0 : (int)$r['ca_parent'];
        $by_parent[$key][] = $r;
    }
    return $by_parent;
}

function get_category(int $ca_id): ?array
{
    foreach (get_categories() as $c) {
        if ((int)$c['ca_id'] === $ca_id) return $c;
    }
    return null;
}

function category_breadcrumb(int $ca_id): array
{
    $trail = [];
    $cur = get_category($ca_id);
    while ($cur) {
        array_unshift($trail, $cur);
        $cur = $cur['ca_parent'] ? get_category((int)$cur['ca_parent']) : null;
    }
    return $trail;
}

/** 자식 카테고리 ID(자기 포함) 모음 — 대분류 클릭 시 하위까지 전부 */
function descendant_category_ids(int $ca_id): array
{
    $ids = [$ca_id];
    $tree = category_tree();
    $stack = [$ca_id];
    while ($stack) {
        $cur = array_pop($stack);
        foreach ($tree[$cur] ?? [] as $child) {
            $ids[] = (int)$child['ca_id'];
            $stack[] = (int)$child['ca_id'];
        }
    }
    return $ids;
}

/* ────────────────────────────────────────────────────────
   상품
─────────────────────────────────────────────────────────*/

function get_product(int $it_id): ?array
{
    return db_one('SELECT * FROM products WHERE it_id = ? AND it_use = 1', [$it_id]);
}

function get_product_options(int $it_id): array
{
    return db_all('SELECT * FROM product_options WHERE it_id = ? ORDER BY io_id', [$it_id]);
}

function get_options_grouped(int $it_id): array
{
    $rows = get_product_options($it_id);
    $g = [];
    foreach ($rows as $r) $g[$r['io_type']][] = $r;
    return $g;
}

function get_best_products(int $limit = 8): array
{
    return db_all(
        "SELECT * FROM products WHERE it_use = 1 AND it_best = 1
         ORDER BY created_at DESC LIMIT $limit"
    );
}

function get_new_products(int $limit = 4): array
{
    return db_all(
        "SELECT * FROM products WHERE it_use = 1 AND it_new = 1
         ORDER BY created_at DESC LIMIT $limit"
    );
}

/**
 * 카테고리/정렬/페이지에 따른 상품 목록
 * @return [items, total]
 */
function list_products(?int $ca_id, string $sort, int $page, int $per_page): array
{
    $where  = ['p.it_use = 1'];
    $params = [];

    if ($ca_id) {
        $ids = descendant_category_ids($ca_id);
        $ph = implode(',', array_fill(0, count($ids), '?'));
        $where[] = "p.ca_id IN ($ph)";
        $params = array_merge($params, $ids);
    }

    $order = match ($sort) {
        'price_asc'  => 'COALESCE(NULLIF(p.it_sell_price,0), p.it_price) ASC',
        'price_desc' => 'COALESCE(NULLIF(p.it_sell_price,0), p.it_price) DESC',
        'new'        => 'p.created_at DESC, p.it_id DESC',
        default      => 'p.it_best DESC, p.created_at DESC',
    };

    $where_sql = implode(' AND ', $where);

    $total = (int)db_one(
        "SELECT COUNT(*) AS c FROM products p WHERE $where_sql",
        $params
    )['c'];

    $offset = max(0, ($page - 1) * $per_page);
    $items = db_all(
        "SELECT p.* FROM products p
         WHERE $where_sql
         ORDER BY $order
         LIMIT $per_page OFFSET $offset",
        $params
    );

    return [$items, $total];
}

/* ────────────────────────────────────────────────────────
   장바구니 (세션 기반)
─────────────────────────────────────────────────────────*/

function cart_init(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
}

function cart_count(): int
{
    cart_init();
    $c = 0;
    foreach ($_SESSION['cart'] as $item) $c += (int)$item['qty'];
    return $c;
}

function cart_items_detailed(): array
{
    cart_init();
    $out = [];
    foreach ($_SESSION['cart'] as $key => $item) {
        $p = get_product((int)$item['it_id']);
        if (!$p) continue;
        $base   = $p['it_sell_price'] > 0 ? $p['it_sell_price'] : $p['it_price'];
        $unit   = $base + (int)($item['option_add'] ?? 0);
        $out[] = [
            'key'         => $key,
            'product'     => $p,
            'qty'         => (int)$item['qty'],
            'option_text' => $item['option_text'] ?? '',
            'option_add'  => (int)($item['option_add'] ?? 0),
            'unit_price'  => $unit,
            'subtotal'    => $unit * (int)$item['qty'],
        ];
    }
    return $out;
}

function cart_total(): int
{
    $sum = 0;
    foreach (cart_items_detailed() as $i) $sum += $i['subtotal'];
    return $sum;
}

/* ────────────────────────────────────────────────────────
   기타
─────────────────────────────────────────────────────────*/

function url(string $path = '', array $query = []): string
{
    $u = '/' . ltrim($path, '/');
    if ($query) $u .= '?' . http_build_query($query);
    return $u;
}

function current_url_with(array $override): string
{
    $q = array_merge($_GET, $override);
    return strtok($_SERVER['REQUEST_URI'], '?') . '?' . http_build_query($q);
}

function pagination_links(int $current, int $total, int $per_page): array
{
    $pages = (int)max(1, ceil($total / $per_page));
    $start = max(1, $current - 2);
    $end   = min($pages, $current + 2);
    return ['pages' => $pages, 'start' => $start, 'end' => $end];
}
