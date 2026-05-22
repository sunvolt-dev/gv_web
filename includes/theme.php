<?php
/**
 * 테마 로더
 *
 * 테마 = themes/{name}/ 폴더: theme.php(색상)·header.php·home.php·product_card.php
 * 데이터·기능·모바일셸·푸터는 전 테마 공유.
 *
 * 테마 전환: config.php 'theme' 값 또는 어드민 → 테마 설정.
 * 색상 커스텀: themes/color_overrides.json (어드민에서 편집).
 * 미리보기: ?preview_theme=name (적용 없이 해당 테마로 1회 렌더).
 */

function themes_dir(): string
{
    return dirname(__DIR__) . '/themes';
}

/** 현재 활성 테마명 (미리보기 > config > classic 폴백) */
function active_theme(): string
{
    static $t = null;
    if ($t !== null) return $t;

    // 미리보기 우선
    if (!empty($_GET['preview_theme'])) {
        $pv = preg_replace('/[^a-z]/', '', (string)$_GET['preview_theme']);
        if ($pv !== '' && is_dir(themes_dir() . '/' . $pv)) {
            return $t = $pv;
        }
    }
    $cfg = require __DIR__ . '/config.php';
    $t = $cfg['theme'] ?? 'classic';
    if (!is_dir(themes_dir() . '/' . $t)) $t = 'classic';
    return $t;
}

/** 테마 내 파일 절대경로 */
function theme_file(string $file): string
{
    return themes_dir() . '/' . active_theme() . '/' . $file;
}

/** 테마 메타/색상 (theme.php 반환값) */
function theme_meta(): array
{
    static $cache = [];
    $t = active_theme();
    if (isset($cache[$t])) return $cache[$t];
    $f = themes_dir() . '/' . $t . '/theme.php';
    return $cache[$t] = (is_file($f) ? (require $f) : []);
}

/* ── 색상 ────────────────────────────────────────────── */

/** 색상 오버라이드 파일 내용 (테마명 => ['primary'=>hex,'accent'=>hex]) */
function color_overrides(): array
{
    $f = themes_dir() . '/color_overrides.json';
    if (!is_file($f)) return [];
    $j = json_decode((string)file_get_contents($f), true);
    return is_array($j) ? $j : [];
}

/** hex 밝기 조절 — pct>0 밝게, pct<0 어둡게 */
function adjust_brightness(string $hex, int $pct): string
{
    $hex = ltrim(trim($hex), '#');
    if (strlen($hex) === 3) $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
    if (strlen($hex) !== 6) return '#' . $hex;
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $adj = function ($c) use ($pct) {
        $delta = ($pct / 100) * ($pct > 0 ? (255 - $c) : $c);
        return max(0, min(255, (int)round($c + $delta)));
    };
    return sprintf('#%02X%02X%02X', $adj($r), $adj($g), $adj($b));
}

/** 단일 hex → DEFAULT/light/dark 3단 팔레트 */
function shade_set(string $hex): array
{
    return [
        'DEFAULT' => '#' . ltrim(trim($hex), '#'),
        'light'   => adjust_brightness($hex, 20),
        'dark'    => adjust_brightness($hex, -20),
    ];
}

/**
 * 활성 테마의 색상 팔레트 (Tailwind colors용).
 * theme.php 기본값에 color_overrides.json 적용.
 */
function theme_colors(): array
{
    $m = theme_meta();
    $colors = $m['colors'] ?? [
        'primary' => ['DEFAULT' => '#0A2540', 'light' => '#1B3A5C', 'dark' => '#061829'],
        'accent'  => ['DEFAULT' => '#FFC107', 'light' => '#FFD54F', 'dark' => '#FFA000'],
    ];
    $ov = color_overrides()[active_theme()] ?? null;
    if ($ov) {
        if (!empty($ov['primary'])) $colors['primary'] = shade_set($ov['primary']);
        if (!empty($ov['accent']))  $colors['accent']  = shade_set($ov['accent']);
    }
    return $colors;
}

/** 테마 폰트 */
function theme_font(): string
{
    return theme_meta()['font'] ?? 'Pretendard';
}

/** 등록된 모든 테마 (name => 표시명) */
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
