<?php
/**
 * 테마 로더
 *
 * 테마 = themes/{name}/ 폴더. 각 테마는 다음 파일 보유:
 *   theme.php        — 메타(이름·설명) + 색상 팔레트
 *   header.php       — PC 헤더 마크업
 *   home.php         — 홈페이지 본문 레이아웃
 *   product_card.php — 상품 카드
 *
 * 데이터·기능(DB·장바구니·회원·어드민)은 전 테마 공유.
 * 모바일 셸(하단 탭바 등)도 공유하되 테마 색상은 입혀짐.
 *
 * 테마 전환: includes/config.php 의 'theme' 값 한 줄.
 */

function themes_dir(): string
{
    return dirname(__DIR__) . '/themes';
}

/** 현재 활성 테마명 (폴더 없으면 classic 폴백) */
function active_theme(): string
{
    static $t = null;
    if ($t !== null) return $t;
    $cfg = require __DIR__ . '/config.php';
    $t = $cfg['theme'] ?? 'classic';
    if (!is_dir(themes_dir() . '/' . $t)) $t = 'classic';
    return $t;
}

/** 테마 내 파일의 절대경로 */
function theme_file(string $file): string
{
    return themes_dir() . '/' . active_theme() . '/' . $file;
}

/** 테마 메타/색상 (theme.php 반환값) */
function theme_meta(): array
{
    static $m = null;
    if ($m !== null) return $m;
    $f = theme_file('theme.php');
    $m = is_file($f) ? (require $f) : [];
    return $m;
}

/**
 * 테마 색상 팔레트 → Tailwind config용 colors 객체.
 * theme.php가 colors를 안 주면 classic 기본값.
 */
function theme_colors(): array
{
    $m = theme_meta();
    return $m['colors'] ?? [
        'primary' => ['DEFAULT' => '#0A2540', 'light' => '#1B3A5C', 'dark' => '#061829'],
        'accent'  => ['DEFAULT' => '#FFC107', 'light' => '#FFD54F', 'dark' => '#FFA000'],
    ];
}

/** 테마 폰트 (Pretendard 기본) */
function theme_font(): string
{
    return theme_meta()['font'] ?? 'Pretendard';
}

/** 등록된 모든 테마 목록 (어드민 전환 UI용) */
function all_themes(): array
{
    $out = [];
    foreach (glob(themes_dir() . '/*', GLOB_ONLYDIR) as $dir) {
        $name = basename($dir);
        $meta = is_file("$dir/theme.php") ? (require "$dir/theme.php") : [];
        $out[$name] = $meta['name'] ?? $name;
    }
    return $out;
}
