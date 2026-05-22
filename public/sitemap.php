<?php
/**
 * sitemap.xml 동적 생성 (.htaccess가 /sitemap.xml → 여기로 매핑)
 * 상품·블로그·납품사례·카테고리를 자동 수집.
 */
require_once __DIR__ . '/../includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$origin = site_origin();
$urls = [];

// 정적 주요 페이지
$urls[] = ['loc' => "$origin/",               'changefreq' => 'daily',   'priority' => '1.0'];
$urls[] = ['loc' => "$origin/shop/list.php",   'changefreq' => 'daily',   'priority' => '0.9'];
$urls[] = ['loc' => "$origin/blog/list.php",   'changefreq' => 'weekly',  'priority' => '0.7'];
$urls[] = ['loc' => "$origin/cases/list.php",  'changefreq' => 'weekly',  'priority' => '0.7'];
$urls[] = ['loc' => "$origin/faq.php",         'changefreq' => 'monthly', 'priority' => '0.5'];

// 카테고리
foreach (get_categories() as $c) {
    $urls[] = [
        'loc'        => "$origin/shop/list.php?ca_id=" . (int)$c['ca_id'],
        'changefreq' => 'weekly',
        'priority'   => '0.8',
    ];
}

// 상품
foreach (db_all('SELECT it_id, created_at FROM products WHERE it_use = 1') as $p) {
    $urls[] = [
        'loc'        => "$origin/shop/item.php?it_id=" . (int)$p['it_id'],
        'lastmod'    => date('Y-m-d', strtotime($p['created_at'])),
        'changefreq' => 'weekly',
        'priority'   => '0.8',
    ];
}

// 블로그
foreach (db_all('SELECT id, updated_at FROM posts WHERE published = 1') as $p) {
    $urls[] = [
        'loc'        => "$origin/blog/view.php?id=" . (int)$p['id'],
        'lastmod'    => date('Y-m-d', strtotime($p['updated_at'])),
        'changefreq' => 'monthly',
        'priority'   => '0.6',
    ];
}

// 납품사례
foreach (db_all('SELECT id, updated_at FROM case_studies WHERE published = 1') as $c) {
    $urls[] = [
        'loc'        => "$origin/cases/view.php?id=" . (int)$c['id'],
        'lastmod'    => date('Y-m-d', strtotime($c['updated_at'])),
        'changefreq' => 'monthly',
        'priority'   => '0.6',
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
echo '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
foreach ($urls as $u) {
    echo "  <url>\n";
    echo '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1, 'UTF-8') . "</loc>\n";
    if (!empty($u['lastmod'])) echo '    <lastmod>' . $u['lastmod'] . "</lastmod>\n";
    echo '    <changefreq>' . $u['changefreq'] . "</changefreq>\n";
    echo '    <priority>' . $u['priority'] . "</priority>\n";
    echo "  </url>\n";
}
echo '</urlset>';
